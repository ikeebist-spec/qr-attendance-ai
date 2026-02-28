<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->unsignedBigInteger('event_id');
            $table->string('student_name');
            $table->string('year_and_section');
            $table->string('scanned_at');
            $table->timestamps();

            $table->unique(['student_id', 'event_id']); // prevent duplicate scans
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
