<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('subjcode'); // para sa "Subject Code" field
            $table->string('day_time'); // Change the "Day/Time" to "day_time" to avoid SQL errors
            $table->string('professor'); // para sa "Professor" field 
            $table->timestamps(); // para sa created_at and updated_at fields
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};