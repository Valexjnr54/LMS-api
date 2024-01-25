<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CreditLoad;
use Illuminate\Http\Request;

class CreditLoadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createCreditLoad(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'department' => 'required',
            'level' => 'required',
            'semester' => 'required',
            'min_load' => 'required',
            'max_load' => 'required'
        ]);

        $dept = substr($request->input('department'), 0, 3);

        $dept = strtoupper($dept);
        $year = strtoupper($request->input('level'));
        $sem = strtoupper($request->input('semester'));

        if ($year == 'Y1') {
            $lvl = 100;
        }elseif ($year == 'Y2') {
            $lvl = 200;
        }elseif ($year == 'Y3') {
            $lvl = 300;
        }elseif ($year == 'Y4') {
            $lvl = 400;
        }elseif ($year == 'Y5') {
            $lvl = 500;
        }elseif ($year == 'Y6') {
            $lvl = 600;
        }elseif ($year == 'Y7') {
            $lvl = 700;
        }

        if ($sem == 'SM1') {
            $semester = "First Semester";
        }elseif ($sem == 'SM2') {
            $semester = "Second Semester";
        }

        $shortcode = $dept."|".$year."|".$sem;

        if (CreditLoad::where('shortcode',$shortcode)->exists()) {
            return response()->json(['message' => 'Credit Load Already Exist'], 422);
        }

        $load = new CreditLoad();
        $load->department = $request->input('department');
        $load->level = $lvl;
        $load->semester = $semester;
        $load->shortcode = $shortcode;
        $load->min_load = $request->input('min_load');
        $load->max_load = $request->input('max_load');
        $load->save();
        return response()->json(['message' => 'Credit Load created successfully', 'credit_load' => $load],200);
    }

    public function updateCreditLoad(Request $request,$id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'department' => 'required',
            'level' => 'required',
            'semester' => 'required',
            'min_load' => 'required',
            'max_load' => 'required'
        ]);

        $dept = substr($request->input('department'), 0, 3);

        $dept = strtoupper($dept);
        $year = strtoupper($request->input('level'));
        $sem = strtoupper($request->input('semester'));

        if ($year == 'Y1') {
            $lvl = 100;
        }elseif ($year == 'Y2') {
            $lvl = 200;
        }elseif ($year == 'Y3') {
            $lvl = 300;
        }elseif ($year == 'Y4') {
            $lvl = 400;
        }elseif ($year == 'Y5') {
            $lvl = 500;
        }elseif ($year == 'Y6') {
            $lvl = 600;
        }elseif ($year == 'Y7') {
            $lvl = 700;
        }

        if ($sem == 'SM1') {
            $semester = "First Semester";
        }elseif ($sem == 'SM2') {
            $semester = "Second Semester";
        }

        $shortcode = $dept."|".$year."|".$sem;

        // if (CreditLoad::where('shortcode',$shortcode)->exists()) {
        //     return response()->json(['message' => 'Credit Load Already Exist'], 422);
        // }

        $load = CreditLoad::find($id);
        $load->department = $request->input('department');
        $load->level = $lvl;
        $load->semester = $semester;
        $load->shortcode = $shortcode;
        $load->min_load = $request->input('min_load');
        $load->max_load = $request->input('max_load');
        $load->save();

        return response()->json(['message' => 'Credit Load updated successfully', 'credit_load' => $load],200);
    }

    public function viewCreditLoad(Request $request)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $this->validate($request,[
            'department' => 'required',
            'level' => 'required',
            'semester' => 'required',
        ]);

        $dept = substr($request->input('department'), 0, 3);

        $dept = strtoupper($dept);
        $year = strtoupper($request->input('level'));
        $sem = strtoupper($request->input('semester'));

        if ($year == 'Y1') {
            $lvl = 100;
        }elseif ($year == 'Y2') {
            $lvl = 200;
        }elseif ($year == 'Y3') {
            $lvl = 300;
        }elseif ($year == 'Y4') {
            $lvl = 400;
        }elseif ($year == 'Y5') {
            $lvl = 500;
        }elseif ($year == 'Y6') {
            $lvl = 600;
        }elseif ($year == 'Y7') {
            $lvl = 700;
        }

        if ($sem == 'SM1') {
            $semester = "First Semester";
        }elseif ($sem == 'SM2') {
            $semester = "Second Semester";
        }

        $shortcode = $dept."|".$year."|".$sem;

        $load = CreditLoad::where(['shortcode' => $shortcode])->first();

        if ($load->count() > 0) {
            return response()->json(['credit_load'=>$load],200);
        }else{
            return response()->json(['message' => 'No Credit Load set for '.$request->input('department').' '.$lvl.'level '.$semester.' yet'], 404);
        }
    }

    public function deleteCreditLoad($id)
    {
        if (auth()->user()->role !== 1) {
            auth()->logout();
            return response()->json([
                'message' => 'Unauthorized User'
            ],422);
        }

        $load = CreditLoad::find($id);
        if (!$load) {
            return response()->json(['message' => 'Credit Load not found'], 404);
        }

        $load->delete();
        return response()->json(['message'=>'Credit Load Has been deleted'],200);
    }
}
