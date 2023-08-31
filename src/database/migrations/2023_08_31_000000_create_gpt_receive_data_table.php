<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGptReceiveDataTable extends Migration
{
    public function up(): void
    {
        Schema::create('gpt_receive_data', function (Blueprint $table) {
            $table->id();
            $table->jsonb('data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gpt_receive_data');
    }
}
