<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ScheduleController extends Controller
{   
    // add
    public function addschedule(Request $request){
        $validator = Validator::make($request->all(), [
            'monday' => 'required|string',
            'tuesday' => 'required|string',
            'wednesday' => 'required|string',
            'thursday' => 'required|string',
            'friday' => 'required|string',
            'saturday' => 'required|string',
            'userID' => 'required|integer|max:13|unique:schedule',
            'shift' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'operation' => 'failure',
                'message' => 'Missing data/invalid data. Please try again.',
                'errors' => $validator->errors()->all()
            ], 400);
            exit();
        }
        $schedule = [
            'monday' => $request->input('monday'),
            'tuesday' => $request->input('tuesday'),
            'wednesday' => $request->input('wednesday'),
            'thursday' => $request->input('thursday'),
            'friday' => $request->input('friday'),
            'saturday' => $request->input('saturday'),
            'userID' => $request->input('userID'),
            'shift' => $request->input('shift'),
        ];

        $inserted = DB::connection('mysql')->table('schedule')->insert($schedule);

        if (!$inserted) {
            return response()->json([
                'status' => 500,
                'operation' => 'failure',
                'message' => 'Failed to add schedule. Please try again later.'
            ], 500);
        }

        return response()->json([
            'status' => 200,
            'operation' => 'success',
            'message' => 'Schedule added  successfully'
        ], 200);
    }
    // edit
    public function updateschedule(Request $request){
        $validator = Validator::make($request->all(), [
            'monday' => 'required|string',
            'tuesday' => 'required|string',
            'wednesday' => 'required|string',
            'thursday' => 'required|string',
            'friday' => 'required|string',
            'saturday' => 'required|string',
            'userID' => 'required|numeric',
            'shift' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'operation' => 'failure',
                'message' => 'Missing data/invalid data. Please try again.',
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $schedule = [
            'monday' => $request->input('monday'),
            'tuesday' => $request->input('tuesday'),
            'wednesday' => $request->input('wednesday'),
            'thursday' => $request->input('thursday'),
            'friday' => $request->input('friday'),
            'saturday' => $request->input('saturday'),
            'shift' => $request->input('shift'),
        ];

        $userID = $request->input('userID');

        $updated = DB::connection('mysql')->table('schedule')->where('userID', $userID)->update($schedule);

        if ($updated) {
            return response()->json([
                'status' => 200,
                'operation' => 'success',
                'message' => 'Schedule updated successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'operation' => 'failure',
                'message' => 'Failed to update schedule. Please try again later.'
            ], 500);
        }
    }

    //return schedule
 
    public function allschedules(Request $request){
        $validator = Validator::make($request->all(), [
            'userID' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'operation' => 'failure',
                'message' => 'Missing data/invalid data. Please try again.',
                'errors' => $validator->errors()->all()
            ], 400);
        }
        $userID = $request->input('userID');
        $schedules = DB::connection('mysql')->table('schedule')->where('userID', $userID)->get();
        if ($schedules->count() > 0) {
            $data = [];
            foreach ($schedules as $schedule) {
                $dates = $this->getDatesForWeek();
    
                $data[] = [
                    "userID" => $schedule->userID,
                    "monday" => $schedule->monday,
                    "tuesday" => $schedule->tuesday,
                    "wednesday" => $schedule->wednesday,
                    "thursday" => $schedule->thursday,
                    "friday" => $schedule->friday,
                    "saturday" => $schedule->saturday,
                    "shift" => $schedule->shift,
                    "dates" => $dates,
                ];
            }
    
            return response()->json([
                'status' => 200,
                'operation' => 'Success',
                'message' => 'Schedules fetched successfully',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'operation' => 'Failure',
                'message' => 'No schedules found'
            ], 404);
        }
    }
    
    private function getDatesForWeek(){
        $now = Carbon::now();
        $startOfWeek = $now->startOfWeek();
        $dates = [];
        for ($i = 0; $i < 6; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            $dayOfWeek = $startOfWeek->copy()->addDays($i)->format('l');
            
            if ($dayOfWeek != 'Sunday') {
                $dates[$dayOfWeek] = $date;
            }
        }
        return $dates;
    }
    
    //delete schedule
    public function deleteSchedule(Request $request){
        $validator = Validator::make($request->all(), [
            'userID' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 404,
                'operation' => 'failure',
                'message' => 'Missing data/invalid data. Please try again.',
                'errors' => $validator->errors()->all()
            ], 404);
        } else {
            $userID = $request->input('userID');
            $deletedSchedule = DB::connection('mysql')->table('schedule')->where('userID', $userID)->delete();
    
            if ($deletedSchedule > 0) {
                return response()->json([
                    'status' => 200,
                    'operation' => 'Success',
                    'message' => 'Schedule deleted successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'operation' => 'Failure',
                    'message' => 'No schedule found'
                ], 404);
            }
        }
    }

}
