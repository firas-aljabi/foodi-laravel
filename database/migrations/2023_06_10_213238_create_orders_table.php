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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status',[Status::Preparing,Status::Done])->default(Status::Preparing);
            $table->time('time');//time_preparing
            $table->time('time_end')->nullable();//time_done
            $table->string('table_num')->default('sss');
            $table->double('total_price')->nullable();
            $table->integer('tax')->default(5);
            $table->boolean('is_paid')->default('0');
            $table->bigInteger('branch_id')->unsigned();
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
        Schema::dropIfExists('orders');
    }
};
class Status
{
	const Preparing    = "Preparing";
	const Done  = "Done";
}


