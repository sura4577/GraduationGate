<?php

namespace App\Http\Controllers;
use App\Models\Projects;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FavoriteProject;
class ProjectController extends Controller
{
    public function userIndex(Request $request)
{
    $queryParams = [];

    if ($request->has('years')) {
        $queryParams['years'] = $request->get('years');
    }

    if ($request->has('classification')) {
        $queryParams['classification'] = $request->get('classification');
    }

    if ($request->has('searchProject')) {
        $queryParams['searchProject'] = $request->get('searchProject');
    }

    $projects = Projects::where('approved', 'approve')
        ->when($queryParams, function ($query) use ($queryParams) {
            $query->where($queryParams);
        })
        ->orderBy('uploade_date', 'desc')
        ->latest()
        ->paginate(12);

    return view('Projects', compact('projects'))
        ->with('i', ($projects->currentPage() - 1) * $projects->perPage());
}

    

    public function index()
    {
        if(auth()->user()->user_type == 'admin'){
            $projects = Projects::latest()->paginate(10);
        }else{
            $projects = Projects::where('created_by', auth()->user()->id)->latest()->paginate(10);
        }
        
        return view('admin.projects',compact('projects'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

   
    public function add(Request $request){
        $projects = array();
        $projects['name'] = $request->projectName;
        $projects['narration'] = $request->projectNarration;
        $projects['created_by'] = auth()->user()->id;
        $projects['created_at'] = time();
        $projects['updated_at'] = time();
        $result = DB::table('projects')->insertGetId($projects);
        if($result){
            if($request->projectFile){
                foreach($request->projectFile as $key => $val){
                    $fileName = rand(100000, 999999).time().'.'.$val->extension();
                    $val->move(base_path().'/public/uploades/', $fileName);
                    $projectsFile['project_id'] = $result;
                    $projectsFile['file'] = $fileName;
                    $projectsFile['created_by'] = auth()->user()->id;
                    $projectsFile['created_at'] = time();
                    $projectsFile['updated_at'] = time();
                    $projFile = DB::table('project_files')->insertGetId($projectsFile);
                } 
            }
            return redirect()->route('projects')->with('success','Project created successfully.');
        }else{
            return redirect()->route('projects')->with('error','Some Error Occure.');
        } 
    }

    public function show(){
        $projects = Projects::all();
        return view('admin.projects', compact('projects'));
    }

    public function showProject(){
        $project = DB::table('projects')->where('id', intval($_REQUEST['id']))->first();
        return view('admin.editProject', compact('project'));
    }
    

    public function editProject(Request $request){
        if(intval($request->editProjectId) > 0){
            $projects = array();
            $projects['name'] = $request->editprojectName;
            $projects['narration'] = $request->editprojectNarration;
            $projects['updated_at'] = time();
            DB::table('projects')->where('id', $request->editProjectId)->update($projects);
            if($request->editprojectFile){
                foreach($request->editprojectFile as $key => $val){
                    $fileName = rand(100000, 999999).time().'.'.$val->extension();
                    $val->move(base_path().'/public/uploades/', $fileName);
                    $projectsFile['project_id'] = $request->editProjectId;
                    $projectsFile['file'] = $fileName;
                    $projectsFile['created_by'] = auth()->user()->id;
                    $projectsFile['created_at'] = time();
                    $projectsFile['updated_at'] = time();
                    $projFile = DB::table('project_files')->insertGetId($projectsFile);
                } 
            }
            return redirect()->route('projects')->with('success','Project Updated successfully.');
        }else{
            return redirect()->route('projects')->with('error','Some Error Occure.');
        }
    }

    public function delProjects(){
        DB::table('projects')->where('id', intval($_REQUEST['id']))->delete();
        // DB::table('project_files')->where('project_id', intval($_REQUEST['id']))->delete();
        $projects = Projects::where('approved', 'approve')->latest()->paginate(8);
        return view('Projects',compact('projects'))
            ->with('i', (request()->input('page', 1) - 1) * 8)->with('success','Project Deleted successfully.');
        // return redirect()->route('projects')->with('success','Project Deleted successfully.');
    }

    public function notApprovedProjects(){
        $projects = Projects::where('approved', 'pending')->latest()->paginate(10);
        return view('admin.notApprovedProjects',compact('projects'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }


    public function approveProject(){
        $projects = array();
        $projects['approved'] = 'approve';
        $projects['approve_by'] = auth()->user()->id;
        DB::table('projects')->where('id', intval($_REQUEST['id']))->update($projects);
        return redirect()->route('notApprovedProjects')->with('success','Project Approved successfully.');
    }
    public function rejectProject(){
        $projects = array();
        $projects['approved'] = 'reject';
        $projects['approve_by'] = auth()->user()->id;
        DB::table('projects')->where('id', intval($_REQUEST['id']))->update($projects);
        return redirect()->route('notApprovedProjects')->with('success','Project Rejected successfully.');
    }

    public function readMore($id) {
        $project = DB::table('projects')->where('id', $id)->first();
    
        // Check if the project exists
        if (!$project) {
            abort(404, 'Project not found');
        }
    
        return view('Abstract', compact('project'));
    }
    
    public function favouriteProject(){
        $favourite['project_id'] = intval($_REQUEST['id']);
        $favourite['created_by'] = auth()->user()->id;
        $favourite['created_at'] = time();
        $favourite['updated_at'] = time();
        $projFile = DB::table('favourite_project')->insertGetId($favourite);
        $project = DB::table('projects')->where('id', intval($_REQUEST['id']))->first();
        return view('Abstract', compact('project'));
    }

    // public function delUSerProject(){
    //     DB::table('projects')->where('id', intval($_REQUEST['id']))->delete();
    //     return redirect()->route('projects')->with('success','Project Deleted successfully.');
    // }

    public function getUserFav(){
        $favProjects=DB::table('favourite_project as fp')
        ->join('projects as p','p.id','=','fp.project_id')
        ->where('fp.created_by','=',intval(auth()->user()->id))
        ->get();
        return view('UploadProjects',compact('favProjects'));
        
    }

    public function removeFav($id,$r_proj=''){
        $fav=DB::table('favourite_project')->where('project_id','=',intval($id))
        ->where('created_by','=',intval(auth()->user()->id))
        ->delete();
        if($r_proj!=''){
            $project = DB::table('projects')->where('id', intval($id))->first();
            return view('Abstract', compact('project'));
        }
        return $this->getUserFav();
    }



    public function showFavoriteProjects(Request $request)
    {
        // Check if the project is already a favorite
        $favoriteProject = FavoriteProject::where('created_by', auth()->user()->id)
            ->where('project_id', $request->projectId)
            ->first();

        if ($favoriteProject) {
            // Project is already a favorite, you can handle this case if needed
            return response()->json(['success' => false, 'message' => 'Project is already a favorite']);
        }

        // Get the project details including abstract
        $projectDetails = Projects::select('id', 'title', 'supervisor_name', 'research_specialization', 'abstract')
            ->where('id', $request->projectId)
            ->first();

        if (!$projectDetails) {
            // Project not found, handle this case if needed
            return response()->json(['success' => false, 'message' => 'Project not found']);
        }

        // If not, add the project to favorites
        $newFavorite = new FavoriteProject();
        $newFavorite->created_by = auth()->user()->id;
        $newFavorite->project_id = $request->projectId;
        $newFavorite->title = $projectDetails->title; // Add title
        $newFavorite->supervisor_name = $projectDetails->supervisor_name; // Add supervisor_name
        $newFavorite->research_specialization = $projectDetails->research_specialization; // Add research_specialization
        $newFavorite->abstract = $projectDetails->abstract; // Add abstract
        $newFavorite->save();

        // Return success message and project details
        return response()->json(['success' => true, 'message' => 'Project added to favorites', 'projectDetails' => $projectDetails]);
    }







    public function uploadProject(Request $request){
        // Create an array to store project data
        $projects = array();
    
        // Assign values from the request to the project array
        $projects['name'] = $request->name;  // Project name
        $projects['narration'] = $request->narration;  // Project narration
        $projects['research_specialization'] = $request->research_specialization;  // Research specialization
        $projects['supervisor_name'] = $request->supervisor_name;  // Supervisor name
        $projects['students_name'] = $request->students_name;  // Students' names
        $projects['uploade_date'] = strtotime($request->uploade_date);  // Uploaded date as a timestamp
        $projects['url'] = $request->url;  // URL related to the project
        $projects['created_by'] = auth()->user()->id;  // ID of the user creating the project
        $projects['created_at'] = time();  // Timestamp for creation time
        $projects['updated_at'] = time();  // Timestamp for last update time
    
        // Insert the project data into the 'projects' table and get the inserted ID
        $result = DB::table('projects')->insertGetId($projects);
    
        // Retrieve approved projects with pagination
        $projects = Projects::where('approved', 'approve')->latest()->paginate(8);
    
        // Redirect to the 'showProjects' route
        return redirect()->route('showProjects');
    }
    

    // public function searchProjects(Request $request){
    //     $sql = "SELECT * FROM projects where  1=1 ";
    //     if($request->searchProject != ''){
    //         $sql .= "and name like '%".$request->searchProject."%'";
    //     }
    //     if($request->years != ''){
    //         $sql .= " and FROM_UNIXTIME(uploade_date , '%Y') = ".$request->years;
    //     }
    //     if($request->classification != ''){
    //         $sql .= " and research_specialization = '".$request->classification."'";
    //     }
    //     $projects = DB::select(DB::raw($sql))->paginate(12);
    //     return view('Projects', compact('projects'));
    // }


    public function searchProjects(Request $request)
    {
        // Start with a query on the 'projects' table
        $query = DB::table('projects');
    
        if ($request->years) {  // Check if the 'years' parameter is provided in the request
            $startOfYear = strtotime($request->years . '-01-01 00:00:00');
            $endOfYear = strtotime($request->years . '-12-31 23:59:59');
    
            $query->whereBetween('uploade_date', [$startOfYear, $endOfYear]);
        }
       
        if ($request->searchProject) { // Check if the 'searchProject' parameter is provided in the request
            // Add a condition to search for projects based on project name, supervisor name, or students name
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->searchProject . '%')
                  ->orWhere('supervisor_name', 'like', '%' . $request->searchProject . '%')
                  ->orWhere('students_name', 'like', '%' . $request->searchProject . '%');
            });
        }
    
        if ($request->classification) {  // Check if the 'classification' parameter is provided in the request
            // Add a condition to filter projects based on the selected research specialization (classification)
            $query->where('research_specialization', '=', $request->classification);
        }
    
        // Get the projects based on the applied conditions and paginate the results
        $projects = $query->latest()->paginate(12);
    
        // Return the 'Projects' view with the paginated projects
        return view('Projects', compact('projects'));
    }
    

    
    
    
    




    




    
}
