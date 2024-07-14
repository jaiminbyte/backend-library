<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Book;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Customer;
use App\Models\Conference;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{

    public function booking_get(Request $request){
        $booking = Booking::select('*')->groupBy('unique_id')->get();
        if(isset($booking) && count($booking) > 0){
            foreach ($booking as $key => $value) {
                $book_ids = Booking::where('unique_id',$value->unique_id)->pluck('book_id');
                $value->book_data = Book::whereIn('id',$book_ids)->get();
            }
        }
        return response()->json(['msg' => '','data'=>$booking, 'status' => false]);
    }

    public function booking_save(Request $request){
        $validator = Validator::make($request->all(),[
            'book_ids' => 'required',
            'user_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        do {
            $uniqid = (string) Str::uuid();
            $count = Booking::where('unique_id', $uniqid)->count();
        } while ($count > 0);

        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        if($start_date > $end_date){
            return response()->json(['msg' => 'Book deleted succesfully','data'=>$books, 'status' => false]);
        }
        $days = $start_date->diffInDays($end_date);
        
        if(isset($request->book_ids) && !empty($request->book_ids)){
            foreach($request->book_ids as $book_id){
                Booking::create([
                    'book_id' => $book_id,
                    'user_id' => isset($request->user_id) ? $request->user_id : 0,
                    'start_date' => isset($request->start_date) ? $request->start_date : NULL,
                    'end_date' => isset($request->end_date) ? $request->end_date : NULL,
                    'price' => 1 * $days,
                    'unique_id' => $uniqid,
                ]);
            }
        }
        return response()->json(['msg' => 'Booking updated successfully','data'=>[], 'status' => true],200);
    }

    public function dashboard(){
        $data = [];

        $most_occuring_user = Booking::select('user_id', \DB::raw('SUM(price) as total_price'))
        ->groupBy('user_id')
        ->orderByDesc('total_price')
        ->first();
        
        $user_id = isset($most_occuring_user->user_id) ? $most_occuring_user->user_id : 0;
        $user_name = '';
        if($user_id > 0){
            $user = User::where('id',$user_id)->first();
            $user_name = isset($user->name) ? $user->name : '';
        }

        $most_getting_book = Booking::select('book_id', \DB::raw('COUNT(id) as count'))
        ->groupBy('book_id')
        ->orderByDesc('count')
        ->first();

        $book_id = isset($most_getting_book->book_id) ? $most_getting_book->book_id : 0;
        $book_data = Book::where('id',$book_id)->first();
        
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $startOfSecondLastWeek = Carbon::now()->subDays(14);
        $earned_last_week = Booking::where('created_at','>=',$sevenDaysAgo)->sum('price');
        $earned_second_last = Booking::whereBetween('created_at', [$startOfSecondLastWeek, $sevenDaysAgo])->sum('price');

        $data = [
            "most_occuring_user_name" => $user_name,
            "most_occuring_user_amount" => isset($most_occuring_user->total_price) ? $most_occuring_user->total_price : 0,
            "most_getting_book_name" => isset($book_data->title) ? $book_data->title : 0,
            "most_getting_book_count" => isset($most_getting_book->count) ? $most_getting_book->count : 0,
            "earned_last_week" => $earned_last_week,
            "different_between_last2_weeks" => number_format($earned_last_week - $earned_second_last,2),
        ];

        $booking = Booking::select('user_id', \DB::raw('SUM(price) as total_price'))
        ->groupBy('user_id')
        ->orderByDesc('total_price')
        ->get();
        $user_data = [];
        if(isset($booking) && count($booking) > 0){
            foreach ($booking as $key => $value) {
                $user_id = isset($value->user_id) ? $value->user_id : 0;
                $user_name = '';
                if($user_id > 0){
                    $user = User::where('id',$user_id)->first();
                    $user_name = isset($user->name) ? $user->name : '';
                }
                $amount_pay = Booking::where('user_id',$user_id)->sum('price');
                $last_booking_date = Booking::max('created_at');
                $user_data[] = array(
                    "user_name" => $user_name,
                    "amount_pay" => $amount_pay,
                    "last_booking_date" => $last_booking_date
                );
            }
        }

        $data['user_data'] = $user_data;

        $book_data = Booking::select('book_id', \DB::raw('COUNT(id) as count'))
        ->groupBy('book_id')
        ->orderByDesc('count')
        ->get();

        $book_data_array = [];
        if(isset($book_data) && count($book_data) > 0){
            foreach ($book_data as $key => $value) {
                $book_data_array[] = Book::where('id',$value->book_id)->first();
            }
        }

        $data['book_data'] = $book_data_array;

        $dates = $book_date_array = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = Carbon::now()->subDays($i)->format('Y-m-d');
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $price_sum = Booking::whereDate('created_at',$date)->sum('price');
            $book_date_array[] = number_format($price_sum,2);
        }
        $data['dates'] = $dates;
        $data['book_date_array'] = $book_date_array;

        return response()->json(['msg' => '','data'=>$data, 'status' => true],200);
    }
}
