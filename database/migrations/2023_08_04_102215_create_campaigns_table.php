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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('blast_name');
            $table->string('from_number');
            $table->text('message');
            $table->boolean('is_schedule')->default(0);
            $table->dateTime('schedule_date')->nullable();
            $table->timestamp('converted_date')->nullable();
            $table->string('timezone')->nullable();
            $table->string('csv_file')->nullable();
            $table->enum('status', ['delivered', 'pending', 'undelivered'])->default('pending');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
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
        Schema::dropIfExists('campaigns');
    }
};
