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
        Schema::table('safety_documents', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('download_ready');
            $table->string('stripe_session_id')->nullable()->after('is_paid');
            $table->decimal('amount', 10, 2)->default(0.00)->after('stripe_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safety_documents', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'stripe_session_id', 'amount']);
        });
    }
};
