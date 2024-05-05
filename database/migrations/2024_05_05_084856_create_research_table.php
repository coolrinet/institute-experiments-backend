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
        Schema::create('research', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->date('last_experiment_date')->nullable();
            $table->foreignId('machinery_id')->constrained('machineries');
            $table->foreignId('author_id')->constrained('users');
        });

        Schema::create('research_user', function (Blueprint $table) {
            $table->foreignId('research_id')->constrained('research');
            $table->foreignId('user_id')->constrained('users');

            $table->primary(['research_id', 'user_id']);
        });

        Schema::create('research_machinery_parameter', function (Blueprint $table) {
            $table->foreignId('research_id')->constrained('research');
            $table->foreignId('parameter_id')->constrained('machinery_parameters');

            $table->primary(['research_id', 'parameter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research');
        Schema::dropIfExists('research_user');
        Schema::dropIfExists('research_machinery_parameter');
    }
};
