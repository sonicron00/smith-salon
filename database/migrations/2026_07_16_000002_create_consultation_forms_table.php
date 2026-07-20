<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consultation_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('fields'); // Array of field definitions
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Link services to a consultation form
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('consultation_form_id')->nullable()->constrained('consultation_forms')->nullOnDelete();
        });

        // Store form responses against appointments
        Schema::create('consultation_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('consultation_form_id')->constrained('consultation_forms')->cascadeOnDelete();
            $table->json('answers');
            $table->timestamps();

            $table->unique('appointment_id'); // One response per appointment
        });

        // Customer records for targeted analysis
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 32)->unique();
            $table->string('email')->nullable();
            $table->json('notes')->nullable();
            $table->timestamps();
        });

        // Link appointments to customer records
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('service_id')->constrained('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::dropIfExists('customers');
        Schema::dropIfExists('consultation_responses');

        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['consultation_form_id']);
            $table->dropColumn('consultation_form_id');
        });

        Schema::dropIfExists('consultation_forms');
    }
};
