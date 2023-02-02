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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 20);
            $table->decimal('expected_rate', 20, 4);
            $table->foreignId('user_id')
                ->index()
                ->constrained();

            $table->foreignId('wallet_id')
                ->index()
                ->constrained();
            $table->foreignId('destination_wallet_id')
                ->index()
                ->constrained('wallets');
            $table->timestamp('expired_at');
            $table->timestamp('exchanged_at')->nullable();
            $table->foreignId('rate_id')->nullable()->constrained();
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
        Schema::dropIfExists('exchanges');
    }
};
