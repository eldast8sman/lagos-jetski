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
        Schema::create('user_memberships', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignIdFor(User::class, 'user_id')->onDelete('cascade');
            $table->id('membership_id');
            $table->double('amount')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('date_joined')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('membership_notes')->nullable();
            $table->boolean('active_diver')->default(false);
            $table->string('padi_level')->nullable();
            $table->string('padi_number')->nullable();
            $table->integer('referee1')->nullable();
            $table->integer('referee2')->nullable();
            $table->integer('referee3')->nullable();
            $table->integer('referee4')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_memberships');
    }
};
