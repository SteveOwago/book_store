<?php

namespace App\Http\Controllers;

use App\Http\Resources\DataResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    public $booksService;

    public function __construct(BookService $bookService)
    {
        $this->booksService = $bookService;
    }
    public function index()
    {
        $data = Book::all();
        return new DataResource($data);
    }

    //Fetch A book
    public function show($id)
    {
        $book = Book::findOrFail();
        return new DataResource($book);
    }

    public function store(Request $request)
    {
        if (Gate::denies('admin_access')) {
            return response()->json([
                'message' => 'Unauthorized Access'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'publisher' => 'required|string',
            'isbn' => 'string',
            'genre_id' => 'integer',
            'subject_category_id' => 'integer',
            'pages' => 'integer',
            'author_id' => 'integer'
        ]);
        try {
            $this->booksService->addBook($request);
            return response()->json([
                'message' => 'Book Added Successfully',
            ], 200);
        } catch (\Throwable $th) {
            //Log Adding Book Error
            info("Error Adding Book" . $th->getMessage());
            //return  Add book failed Message response

            return response()->json([
                'message' => 'Failed to Add A Book',
            ], 422);
        }
    }

    public function issueBook($id)
    {
        if (Gate::denies('admin_access')) {
            return response()->json([
                'message' => 'Unauthorized Access'
            ], 403);
        }

        try {
            $responseApi = $this->booksService->applyBookLoan($id);

            return response()->json([
                'message' => $responseApi['message'],
            ], $responseApi['status']);
        } catch (\Throwable $th) {
            //Log Adding Book Error
            info("Error Adding Book: " . $th->getMessage());
            //return  Add book failed Message response

            return response()->json([
                'message' => 'Failed to Issue A Book',
            ], 422);
        }
    }

    public function applyBookLoan(){
        try {
            $responseApi = $this->booksService->applyBookLoan(request()->book_id);

            return response()->json([
                'message' => $responseApi['message'],
            ], $responseApi['status']);
        } catch (\Throwable $th) {
            //Log Adding Book Error
            info("Error Applying Book Loan: " . $th->getMessage());
            //return  Add book failed Message response

            return response()->json([
                'message' => 'Failed to Apply A Book Loan',
            ], 422);
        }
    }
}
