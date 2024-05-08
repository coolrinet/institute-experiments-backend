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
        Schema::create('experiment_quantitative_inputs', function (Blueprint $table) {
            $table->foreignId('experiment_id')->constrained('experiments');
            $table->foreignId('parameter_id')->constrained('machinery_parameters');
            $table->decimal('value');

            $table->primary(['experiment_id', 'parameter_id']);
        });

        Schema::create('experiment_quality_inputs', function (Blueprint $table) {
            $table->foreignId('experiment_id')->constrained('experiments');
            $table->foreignId('parameter_id')->constrained('machinery_parameters');
            $table->string('value');

            $table->primary(['experiment_id', 'parameter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiment_quantitative_inputs');
        Schema::dropIfExists('experiment_quality_inputs');
    }
};
