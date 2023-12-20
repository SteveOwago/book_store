<?php

use App\Models\Author;
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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('publisher',50)->nullable();
            $table->string('isbn',50)->nullable();
            $table->string('genre_id');
            //$table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('sub_category_id')->nullable()->constrained();
            $table->text('description')->nullable();
            $table->integer('pages')->nullable();
            $table->foreignIdFor(User::class,'added_by')->nullable()->constrained();
            $table->foreignIdFor(Author::class,'author_id')->nullable()->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
