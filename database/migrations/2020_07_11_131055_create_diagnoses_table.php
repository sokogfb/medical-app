<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagnosesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('entry_id');
            $table->uuid('symptom_id');
            $table->integer('issueID');
            $table->string('name');
            $table->longText('issueDescription');
            $table->double('accuracy')->default(0.00);
            $table->json('diagnosis');
            $table->boolean('is_valid')->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('diagnoses');
    }
}
