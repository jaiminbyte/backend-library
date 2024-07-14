<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Booking;

class BookController extends Controller
{
    public function search_google_api(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'query' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $query = $request->input('query');
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => 40,
        ]);

        if ($response->successful()) {
            $data = [];
            $booksData = $response->json();
            foreach ($booksData['items'] as $key => $bookData) {
                $volumeInfo = $bookData['volumeInfo'];
                $data[] = array(
                    'isbn' => $volumeInfo['industryIdentifiers'][0]['identifier'] ?? NULL,
                    'title' => $volumeInfo['title'] ?? NULL,
                    'author' => $volumeInfo['authors'][0] ?? NULL,
                    'publisher' => $volumeInfo['publisher'] ?? NULL,
                    'year' => isset($volumeInfo['publishedDate']) ? substr($volumeInfo['publishedDate'], 0, 4) : NULL,
                    'genre' => 'Fiction',
                    'quantity' => rand(1, 100),
                    'image' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
                );
            }
            return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
        } else {
            return response()->json(['error' => 'Failed to fetch data from Google Books API'], $response->status());
        }
    }

    public function data()
    {
        $googleBooksApiUrl = 'https://www.googleapis.com/books/v1/volumes?q=subject:fiction&maxResults=40';

        $response = Http::get($googleBooksApiUrl);
        $booksData = $response->json();

        foreach ($booksData['items'] as $key => $bookData) {
            $volumeInfo = $bookData['volumeInfo'];
            // dd($volumeInfo['publishedDate']);
            Book::create([
                'isbn' => $volumeInfo['industryIdentifiers'][0]['identifier'] ?? NULL,
                'title' => $volumeInfo['title'] ?? NULL,
                'author' => $volumeInfo['authors'][0] ?? NULL,
                'publisher' => $volumeInfo['publisher'] ?? NULL,
                'year' => isset($volumeInfo['publishedDate']) ? substr($volumeInfo['publishedDate'], 0, 4) : NULL,
                'genre' => 'Fiction',
                'quantity' => rand(1, 100),
                'image' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
            ]);
        }
    }

    public function index()
    {
        $books = Book::all();
        $data = [];
        if(isset($books) && count($books) > 0){
            foreach ($books as $key => $value) {
                if(isset($value->is_online_image) && $value->is_online_image == 'no'){
                    $image = isset($value->image) && $value->image != "" ? env('HOST_URL').$value->image : env('HOST_URL').'images/books_default.jpg';
                    $value->image = $image;
                }
                $booking_have = Booking::where('end_date','>=',date('Y-m-d'))->where('book_id',$value->id)->groupBy('unique_id')->count();
                if($booking_have > 0){
                    $value->booked = true;
                } else {
                    $value->booked = false;
                }
                $data[] = $value;
            }
        }
        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'isbn' => 'required',
            'title' => 'required',
            'author' => 'required',
            'publisher' => 'required',
            'year' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'genre' => 'required',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }
        $data['is_online_image'] = 'no';

        $book = Book::create($data);

        return response()->json(['msg' => 'Book Added succesfully','data'=>[], 'status' => true],200);
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);
        if(isset($book->is_online_image) && $book->is_online_image == 'no'){
            $image = isset($book->image) && $book->image != "" ? env('HOST_URL').$book->image : "";
        }
        return response()->json(['msg' => '','data'=>$book, 'status' => true],200);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validator = Validator::make($request->all(),[
            'isbn' => 'required',
            'title' => 'required',
            'author' => 'required',
            'publisher' => 'required',
            'year' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'genre' => 'required',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }
        $book->update($data);

        return response()->json(['msg' => 'Book updated succesfully','data'=>[], 'status' => true],200);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(['msg' => 'Book deleted succesfully','data'=>[], 'status' => true],200);
    }

    public function latestbooks(){
        $books = Book::orderBy('year','DESC')->get();
        if(isset($books) && count($books) > 0){
            foreach ($books as $key => $value) {
                if(isset($value->is_online_image) && $value->is_online_image == 'no'){
                    $image = isset($value->image) && $value->image != "" ? env('HOST_URL').$value->image : env('HOST_URL').'images/books_default.jpg';
                    $value->image = $image;
                }
            }
        }
        return response()->json(['msg' => 'Book deleted succesfully','data'=>$books, 'status' => true],200);
    }

    public function bestseller(){
        $books = Book::get();
        if(isset($books) && count($books) > 0){
            foreach ($books as $key => $value) {
                if(isset($value->is_online_image) && $value->is_online_image == 'no'){
                    $image = isset($value->image) && $value->image != "" ? env('HOST_URL').$value->image : env('HOST_URL').'images/books_default.jpg';
                    $value->image = $image;
                }
            }
        }
        return response()->json(['msg' => 'Book deleted succesfully','data'=>$books, 'status' => true],200);
    }

    public function toprated(){
        $books = Book::get();
        if(isset($books) && count($books) > 0){
            foreach ($books as $key => $value) {
                if(isset($value->is_online_image) && $value->is_online_image == 'no'){
                    $image = isset($value->image) && $value->image != "" ? env('HOST_URL').$value->image : env('HOST_URL').'images/books_default.jpg';
                    $value->image = $image;
                }
            }
        }
        return response()->json(['msg' => 'Book deleted succesfully','data'=>$books, 'status' => true],200);
    }
}
