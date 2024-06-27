<?php

use App\Models\User;
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
        Schema::create('membership_information', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignIdFor(User::class, 'user_id')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('hin_number')->unique()->nullable();
            $table->date('year')->nullable();
            $table->integer('loa')->nullable();
            $table->integer('beam')->nullable();
            $table->integer('draft')->nullable();
            $table->integer('nwa')->unique()->nullable();
            $table->date('nwa_expiry')->nullable();
            $table->integer('mmsi')->unique();
            $table->integer('call_sign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_information');
    }
};
