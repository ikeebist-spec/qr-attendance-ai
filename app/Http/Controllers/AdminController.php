<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Event;
use App\Models\YearAndSection;
use App\Models\AttendanceLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Routing\Controllers\HasMiddleware;

class AdminController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                if (!in_array($request->method(), ['GET', 'HEAD']) && $request->route()->getActionMethod() !== 'scanMasterlistPhoto') {
                    if (auth()->check() && auth()->user()->role !== 'super_admin') {
                        return response()->json(['error' => 'Unauthorized. Super Admin access required.'], 403);
                    }
                }
                return $next($request);
            }
        ];
    }

    // ─── STUDENTS ─────────────────────────────────────────────────────────────

    public function students()
    {
        $students = Student::orderBy('created_at', 'desc')->get();
        $totalEvents = Event::count();
        $totalEventFines = Event::sum('fine');

        $attendanceLogs = AttendanceLog::all()->groupBy('student_id');

        foreach ($students as $student) {
            $studentLogs = $attendanceLogs[$student->student_id] ?? collect();
            $presentEventIds = $studentLogs->pluck('event_id')->unique()->toArray();
            
            // --- DECISION TREE FINE COMPUTATION ---
            $allEvents = Event::orderBy('date', 'asc')->get();
            $computedFine = 0;
            $absenceSequence = 0;
            $mandatoryMissed = 0;

            foreach ($allEvents as $event) {
                if (!in_array($event->id, $presentEventIds)) {
                    $absenceSequence++;
                    $multiplier = 1.0;

                    // Branch 1: Event Type Weighting
                    if ($event->type === 'Mandatory') {
                        $multiplier = 1.0;
                        $mandatoryMissed++;
                    } elseif ($event->type === 'Major') {
                        $multiplier = 0.8;
                    } else {
                        $multiplier = 0.5;
                    }

                    // Branch 2: Escalation Logic (The more absences, the higher the weight)
                    if ($absenceSequence >= 2) $multiplier += 0.2;
                    if ($absenceSequence >= 4) $multiplier += 0.3;

                    $computedFine += ($event->fine * $multiplier);
                }
            }

            $student->absences = $absenceSequence;
            $student->fine = round($computedFine, 2);
            
            // --- DECISION TREE RISK ASSESSMENT ---
            $riskScore = ($student->absences * 10) + ($mandatoryMissed * 15);
            $student->risk_level = $riskScore >= 40 ? 'High Risk' : ($riskScore >= 20 ? 'Warning' : 'Stable');
        }

        return response()->json($students);
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|unique:students,student_id',
            'name' => 'required|string',
            'year_and_section' => 'required|string',
        ]);

        $student = Student::create([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'year_and_section' => $request->year_and_section,
            'absences' => 0,
        ]);

        return response()->json($student, 201);
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return response()->json(['success' => true]);
    }

    // ─── EVENTS ───────────────────────────────────────────────────────────────

    public function events()
    {
        return response()->json(Event::orderBy('date', 'asc')->get());
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'month' => 'required|string',
            'type' => 'required|in:Mandatory,Major,Voluntary',
            'fine' => 'required|integer|min:20|max:50',
            'is_single_scan' => 'boolean',
            // Single Window
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            // Session windows
            'morn_in_start' => 'nullable|string',
            'morn_in_end' => 'nullable|string',
            'morn_out_start' => 'nullable|string',
            'morn_out_end' => 'nullable|string',
            'aft_in_start' => 'nullable|string',
            'aft_in_end' => 'nullable|string',
            'aft_out_start' => 'nullable|string',
            'aft_out_end' => 'nullable|string',
        ]);

        $event = Event::create([
            'name' => $request->name,
            'date' => $request->date,
            'month' => $request->month,
            'type' => $request->type,
            'fine' => $request->fine,
            'is_single_scan' => $request->is_single_scan ?? true,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'morn_in_start' => $request->morn_in_start,
            'morn_in_end' => $request->morn_in_end,
            'morn_out_start' => $request->morn_out_start,
            'morn_out_end' => $request->morn_out_end,
            'aft_in_start' => $request->aft_in_start,
            'aft_in_end' => $request->aft_in_end,
            'aft_out_start' => $request->aft_out_start,
            'aft_out_end' => $request->aft_out_end,
        ]);

        return response()->json($event, 201);
    }

    public function deleteEvent($id)
    {
        $event = Event::findOrFail($id);
        AttendanceLog::where('event_id', $id)->delete();
        $event->delete();
        return response()->json(['success' => true]);
    }

    // ─── YEAR AND SECTIONS ─────────────────────────────────────────────────────────────

    public function yearAndSections()
    {
        $yearAndSections = YearAndSection::orderBy('name')->pluck('name');

        return response()->json($yearAndSections);
    }

    public function storeYearAndSection(Request $request)
    {
        $request->validate(['name' => 'required|unique:year_and_sections,name']);
        $yearAndSection = YearAndSection::create(['name' => strtoupper($request->name)]);
        return response()->json($yearAndSection, 201);
    }

    public function deleteYearAndSection($name)
    {
        $hasStudents = Student::where('year_and_section', $name)->exists();
        if ($hasStudents) {
            return response()->json(['error' => "Cannot delete {$name}: Students are assigned to this year and section."], 422);
        }
        YearAndSection::where('name', $name)->delete();
        return response()->json(['success' => true]);
    }

    // ─── ATTENDANCE ───────────────────────────────────────────────────────────

    public function attendance(Request $request)
    {
        $logs = AttendanceLog::when($request->event_id, fn($q) => $q->where('event_id', $request->event_id))
            ->when($request->month, function ($q) use ($request) {
                // If filtering by month, we need to join with events or find event IDs for that month
                $eventIds = Event::where('month', $request->month)->pluck('id');
                return $q->whereIn('event_id', $eventIds);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($logs);
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'event_id' => 'required|integer',
            'student_name' => 'required|string',
            'year_and_section' => 'required|string',
            'log_type' => 'nullable|string',
            'scanned_at' => 'nullable|string',
        ]);

        $logType = $request->log_type ?? 'Morning In';

        // Check for duplicate
        $exists = AttendanceLog::where('student_id', $request->student_id)
            ->where('event_id', $request->event_id)
            ->where('log_type', $logType)
            ->exists();

        if ($exists) {
            return response()->json(['error' => "Student already logged for {$logType} in this event."], 409);
        }

        $log = AttendanceLog::create([
            'student_id' => $request->student_id,
            'event_id' => $request->event_id,
            'student_name' => $request->student_name,
            'year_and_section' => $request->year_and_section,
            'log_type' => $logType,
            'scanned_at' => $request->scanned_at ?? now()->format('h:i A'),
        ]);

        return response()->json($log, 201);
    }

    // ─── ACTIVITY LOGS ────────────────────────────────────────────────────────

    public function logs()
    {
        return response()->json(ActivityLog::orderBy('created_at', 'desc')->limit(100)->get());
    }

    public function storeLog(Request $request)
    {
        $request->validate(['action' => 'required|string']);
        $log = ActivityLog::create([
            'user' => $request->user ?? 'System',
            'action' => $request->action,
        ]);
        return response()->json($log, 201);
    }

    // ─── DASHBOARD ────────────────────────────────────────────────────────────

    public function dashboardData(Request $request)
    {
        $eventId = $request->event_id;
        $presentCount = $eventId ? AttendanceLog::where('event_id', $eventId)->distinct('student_id')->count('student_id') : 0;
        $totalStudents = Student::count();

        // Dynamic AI Fine Computation
        $students = Student::all();
        $totalEvents = Event::count();
        $totalFines = 0;
        $atRisk = 0;

        if ($totalEvents > 0) {
            $allEvents = Event::orderBy('date', 'asc')->get();
            $logs = AttendanceLog::all()->groupBy('student_id');

            foreach ($students as $s) {
                $studentLogs = $logs[$s->student_id] ?? collect();
                $presentEventIds = $studentLogs->pluck('event_id')->toArray();

                // --- DECISION TREE RE-CALCULATION (FOR ACCURACY) ---
                $computedFine = 0;
                $absenceSequence = 0;
                $mandatoryMissed = 0;

                foreach ($allEvents as $event) {
                    if (!in_array($event->id, $presentEventIds)) {
                        $absenceSequence++;
                        $multiplier = 1.0;
                        if ($event->type === 'Mandatory') { $multiplier = 1.0; $mandatoryMissed++; }
                        elseif ($event->type === 'Major') { $multiplier = 0.8; }
                        else { $multiplier = 0.5; }

                        if ($absenceSequence >= 2) $multiplier += 0.2;
                        $computedFine += ($event->fine * $multiplier);
                    }
                }

                $s->absences = $absenceSequence;
                $s->fine = round($computedFine, 2);
                $totalFines += $s->fine;

                $riskScore = ($s->absences * 10) + ($mandatoryMissed * 15);
                if ($riskScore >= 40) {
                    $atRisk++;
                }
            }
        }

        return response()->json([
            'present' => $presentCount,
            'total_students' => $totalStudents,
            'total_fines' => $totalFines,
            'at_risk' => $atRisk,
        ]);
    }

    // ─── AI MASTERLIST PHOTO SCANNER ─────────────────────────────────────────

    public function scanMasterlistPhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|max:10240']);

        $apiKey = env('GEMINI_API_KEY');
        $imageData = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        $mimeType = $request->file('photo')->getMimeType();

        $prompt = 'Look at this image carefully. It may be a class list, enrollment form, or any document containing student information. '
            . 'Find ALL names and student ID numbers visible. '
            . 'Return ONLY a valid JSON array — no markdown, no explanation — in exactly this format: '
            . '[{"student_id":"2024-001","name":"Dela Cruz, Juan A."},{"student_id":"2024-002","name":"Santos, Maria B."}] '
            . 'Rules: '
            . '(1) If a student ID number is not visible, use "TBD" as the student_id. '
            . '(2) Write names as shown in the document. '
            . '(3) Return ONLY the JSON array. Nothing else.';

        try {
            $response = Http::withoutVerifying()
                ->withOptions(['timeout' => 30])
                ->post(
                    "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash-lite:generateContent?key={$apiKey}",
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                    ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageData]],
                                ]
                            ]
                        ],
                        'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 2048],
                    ]
                );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection to AI failed: ' . $e->getMessage()], 500);
        }

        if (!$response->ok()) {
            $body = $response->json();
            $msg = $body['error']['message'] ?? $response->body();
            return response()->json(['error' => 'Gemini API error: ' . $msg], 500);
        }

        $text = $response->json('candidates.0.content.parts.0.text') ?? '';
        // Strip markdown fences if present
        $text = trim(preg_replace('/^```(?:json)?\n?|\n?```$/m', '', trim($text)));

        $students = json_decode($text, true);
        if (!is_array($students) || count($students) === 0) {
            return response()->json(['error' => 'No student data found in the image. Please try a clearer photo.'], 422);
        }

        return response()->json(['students' => $students]);
    }

    public function storeBatchStudents(Request $request)
    {
        $request->validate([
            'students' => 'required|array',
            'students.*.student_id' => 'required|string',
            'students.*.name' => 'required|string',
            'students.*.year_and_section' => 'required|string',
        ]);

        $addedCount = 0;
        $skipped = [];

        foreach ($request->students as $s) {
            // Auto-create section if missing
            $secName = strtoupper(trim($s['year_and_section']));
            if (!empty($secName)) {
                YearAndSection::firstOrCreate(['name' => $secName]);
            }

            $exists = Student::where('student_id', $s['student_id'])->exists();
            if ($exists) {
                $skipped[] = $s['student_id'];
                continue;
            }

            Student::create([
                'student_id' => $s['student_id'],
                'name' => $s['name'],
                'year_and_section' => $secName,
                'absences' => 0,
            ]);
            $addedCount++;
        }

        return response()->json([
            'message' => 'Batch processing complete.',
            'count' => $addedCount,
            'skipped_count' => count($skipped),
            'skipped_ids' => $skipped
        ]);
    }

    // ─── CHATBOT AI SUPPORT ──────────────────────────────────────────────────

    public function askChatbot(Request $request)
    {
        $request->validate(['message' => 'required|string', 'context' => 'required|string']);

        $totalStudents = Student::count();
        $totalEventsCount = Event::count();

        $eventsList = Event::select('name', 'date', 'type', 'fine')->get()->map(function ($ev) {
            return "{$ev->name} ({$ev->date}, {$ev->type}, Fine: ₱{$ev->fine})";
        })->implode('; ');

        $studentsList = Student::select('student_id', 'name', 'year_and_section')->take(60)->get()->map(function ($s) {
            return "{$s->name} (ID: {$s->student_id}, Section: {$s->year_and_section})";
        })->implode('; ');

        $apiKey = env('GEMINI_API_KEY');
        $prompt = "You are the AI Assistant for the ESSU CCS Attendance System. Answer the user's question concisely, perfectly, and accurately based ONLY on the following System Data.\n\n--- SYSTEM DATA ---\nFrontend Summary Context: {$request->context}\nTotal Students in Masterlist: {$totalStudents}\nTotal Events Recorded: {$totalEventsCount}\nDetailed Event List: {$eventsList}\nStudent List (up to 60): {$studentsList}\n-------------------\n\nUser Question: {$request->message}\n\nIf the user asks about specific numbers (like 'how many students' or 'how many events'), use the exact numbers provided in the System Data above. Start your answer directly, be helpful and professional:";

        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 500],
        ];

        $models = ['gemini-2.0-flash', 'gemini-2.0-flash-lite'];
        $response = null;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $model = $models[$attempt % count($models)];
            try {
                $response = Http::withoutVerifying()
                    ->withOptions(['timeout' => 30])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", $payload);

                if ($response->ok())
                    break;
                if ($response->status() === 429 && $attempt < 2) {
                    sleep(5);
                    continue;
                }
            } catch (\Exception $e) {
                if ($attempt >= 2)
                    return response()->json(['reply' => 'Connection to AI failed. Please try again later.']);
                sleep(3);
            }
        }

        if (!$response || !$response->ok()) {
            \Illuminate\Support\Facades\Log::error('Gemini API Error: ' . ($response ? $response->body() : 'No response'));
            return response()->json(['reply' => 'The AI is temporarily unavailable due to high usage. Please wait a moment and try again.']);
        }

        $text = $response->json('candidates.0.content.parts.0.text') ?? 'I am sorry, I am unable to process that right now.';
        return response()->json(['reply' => $text]);
    }

    // ─── ADMIN ACCOUNTS MANAGEMENT ──────────────────────────────────────────

    public function getAdmins(Request $request)
    {
        // Only allow super admin to view this list theoretically, but dashboard UI will handle it
        $admins = \App\Models\User::orderBy('id', 'asc')->get(['id', 'name', 'username', 'role']);
        return response()->json($admins);
    }

    public function updateAdmin(Request $request, $id)
    {
        $admin = \App\Models\User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $admin->username = $request->username;
        if ($request->filled('password')) {
            $admin->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Admin account updated successfully.',
            'admin' => $admin
        ]);
    }
}
