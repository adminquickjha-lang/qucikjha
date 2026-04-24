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
        Schema::create('safety_documents', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('project_name');
            $table->string('project_location');
            $table->text('project_description');
            $table->text('equipment_tools');
            $table->string('competent_person')->nullable();
            $table->string('safety_coordinator')->nullable();
            $table->json('regulations')->nullable();
            $table->string('document_type'); // JHA, AHA, JSA
            $table->json('ai_response')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('download_ready')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_documents');
    }
};
