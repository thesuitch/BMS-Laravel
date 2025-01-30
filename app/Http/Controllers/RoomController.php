<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RoomCreateRequest;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{

    protected $level_id;
    protected $user_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->is_admin == 1) {
                $this->level_id = auth()->user()->user_id;
            } else {
                $this->level_id = auth()->user()->userinfo->created_by;
            }
            $this->user_id = auth()->user()->user_id;
            return $next($request);
        });
    }

    function store(RoomCreateRequest $request)
    {
        try {
            $room_name = $request->input('room_name');
            $user_id = auth()->user()->user_id;

            $last_room_id = DB::table('rooms')->insertGetId([
                'room_name' => $room_name,
                'created_by' => $user_id,
            ], 'room_id');

            if ($last_room_id > 0) {
                $room = DB::table('rooms')->where('room_id', $last_room_id)->first();

                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Data inserted successfully',
                    'data' => [
                        "room" => $room
                    ]
                ]);
            }
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data is not inserted',
                'data' => [
                    "room" => [],
                ]
            ], 404);
        }
    }

    function getRooms()
    {

        try {

            $rooms = DB::table('rooms')
                ->select('room_id as id' , 'room_name')
                ->where(function ($query) {
                    $query->where('created_by', 0)
                        ->orWhere('created_by', $this->level_id)
                        ->orWhere('wholesaler_by', $this->level_id);
                })
                ->orderBy('room_name')
                ->get();

            if ($rooms->isEmpty()) {
                throw new \Exception();
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    'rooms' => $rooms,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ' . $e->getMessage(),
                'data' => [
                    'rooms' => [],
                ]
            ], 404);
        }
    }
}
