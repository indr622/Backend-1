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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->text('label');
            $table->text('menu_id')->unique();
            $table->text('icon')->nullable();
            $table->integer('sort');
            $table->text('router')->nullable();
            $table->bigInteger('menu_parent_id')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('module')->nullable();
            $table->text('sub_module')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
