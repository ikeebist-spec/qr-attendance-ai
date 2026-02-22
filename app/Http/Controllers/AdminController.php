<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Event;
use App\Models\Section;
use App\Models\AttendanceLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    // ─── STUDENTS ─────────────────────────────────────────────────────────────

    public function students()
    {
        return response()->json(Student::orderBy('created_at', 'desc')->get());
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|unique:students,student_id',
            'name' => 'required|string',
            'section' => 'required|string',
        ]);

        $student = Student::create([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'section' => $request->section,
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
            'type' => 'required|in:Mandatory,Major,Voluntary',
            'fine' => 'required|integer|min:20|max:50',
        ]);

        $event = Event::create($request->only('name', 'date', 'type', 'fine'));
        return response()->json($event, 201);
    }

    // ─── SECTIONS ─────────────────────────────────────────────────────────────

    public function sections()
    {
        return response()->json(Section::orderBy('name')->pluck('name'));
    }

    public function storeSection(Request $request)
    {
        $request->validate(['name' => 'required|unique:sections,name']);
        $section = Section::create(['name' => strtoupper($request->name)]);
        return response()->json($section, 201);
    }

    public function deleteSection($name)
    {
        $hasStudents = Student::where('section', $name)->exists();
        if ($hasStudents) {
            return response()->json(['error' => "Cannot delete {$name}: Students are assigned to this section."], 422);
        }
        Section::where('name', $name)->delete();
        return response()->json(['success' => true]);
    }

    // ─── ATTENDANCE ───────────────────────────────────────────────────────────

    public function attendance(Request $request)
    {
        $logs = AttendanceLog::when($request->event_id, fn($q) => $q->where('event_id', $request->event_id))
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
            'section' => 'required|string',
        ]);

        // Check for duplicate
        $exists = AttendanceLog::where('student_id', $request->student_id)
            ->where('event_id', $request->event_id)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Student already logged for this event.'], 409);
        }

        $log = AttendanceLog::create([
            'student_id' => $request->student_id,
            'event_id' => $request->event_id,
            'student_name' => $request->student_name,
            'section' => $request->section,
            'scanned_at' => now()->toTimeString(),
        ]);

        // Increment student absences tracking is handled via events not present count
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
        $presentCount = $eventId ?AttendanceLog::where('event_id', $eventId)->count() : 0;
        $totalStudents = Student::count();

        // Dynamic AI Fine Computation
        $students = Student::all();
        $totalEvents = Event::count();
        $totalFines = 0;
        $atRisk = 0;

        // If there are no events, no one owes fines
        if ($totalEvents > 0) {
            $events = Event::all();

            // To be perfectly accurate, we need to know exactly which events each student missed.
            // Since `absences` on the Student model is just a counter of HOW MANY they missed,
            // we will estimate the fine by multiplying their absence count by the average event fine.
            // (For exact mapping, the AI Analytics frontend does a deeper dive).
            $averageFine = $events->avg('fine') ?? 50;

            foreach ($students as $s) {
                if ($s->absences > 0) {
                    $totalFines += ($s->absences * $averageFine);
                }
                if ($s->absences >= 2) {
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
                'contents' => [[
                        'parts' => [
                            ['text' => $prompt],
                            ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageData]],
                        ]
                    ]],
                'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 2048],
            ]
            );
        }
        catch (\Exception $e) {
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
}
