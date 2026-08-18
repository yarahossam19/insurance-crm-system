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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('insurance_company_id')->constrained()->restrictOnDelete();
            $table->string('policy_number')->unique();
            $table->string('type')->default('other'); // App\Enums\PolicyType
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('premium_amount', 12, 2);
            $table->decimal('commission_rate', 5, 2)->default(0); // %
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('employee_commission_amount', 12, 2)->default(0);
            $table->decimal('net_office_commission', 12, 2)->default(0);
            $table->string('status')->default('active'); // App\Enums\PolicyStatus
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('documents')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
