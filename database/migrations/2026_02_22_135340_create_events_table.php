<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->string('type')->default('Mandatory'); // Mandatory, Major, Voluntary
            $table->integer('fine')->default(50); // Dynamic event fine (20-50 PHP)
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
