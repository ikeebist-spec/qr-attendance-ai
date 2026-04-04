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
        Schema::table('events', function (Blueprint $table) {
            $table->string('morn_in_start')->nullable();
            $table->string('morn_in_end')->nullable();
            $table->string('morn_out_start')->nullable();
            $table->string('morn_out_end')->nullable();
            $table->string('aft_in_start')->nullable();
            $table->string('aft_in_end')->nullable();
            $table->string('aft_out_start')->nullable();
            $table->string('aft_out_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'morn_in_start', 'morn_in_end',
                'morn_out_start', 'morn_out_end',
                'aft_in_start', 'aft_in_end',
                'aft_out_start', 'aft_out_end'
            ]);
        });
    }
};
