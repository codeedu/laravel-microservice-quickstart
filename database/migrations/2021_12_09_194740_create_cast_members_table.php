<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastMembersTable extends Migration
{

    public function up()
    {
        Schema::create('cast_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->smallInteger('type')->min(1)->max(2);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cast_members');
    }
}
