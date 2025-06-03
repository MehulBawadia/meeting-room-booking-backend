<?php

use App\Models\Subscription;
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
        Schema::create('subscription_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->string('user_name');
            $table->string('user_email');
            $table->foreignIdFor(Subscription::class, 'subscription_id')->constrained();
            $table->string('subscription_name');
            $table->float('total_amount')->default(0.0);
            $table->unsignedInteger('payment_gateway_id')->default(0);
            $table->string('payment_type')->nullable();
            $table->string('payment_done_from')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('payment_gateway_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_orders');
    }
};
