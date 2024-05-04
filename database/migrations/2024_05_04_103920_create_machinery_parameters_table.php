<?php

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
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
        Schema::create('machinery_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('parameter_type', MachineryParameterTypeEnum::values());
            $table->enum('value_type', MachineryParameterValueTypeEnum::values());
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('machinery_id')
                ->nullable()
                ->constrained('machineries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machinery_parameters');
    }
};
