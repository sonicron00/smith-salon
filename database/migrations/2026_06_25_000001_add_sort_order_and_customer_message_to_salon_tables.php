<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('services', 'sort_order')) {
            Schema::table('services', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('price_pence');
            });
        }

        if (! Schema::hasColumn('staff', 'sort_order')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('name');
            });
        }

        if (! Schema::hasColumn('appointments', 'customer_message')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->text('customer_message')->nullable()->after('customer_email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('appointments', 'customer_message')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('customer_message');
            });
        }

        if (Schema::hasColumn('staff', 'sort_order')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }

        if (Schema::hasColumn('services', 'sort_order')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};