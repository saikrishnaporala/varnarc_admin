<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use App\Models\Human_Resource\hr_attendance;
use App\Models\Human_Resource\hr_employee;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\access_right;
use App\User;
use Carbon\Carbon;


class HrAttendanceController extends Controller
{
    public function index()
    {
        $access=access_right::where('employee_id',Auth::id())->first();
        $group=user::find(Auth::id());
        $attendance = hr_attendance::join('users', 'attendance.employee_id', '=', 'users.id')
                    ->select('attendance.*', 'users.name')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        return view('attendance.index', compact('access','group','attendance'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $startDate=$request->start;
        $endDate=$request->end;
        $name = $request->value;
        $access=access_right::where('employee_id',Auth::id())->first();
        $group=user::find(Auth::id());
        if($startDate!=""||$endDate!="" ){
            $attendance = hr_attendance::join('users', 'attendance.employee_id', '=', 'users.id')
                        ->select('attendance.*', 'users.name')
                        ->orderBy('created_at', 'desc')
                        ->where([['date', '>', $startDate],['date', '<', $endDate]])
                        ->paginate(31);
            $attendance->appends(['start' => $startDate ,'end' => $endDate,'submit' => 'Submit' ])->links();
        }
        else if($name !=""){
            $attendance = hr_attendance::join('users', 'attendance.employee_id', '=', 'users.id')
                        ->select('attendance.*', 'users.name')
                        ->orderBy('created_at', 'desc')
                        ->where('users.name','like',"%".$name."%")
                        ->paginate(31);
            $attendance->appends(['name' => $name ,'submit' => 'Submit' ])->links();
        }
        return view('attendance.index', compact('access','group','attendance'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        try {
            $date=date('Y-m-d');
            $user = User::find($id);
            $name = $user->name;
            $attendance = hr_attendance::create([
                'employee_id'=> $id,
                'date'=> $date,
                'check_in'=> $request->time,
            ]);
            $employee = hr_employee::where('employee_id',$id)->update([
                'active'=> True,
            ]);
            Toastr::success('Welcome '.$name.' <br> Check in Date: ' . $date. ' Time :'.$request->time ,'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Check In Error!','Something Wrong');
            return redirect()->back();
        }
    }

    public function update(Request $request,$id)
    {
        try {
            $date=date('Y-m-d');
            $user = User::find($id);
            $name = $user->name;
            $attendance = hr_attendance::where('employee_id',$id)
                                        ->where('date',$date)
                                        ->update([
                                                    'check_out'=> $request->time,
                                                ]);
            $employee = hr_employee::where('employee_id',$id)->update([
                'active'=> False,
            ]);
            Toastr::success('Goodbye '.$name.' <br> Check out Date: ' . $date. ' Time :'.$request->time ,'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function checkatd(Request $request){
        $this->validate($request, [
            'id' => 'required'
        ]);

        $date=date('Y-m-d');
        $attendance = hr_attendance::all()->where('employee_id',$request->id)->where('date',$date)->count();
        if ($attendance) {
            return response()->json([
                'status' => 'success',
                'data' => $attendance
            ], 200);
        }
        return response()->json([
            'status' => 'failed',
            'data' => []
        ]);
    }
    public function getcount(Request $request){
        $startDate=$request->start;
        $endDate=$request->end;
        $attendance = hr_attendance::where([['employee_id',$request->id],['date', '>', $startDate],['date', '<', $endDate]])->count();
        if ($attendance) {
            return response()->json([
                'status' => 'success',
                'data' => $attendance
            ], 200);
        }
        return response()->json([
            'status' => 'failed',
            'data' => []
        ]);
    }

    public function checkIn(Request $request)
    {
        $employeeId = $request['employee_id'];
        // return Carbon::now();
        // Record the check-in time
        $attendance = hr_attendance::create([
            'employee_id' => $employeeId,
            'check_in' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Checked in successfully', 'data' => $attendance]);
    }

    public function checkOut(Request $request)
    {
        $employeeId = $request->input('employee_id');
        
        // Find the latest check-in record without a check-out time
        $attendance = hr_attendance::where('employee_id', $employeeId)
                                ->whereNull('check_out')
                                ->latest()
                                ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No check-in record found'], 404);
        }

        // Record the check-out time
        $attendance->update(['check_out' => Carbon::now()]);

        return response()->json(['message' => 'Checked out successfully', 'data' => $attendance]);
    }

    public function getTodayAttendance($employeeId)
    {
        $attendance = hr_attendance::where('employee_id', $employeeId)
                                ->whereDate('check_in', Carbon::today())
                                ->get();

        return response()->json(['data' => $attendance]);
    }

    public function getWeeklyAttendance($employeeId)
    {
        $attendance = hr_attendance::where('employee_id', $employeeId)
                                ->whereBetween('check_in', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                ->get();

        return response()->json(['data' => $attendance]);
    }

    public function getMonthlyAttendance($employeeId)
    {
        $attendance = hr_attendance::where('employee_id', $employeeId)
                                ->whereMonth('check_in', Carbon::now()->month)
                                ->whereYear('check_in', Carbon::now()->year)
                                ->get();

        return response()->json(['data' => $attendance]);
    }

    public function hasCheckedInToday(Request $request)
    {
        $employee_id = $request->employee_id;
        // Define the start and end of today
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        // Retrieve all records for the employee for today
        $records = hr_attendance::where('employee_id', $employee_id)
                    ->whereBetween('created_at', [$todayStart, $todayEnd])
                    ->orderBy('id', 'desc')
                    ->get();
        // Initialize total seconds and latest status
        $totalSeconds = 0;
        $isCheckedIn = false;
        foreach ($records as $record) {
            if ($record->check_out) {
                // Calculate the difference in seconds if there is a check-out
                $check_out = Carbon::parse($record->check_out);
                $check_in = Carbon::parse($record->check_in);
                $totalSeconds += $check_in->diffInSeconds($check_out);
            } else {
                // If there's no check-out, assume employee is still checked in
                $isCheckedIn = true;
            }
        }

        // Convert total seconds to hours
        $totalHours = gmdate("H:i:s", $totalSeconds);

        // Return response
        return response()->json([
            'total_hours_today' => $totalHours,
            'checked_in_today' => $isCheckedIn,
            'check_in_time' => $records,
            'message' => $isCheckedIn ? 'Employee has already checked in today.' : 'Employee has not checked in today.'
        ]);
    }
}
