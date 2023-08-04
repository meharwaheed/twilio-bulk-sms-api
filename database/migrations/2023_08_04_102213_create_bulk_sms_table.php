<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_sms', function (Blueprint $table) {
            $table->id();
            $table->string('blast_name');
            $table->string('from_number');
            $table->text('message');
            $table->boolean('is_schedule')->default(0);
            $table->timestamp('schedule_date')->nullable();
            $table->boolean('status')->default(0);
            $table->string('csv_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bulk_sms');
    }
};
