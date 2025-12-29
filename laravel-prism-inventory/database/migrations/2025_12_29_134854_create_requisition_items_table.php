<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisition_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('requisition_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity'); // requested quantity (>0)
            $table->timestamps();

            $table->index(['requisition_id']);
            $table->index(['product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_items');
    }
};