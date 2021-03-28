<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->date('date');
            $table->time('time');
            $table->text('description');
            $table->string('branch');
            $table->decimal('amount')->nullable();
            $table->decimal('tax');
            $table->integer('endorsed');
            $table->enum('category', ['operation', 'definitive', 'confirmed', 'declared']);
            // foreign keys
            $table->foreignId('instrument_id')->constrained();
            $table->foreignId('concept_dailies_id')->constrained();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('total_id')->nullable()->constrained();
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
        Schema::dropIfExists('transactions');
    }
}
