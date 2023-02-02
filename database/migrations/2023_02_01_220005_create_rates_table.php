<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_currency_id')
                ->index()
                ->constrained('currencies');
            $table->foreignId('to_currency_id')
                ->index()
                ->constrained('currencies');
            $table->decimal('rate', 20, 4);
            $table->timestamp('date');
            $table->unique(['from_currency_id', 'to_currency_id', 'date']);
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
        Schema::dropIfExists('rates');
    }
};
