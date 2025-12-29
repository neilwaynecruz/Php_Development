<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 64)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        DB::table('settings')->insert([
            ['key' => 'brand_text',           'value' => 'PRISM', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'low_stock_threshold',  'value' => '10',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'show_total_to_user',   'value' => '1',     'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};