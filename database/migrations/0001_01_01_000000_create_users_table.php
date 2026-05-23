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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->string('email')->unique();
            $table->string('password_hash')->nullable();
            $table->enum('provider', ['local', 'google'])->default('local');
            $table->string('provider_id')->nullable();
            $table->string('avatar_url', 500)->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('role', ['client', 'employee', 'admin'])->default('client')->index();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
