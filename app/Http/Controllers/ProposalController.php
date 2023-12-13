<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Auth;
class ProposalController extends Controller
{


    public function index()
    {
        if(auth()->user()->user_type == 'admin'){
            $proposals =  DB::table('proposals')->latest()->paginate(10);
        }else{
            $proposals = DB::table('proposals')->where('created_by', auth()->user()->id)->latest()->paginate(10);
        }
        return view('ShowProposal',compact('proposals'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function proposalRequest()
    {
        $proposals =  DB::table('proposals')->where('created_by', auth()->user()->id)->latest()->paginate(10);
        return view('Requests',compact('proposals'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function adminProposal(){
        // dd('here');
        $proposals = DB::table('proposals')->latest()->paginate(10);
        return view('admin.proposal',compact('proposals'));

    }

    
    public function addProposals(Request $request){
        $proposals = array();
        $proposals['title'] = $request->title;
        $proposals['supervisor_name'] = $request->supervisor_name;
        $proposals['research_specialization'] = $request->research_specialization;
        $proposals['date_of_submission'] = strtotime($request->date_of_submission);
        $proposals['proposal'] = $request->proposal;
        if(auth()->user()->user_type == 'admin'){
            $proposal['approved']='approve';
        }
        $proposals['created_by'] = auth()->user()->id;
        $proposals['created_at'] = time();
        $proposals['updated_at'] = time();
        $result = DB::table('proposals')->insertGetId($proposals);
        return redirect()->back()->with('success','success'); 
    }

    public function notApprovedProposals(){
        $proposals = DB::table('proposals')->where('approved', 'pending')->latest()->paginate(10);
        return view('admin.notApprovedProposals',compact('proposals'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function approvedProposals(){
        $proposals = DB::table('proposals')->where('approved', 'approve')->latest()->paginate(12);
        return view('ShowProposal',compact('proposals'))
            ->with('i', (request()->input('page', 1) - 1) * 12);
    }

    public function approveProposal(){
        // Create an array to update the proposal with approval status and approver information
        $proposal = array();
        $proposal['approved'] = 'approve';  // Set the approval status to 'approve'
        $proposal['approve_by'] = auth()->user()->id;  // Set the ID of the user who approves the proposal
    
        // Update the 'proposals' table where the 'id' matches the provided proposal ID
        DB::table('proposals')->where('id', intval($_REQUEST['id']))->update($proposal);
    
        // Redirect to the 'notApprovedProposals' route with a success message
        return redirect()->route('notApprovedProposals')->with('success','Proposal Approved successfully.');
    }
    
    public function rejectProposal(){
        // Create an array to update the proposal with rejection status and approver information
        $proposal = array();
        $proposal['approved'] = 'reject';  // Set the approval status to 'reject'
        $proposal['approve_by'] = auth()->user()->id;  // Set the ID of the user who rejects the proposal
    
        // Update the 'proposals' table where the 'id' matches the provided proposal ID
        DB::table('proposals')->where('id', intval($_REQUEST['id']))->update($proposal);
    
        // Redirect to the 'notApprovedProposals' route with a success message
        return redirect()->route('notApprovedProposals')->with('success','Proposal Rejected successfully.');
    }
    

    public function readMoreProposal(){
        $proposal = DB::table('proposals')->where('id', intval($_REQUEST['id']))->first();
        return view('AbstractProposal', compact('proposal'));
    }

    public function showProposal(){
        $proposal = DB::table('proposals')->where('id', intval($_REQUEST['id']))->first();
        return view('admin.editProposal', compact('proposal'));
    }


    public function editProposal(Request $request){
        if(intval($request->editProposalId) > 0){
            $arr = array();
            $arr['title'] = $request->title;
            $arr['supervisor_name'] = $request->supervisor_name;
            $arr['research_specialization'] = $request->research_specialization;
            $arr['date_of_submission'] = strtotime($request->date_of_submission);
            $arr['proposal'] = $request->proposal;
            $arr['updated_at'] = time();
            DB::table('proposals')->where('id', $request->editProposalId)->update($arr);
            return redirect()->route('admin.proposal')->with('success',' Updated successfully.');
        }else{
            return redirect()->route('admin.proposal')->with('error','Some Error Occure.');
        }
    }
    public function delProposal(){
        DB::table('proposals')->where('id', intval($_REQUEST['id']))->delete();
        return redirect()->back()->with('success',' Deleted successfully.');
    }

    

}
