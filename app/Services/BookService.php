<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookLoan;
use App\Models\BookLoanRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Gate;

/**
 * Class BookService.
 */
class BookService
{

    public function addBook($request)
    {
        Book::create([
            'name' => $request->name ?? null,
            'publisher' => $request->publisher ?? null,
            'isbn' => $request->isbn ?? null,
            'genre_id' => $request->genre_id ?? null,
            'sub_category_id' => $request->subject_category_id ?? null,
            'description' => $request->description ?? null,
            'pages' => $request->pages ?? null,
            'author_id' => $request->author_id ?? null
        ]);
    }

    public function applyBookLoan($book_id)
    {
        $time = Carbon::now();
        //Check Book Availability status
        $book = Book::find($book_id);
        if (!$book) {
            $message = 'The Requested Book Is Unavailable in our Library Database';
            $response = [
                'message' => $message,
                'status' => 404,
            ];
            return $response;
        }
        $bookStatus = BookLoan::where('book_id', $book->id)->where('status','=', 'loan_application')->first();

        //Add To Loan Requests Table
        if ($bookStatus == null) {
            DB::beginTransaction();

            try {
                BookLoanRequest::create([
                    'user_id' => Auth::id(),
                    'book_id' => $book->id,
                    'status' => 'pending',
                ]);

                BookLoan::create([
                    'user_id' => request()->user()->id,
                    'book_id' => $book->id,
                    'loan_date' => $time->format('Y-m-d'),
                    'return_date' => request()->due_date,
                    'status' => 'loan_application'
                ]);
                $message = 'Book Loan Request Logged Successfully';
                $response = [
                    'message' => $message,
                    'status' => 200,
                ];
                return $response;

                //Send Notification To User and Admin Here

                DB::commit();
                // all good
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
            }

            // Return Response


        }

        $message = 'Requested Book Loan has already been logged';
        $response = [
            'message' => $message,
            'status' => 200,
        ];
        return $response;
    }



    public function issueBook($book_id)
    {
        $time = Carbon::now();
        //Check Book Availability status
        $book = Book::find($book_id);
        if (!$book) {
            $message = 'The Requested Book Is Unavailable in our Library Database';
            $response = [
                'message' => $message,
                'status' => 404,
            ];
            return $response;
        }
        $bookStatus = BookLoan::where('book_id', $book->id)->where('status', 'on_loan')->first();
        // Issue Book
        if ($bookStatus) {
            $message = 'The Requested Book Is Currently On Loan. Kindly Check in later days';
            $response = [
                'message' => $message,
                'status' => 200,
            ];
            return $response;
        }
        //Add to loans
        $loanRequest = BookLoanRequest::where('book_id', $book_id)->where('user_id', request()->user_id)->first();

        if ($loanRequest) {
            DB::beginTransaction();
            try {
                // Update Loan Request

                if (request()->approve_book_loan == 'approve') {

                    $loanRequest->update([
                        'status' => 'approved',
                    ]);

                    $bookLoan = BookLoan::where('book_id', $book_id)->where('user_id', request()->user_id)->latest();

                    $bookLoan->update([
                        'penalty_amount'  => request()->penalty_amount,
                        'status' => 'on_loan',
                        'added_by' => Auth::id(),
                    ]);
                    //Send Approvel Notification to User and Book loan Details

                    //return response
                    $message = 'Application Is Approved Successfully';
                    $response = [
                        'message' => $message,
                        'status' => 200,
                    ];
                    return $response;
                }
                //Update Request Rejected
                $loanRequest->update([
                    'status' => 'rejected',
                ]);

                //Update the requests
                $message = 'Application Is Rejected Successfully';
                $response = [
                    'message' => $message,
                    'status' => 200,
                ];
                return $response;

                DB::commit();
                // all good
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
            }
        } else {

            $message = 'No Loan Request Available for the User with the Book';
            $response = [
                'message' => $message,
                'status' => 404,
            ];
            return $response;
        }
    }

    public function returnBook($book_id)
    {
        $time = Carbon::now();
        //Check Book Availability status
        $book = Book::findOrFail($book_id);
        if (!$book) {
            $message = 'The Requested Book Is Unavailable in our Library Database';
            $response = [
                'message' => $message,
                'status' => 404,
            ];
            return $response;
        }
        $bookStatus = BookLoan::where('book_id', $book->id)->where('status', 'on_loan')->where('user_id', request()->user_id)->first();
        // Issue Book
        if (!$bookStatus) {
            $message = 'The Requested Book Is not Currently On Loan by the User. Kindly Check for another book';
            $response = [
                'message' => $message,
                'status' => 404,
            ];
            return $response;
        }

        $bookStatus->update([
            'return_date' => request()->return_date ?? $time->format('Y-m-d'),
            'status' => 'available',
        ]);

        return response()->json([
            'message' => 'Book Returned and Updated Successfully'
        ], 200);
    }
}
