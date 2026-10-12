<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_addon')->default(false)->after('active');
        });

        // Which add-on services are available for a given parent service
        Schema::create('service_addon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained('services')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['service_id', 'addon_id']);
        });

        // Which add-ons were actually selected for a booking
        Schema::create('appointment_addon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained('services')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_addon');
        Schema::dropIfExists('service_addon');

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('is_addon');
        });
    }
};
