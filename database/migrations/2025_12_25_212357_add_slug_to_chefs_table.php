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
        Schema::table('chefs', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });
        
        // Update existing chefs with slugs
        $chefs = \Modules\Website\app\Models\Chef::all();
        foreach($chefs as $chef) {
            $chef->slug = \Illuminate\Support\Str::slug($chef->name);
            $chef->save();
        }
        
        // Make slug unique after populating
        Schema::table('chefs', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chefs', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
