<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Map existing string values to integers first
        DB::statement("UPDATE professional_reviews SET progress = '1' WHERE progress = 'pending'");
        DB::statement("UPDATE professional_reviews SET progress = '2' WHERE progress = 'in progress'");
        DB::statement("UPDATE professional_reviews SET progress = '3' WHERE progress = 'completed'");
        DB::statement("UPDATE professional_reviews SET progress = '1' WHERE progress NOT IN ('1', '2', '3')");

        // PostgreSQL: drop default first, then cast, then restore
        DB::statement('ALTER TABLE professional_reviews ALTER COLUMN progress DROP DEFAULT');
        DB::statement('ALTER TABLE professional_reviews ALTER COLUMN progress DROP NOT NULL');
        DB::statement('ALTER TABLE professional_reviews ALTER COLUMN progress TYPE integer USING progress::integer');
        DB::statement('ALTER TABLE professional_reviews ALTER COLUMN progress SET NOT NULL');
        DB::statement('ALTER TABLE professional_reviews ALTER COLUMN progress SET DEFAULT 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_reviews', function (Blueprint $table) {
            $table->string('progress')->default('pending')->change();
        });
    }
};
