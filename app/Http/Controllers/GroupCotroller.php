<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class GroupCotroller extends Controller
{


    // public function group(Request $request){
    //     $groups = DB::table('groups')->where('created_by', auth()->user()->id)->latest()->paginate(10);
    //     return view('Groups',compact('groups'));
    // }
    public function group(Request $request){
        $groups = DB::table('groups')->latest()->paginate(10);
        return view('Groups',compact('groups'));
    }

    //Add Group Prioritization List
    public function addGroup(Request $request) {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Check if the user has already submitted the form
            $existingSubmission = DB::table('groups')
                ->where('created_by', Auth::user()->id)
                ->first();
    
            $userType = Auth::user()->user_type;   // Check the user's user_type
    
            // If the user is not an admin and has already submitted, redirect with an error
            if ($existingSubmission && $userType != 'admin') {
                return redirect()->back()->with('error', 'Error');
            }
    
            $proposals = [
                'group_number' => $request->group_number,
                'students_name' => $request->students_name,
                'priorities_list' => $request->priorities_list,
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $result = DB::table('groups')->insertGetId($proposals);
    
            // Set a message for success
            return redirect()->back()->with('success', 'success');
        }
        // If the user is not authenticated, handle accordingly (redirect to login page, etc.)
        return redirect()->route('login')->with('error', 'Error');
    }
    
    
    
    

    public function adminGroups(){
        // dd('here');
        $groups = DB::table('groups')->latest()->paginate(10);
        return view('admin.groups',compact('groups'));

    }

    public function showGroup(){
        $group = DB::table('groups')->where('id', intval($_REQUEST['id']))->first();
        return view('admin.editGroup', compact('group'));
    }

    public function editGroup(Request $request){
        if(intval($request->editGroupId) > 0){
            $arr = array();
            $arr['group_number'] = $request->editGroupNo;
            $arr['students_name'] = $request->editGroupStName;
            $arr['priorities_list'] = $request->editGroupPrList;
            $arr['updated_at'] = time();
            DB::table('groups')->where('id', $request->editGroupId)->update($arr);
            return redirect()->route('admin.groups')->with('success',' Updated successfully.');
        }else{
            return redirect()->route('admin.groups')->with('error','Some Error Occure.');
        }
    }
    public function delGroup(){
        DB::table('groups')->where('id', intval($_REQUEST['id']))->delete();
        return redirect()->route('admin.groups')->with('success',' Deleted successfully.');
    }


    public function addSurvay(Request $request){
        // Check if the user is authenticated
        if (Auth::check()) {
            // Check if the user has already submitted the survey
            $existingSubmission = DB::table('survay')
                ->where('created_by', Auth::user()->id)
                ->first();
    
            // If the user has already submitted, redirect with an error
            if ($existingSubmission) {
                return redirect()->back()->with('error', 'Error');
            }
    
            // Continue processing the survey submission
            $arr = [
                'name' => $request->name,
                'st_id' => $request->st_id,
                'hourPassed' => $request->hourPassed,
                'created_by' => auth()->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
    
            $result = DB::table('survay')->insertGetId($arr);
    
            // Set a flash message for success
            return redirect()->back()->with('success', 'success');        }
    
        // If the user is not authenticated, handle accordingly (redirect to login page, etc.)
        return redirect()->route('login')->with('error', 'Authentication error.');
    }
    

    public function delSurvay(){
        DB::table('survay')->delete();
        return view('HomeAsGPC')->with('success',' Deleted successfully.');
    }


    


    
}
