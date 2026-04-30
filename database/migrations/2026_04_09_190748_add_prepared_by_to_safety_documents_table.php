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
            $table->string('prepared_by')->nullable()->after('equipment_tools');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safety_documents', function (Blueprint $table) {
            $table->dropColumn('prepared_by');
        });
    }
};
