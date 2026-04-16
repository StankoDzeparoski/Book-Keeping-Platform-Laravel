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

//        class Equipment{
//	id;
//	brand; (name:string)
//	model; (product vin:string)
//	category; (Laptop,Computer,Peripherals,Ergonomics, :Enum)
//	cost; (price:int)
//	condition; (new, used, broken:Enum)
//	status; (Available, assigned, repair, lost:Enum)
//	acquisition date; (buy date)
//	loanDate; (:date)
//	loanExpireDate (:date);
//	storageLocation (:string)
//	Employee/Userid (Who it is assigned to)
//}
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model'); // VIN/product model
            $table->string('category');
            $table->integer('cost');
            $table->string('condition');
            $table->string('status')->nullable(); // Set by observer to AVAILABLE on creation
            $table->date('acquisition_date'); // purchase/buy date
            $table->date('loan_date')->nullable();
            $table->date('loan_expire_date')->nullable();
            $table->string('storage_location');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
