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
            $table->string('transaction_id')->nullable()->after('stripe_session_id');
        });

        Schema::table('professional_reviews', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('stripe_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('safety_documents', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
        });

        Schema::table('professional_reviews', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
        });
    }
};
