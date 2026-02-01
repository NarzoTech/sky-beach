<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update existing menu items to have available_in_website = 1 where NULL
     */
    public function up(): void
    {
        DB::table('menu_items')
            ->whereNull('available_in_website')
            ->update(['available_in_website' => 1]);

        DB::table('menu_items')
            ->whereNull('available_in_pos')
            ->update(['available_in_pos' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed - setting defaults is safe
    }
};
