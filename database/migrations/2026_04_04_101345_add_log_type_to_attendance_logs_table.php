<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->string('log_type')->default('Morning In')->after('year_and_section');
            
            // Drop old unique index
            $table->dropUnique(['student_id', 'event_id']);
            
            // Add new unique index
            $table->unique(['student_id', 'event_id', 'log_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'event_id', 'log_type']);
            $table->dropColumn('log_type');
            
            // Restore old unique index (Note: This may fail if there are now duplicate entries for different log_types)
            $table->unique(['student_id', 'event_id']);
        });
    }
};
