<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 64); // who requested
            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected',
                'fulfilled',
                'cancelled',
            ])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['username', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};