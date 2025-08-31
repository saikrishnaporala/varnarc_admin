<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\Human_Resource\hr_employee;
use App\Models\Human_Resource\hr_job;
use App\Models\Human_Resource\hr_department;
use App\Models\World_database\res_country;
use App\Models\World_database\res_country_state;
use App\Models\Currency\res_currency;
use App\Models\Company;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\access_right;
use App\User;

class CRMCompaniesController extends Controller
{
    public function index()
    {
        $access=access_right::where('user_id',Auth::id())->first();
        $group=user::find(Auth::id());
        $employee = DB::table('hr_employees')
                    ->join('res_country', 'hr_employees.country_id', '=', 'res_country.id')
                    ->join('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
                    ->join('hr_jobs', 'hr_employees.job_id', '=', 'hr_jobs.id')
                    ->select('hr_employees.*', 'res_country.country_name','hr_departments.department_name','hr_jobs.jobs_name')
                    ->whereNull('hr_employees.deleted_at')
                    ->orderBy('employee_name', 'ASC')
                    ->paginate(30);
        return view ('hr_employee.index',compact('access','group','employee'));
    }

    public function search(Request $request)
    {
        $key=$request->filter;
        $value=$request->value;
        $access=access_right::where('user_id',Auth::id())->first();
        $group=user::find(Auth::id());
        if ($key!=""){
            $employee = DB::table('hr_employees')
                    ->join('res_country', 'hr_employees.country_id', '=', 'res_country.id')
                    ->join('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
                    ->join('hr_jobs', 'hr_employees.job_id', '=', 'hr_jobs.id')
                    ->select('hr_employees.*', 'res_country.country_name','hr_departments.department_name','hr_jobs.jobs_name')
                    ->orderBy('employee_name', 'ASC')
                    ->where($key,'like',"%".$value."%")
                    ->paginate(30);
            $employee ->appends(['filter' => $key ,'value' => $value,'submit' => 'Submit' ])->links();
        }else{
            $employee = DB::table('hr_employees')
                    ->join('res_country', 'hr_employees.country_id', '=', 'res_country.id')
                    ->join('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
                    ->join('hr_jobs', 'hr_employees.job_id', '=', 'hr_jobs.id')
                    ->select('hr_employees.*', 'res_country.country_name','hr_departments.department_name','hr_jobs.jobs_name')
                    ->orderBy('employee_name', 'ASC')
                    ->paginate(30);
        }
        return view('hr_employee.index',compact('access','group','employee'));
    }

    public function create()
    {
        $access=access_right::where('user_id',Auth::id())->first();
        $group=user::find(Auth::id());
        $departments = hr_department::orderBy('department_name', 'ASC')->get();
        $jobs = hr_job::orderBy('jobs_name', 'ASC')->get();
        $country=res_country::orderBy('country_name', 'ASC')->get();
        $state=res_country_state::orderBy('state_name', 'ASC')->get();
        $currency = res_currency::orderBy('currency_name', 'ASC')->get();
        $employee = hr_employee::orderBy('employee_name', 'ASC')->get();
        return view('hr_employee.create',
                compact('access','group','departments','jobs','country','state','currency','employee'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'img_src' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'owner' => 'nullable|string|max:255',
            'industry_type' => 'nullable|string|max:45',
            'company_type' => 'nullable|string|max:45',
            'star_value' => 'nullable|string|max:45',
            'location' => 'nullable|string|max:255',
            'employee' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:45',
            'contact_email' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:45',
            'since' => 'nullable|string|max:45',
            'pisd' => 'nullable|string|max:45',
            'misd' => 'nullable|string|max:45',
            'tin' => 'nullable|string|max:45',
            'cst' => 'nullable|string|max:45',
            'address1' => 'nullable|string|max:45',
            'address2' => 'nullable|integer',
            'area' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:200',
            'state' => 'nullable|string|max:45',
            'country' => 'nullable|string|max:45',
            'landmark' => 'nullable|string|max:45',
            'zip' => 'nullable|string',
            'pname' => 'nullable|string|max:45',
            'pdesignation' => 'nullable|string|max:45',
            'pdepartment' => 'nullable|string|max:45',
            'ppisd' => 'nullable|string|max:45',
            'pphone' => 'nullable|string|max:45',
            'pmisd' => 'nullable|string|max:45',
            'pmobile' => 'nullable|string|max:45',
            'pemail' => 'nullable|string|max:45',
            'sname' => 'nullable|string|max:45',
            'sdesignation' => 'nullable|string|max:45',
            'sdepartment' => 'nullable|string|max:45',
            'spisd' => 'nullable|string|max:45',
            'sphone' => 'nullable|string|max:45',
            'smobile' => 'nullable|string|max:45',
            'semail' => 'nullable|string|max:45',
            'active' => 'nullable|string|max:45',
            'comment' => 'nullable|string|max:45'
        ]);

        try {
            if ($request->hasFile('img_src')) {
                $imagePath = $request->file('img_src')->store('images/companies', 'public');
                $validatedData['img_src'] = $imagePath;
            }
            if($request->id) {
                $lead = Company::find($validatedData['id']);
                if ($lead) {
                    // Update the Company with validated data
                    $lead->name = $validatedData['name'];
                    $lead->company = $validatedData['company'];
                    $lead->phone = $validatedData['phone'];
                    $lead->email = $validatedData['email'];
                    $lead->score = $validatedData['score'];
                    $lead->tags = $validatedData['tags']; // Convert array back to a string
                    $lead->img_src = $validatedData['img_src'];
                    $lead->location = $validatedData['location'];

                    // Save the updated lead to the database
                    $lead->save();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Company updated successfully',
                        'data' => $lead
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Company not found'
                    ], 404);
                }
            } else {
                $leadExists = Company::where('contact_email', $request->email)
                          ->where('contact_phone', $request->phone)
                          ->exists();
                if ($leadExists) {
                    return response()->json(['message' => 'Company with this email and phone combination already exists'], 409);
                }
                $lead = Company::create($validatedData);

                return response()->json(['message' => 'Company created successfully', 'lead' => $lead], 201);
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function edit(hr_employee $hr_employee)
    {
        $access=access_right::where('user_id',Auth::id())->first();
        $group=user::find(Auth::id());
        $departments = hr_department::orderBy('department_name', 'ASC')->get();
        $jobs = hr_job::orderBy('jobs_name', 'ASC')->get();
        $country=res_country::orderBy('country_name', 'ASC')->get();
        $state=res_country_state::orderBy('state_name', 'ASC')->get();
        $currency = res_currency::orderBy('currency_name', 'ASC')->get();
        $employee = hr_employee::orderBy('employee_name', 'ASC')->get();
        return view('hr_employee.edit',
                compact('access','group','hr_employee','departments','jobs','country','state','currency','employee'));
    }

    public function update(Request $request, hr_employee $hr_employee)
    {
        $this->validate($request, [
            'name' => 'required|string|max:50|unique:products',
            'work_email' => 'required|min:5|email',
            'identification_id' => 'required',
            'gender' => 'required',
            'country' => 'required|integer',
            'work_phone'=> 'required',
            'emergency_contact'=> 'required',
            'emergency_phone' => 'required',
            'country_of_birth' => 'required|integer',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);
        try {

            $employee = hr_employee::where('id',$request->id)->first();
            echo $nama_file=$employee->photo;
            $photo = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo')->getClientOriginalName();
                $nama_file = time()."_".$photo;
                $destination = base_path() . '/public/uploads/Employees';
                $request->file('photo')->move($destination, $nama_file);
            }

            $employee->update([
                'employee_name'=> $request->name,
                'identification_id'=> $request->identification_id,
                'gender'=> $request->gender,
                'marital'=> $request->marital,
                'spouse_complete_name'=> $request->spouse_complete_name,
                'spouse_birthdate'=> $request->spouse_birthday,
                'children'=> $request->children,
                'place_of_birth'=> $request->place_of_birth,
                'country_of_birth'=> $request->country_of_birth,
                'address'=> $request->street1,
                'street'=> $request->street2,
                'zip'=> $request->zip,
                'nationality'=> $request->nationality,
                'city'=> $request->city,
                'work_phone'=> $request->work_phone,
                'work_location'=> $request->work_location,
                'mobile_phonee'=> $request->work_mobile,
                'work_email'=> $request->work_email,
                'country_id'=> $request->country,
                'currency_id'=> $request->currency_id,
                'state_id'=> $request->state,
                'birthday'=> $request->birthday,
                'ssnid'=> $request->ssnid,
                'passport_id'=> $request->passport_id,
                'permit_no'=> $request->permit_no,
                'visa_no'=> $request->visa_no,
                'visa_expire'=> $request->visa_expire,
                'salary'=> $request->salary,
                'additional_note'=> $request->name,
                'certificate'=> $request->certificate,
                'study_field'=> $request->study_field,
                'study_school'=> $request->study_school,
                'emergency_contact'=> $request->emergency_contact,
                'emergency_phone'=> $request->emergency_phone,
                'bank_account_id'=> $request->Bank_account_id,
                'department_id'=> $request->department,
                'job_id'=> $request->jobs,
                'photo'=> $nama_file,
                'parent_id'=> $request->parent_id,
                'coach_id'=> $request->coach_id,
            ]);
            Toastr::success('Employee With Name '.$request->name .' was successfully update','Success');
            return redirect(route('employee'));
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(),'Something Wrong');
            // Toastr::error('Check In Error!','Something Wrong');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\hr_employee  $hr_employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lead = Company::find($id);
        $lead -> delete();
        return response()->json(['message' => 'Company successfully deleted!', 'lead' => $lead], 201);
    }

    public function searchapi(Request $request)
    {
        $this->validate($request, [
            'email' => 'required'
        ]);

        $employee = hr_employee::join('res_country', 'hr_employees.country_id', '=', 'res_country.id')
                                ->join('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
                                ->join('hr_jobs', 'hr_employees.job_id', '=', 'hr_jobs.id')
                                ->select('hr_employees.*', 'res_country.country_name','hr_departments.department_name','hr_jobs.jobs_name')
                                ->where('hr_employees.work_email',$request->email)->first();
        $employee = hr_employee::where('work_email', $request->email)->first();
        if ($employee) {
            return response()->json([
                'status' => 'success',
                'data' => $employee
            ], 200);
        }
        return response()->json([
            'status' => 'failed',
            'data' => []
        ]);
    }

    public function fetch(){
        try {
            $response = Company::orderBy('created_at', 'desc')
                    ->get();
            return response()->json([
                'status' => 'success',
                'result' => $response
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                'status' => 'failed',
                'result' => []
            ]);
        }
    }
}
