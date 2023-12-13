<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProblemController extends Controller
{

    public function problems(Request $request){
        $problems = DB::table('problems')->latest()->paginate(10);
        return view('ShowProblems',compact('problems'));
    }
    public function addProblems(Request $request){
        $proposals = array();
        $proposals['problem_subject'] = $request->problem_subject;
        $proposals['name'] = $request->name;
        $proposals['email'] = $request->email;
        $proposals['narration'] = $request->narration;
        $proposals['created_by'] = auth()->user()->id;
        $proposals['created_at'] = time();
        $proposals['updated_at'] = time();
        $result = DB::table('problems')->insertGetId($proposals);
        return view('/Problems')->with('success','Problem Uploaded successfully.'); 
    }

    public function adminProblems(){
        $problems = DB::table('problems')->latest()->paginate(10);
        return view('admin.problems',compact('problems'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
    public function delProblem(){
        DB::table('problems')->where('id', intval($_REQUEST['id']))->delete();
        return redirect()->back()->with('success',' Deleted successfully.');
    }
}
