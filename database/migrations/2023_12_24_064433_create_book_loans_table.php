<?php

use App\Models\Book;
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
        Schema::create('book_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class,'user_id')->nullable()->constrained();
            $table->foreignIdFor(User::class,'added_by')->nullable()->constrained();
            $table->foreignIdFor(Book::class,'book_id')->nullable()->constrained();
            $table->date('loan_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('return_date')->nullable();
            $table->enum('extended',['true','false'])->default('false');
            $table->date('extension_date')->nullable();
            $table->enum('status',['available','on_loan','loan_application'])->default('available');
            $table->double('penalty_amount',8,2)->nullable();
            $table->enum('penalty_status',['0','1'])->default('0');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_loans');
    }
};
