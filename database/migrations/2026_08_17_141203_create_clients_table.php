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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('individual'); // App\Enums\ClientType
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('national_id')->nullable(); // for individuals
            $table->string('commercial_register')->nullable(); // for companies
            $table->string('pipeline_stage')->default('new_lead'); // App\Enums\PipelineStage
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->json('documents')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
