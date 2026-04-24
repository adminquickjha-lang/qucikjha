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
        DB::table('professional_reviews')->where('progress', 'pending')->update(['progress' => 1]);
        DB::table('professional_reviews')->where('progress', 'in progress')->update(['progress' => 2]);
        DB::table('professional_reviews')->where('progress', 'completed')->update(['progress' => 3]);

        // Ensure any other values are defaulted to 1
        DB::table('professional_reviews')->whereNotIn('progress', [1, 2, 3])->update(['progress' => 1]);

        DB::statement('ALTER TABLE professional_reviews ALTER COLUMN progress DROP DEFAULT');
        DB::statement('ALTER TABLE professional_reviews ALTER COLUMN progress TYPE integer USING progress::integer');
        DB::statement('ALTER TABLE professional_reviews ALTER COLUMN progress SET DEFAULT 1');
        DB::statement('ALTER TABLE professional_reviews ALTER COLUMN progress SET NOT NULL');
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
