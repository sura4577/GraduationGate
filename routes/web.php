<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\GroupCotroller;
use App\Models\FavoriteProject;
use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('Home');
});


// Route::get('/login2', function () {
//     return view('auth.login2');
// });
Route::get('proposalRequest', [\App\Http\Controllers\ProposalController::class, 'proposalRequest'])->name('proposalRequest');
Auth::routes();
Route::get('/showProjects', [\App\Http\Controllers\ProjectController::class, 'userIndex'])->name('showProjects');
Route::get('/abstract', [\App\Http\Controllers\ProjectController::class, 'abstract'])->name('abstract');
Route::any('/searchProjects', [\App\Http\Controllers\ProjectController::class, 'searchProjects'])->name('searchProjects');

Route::get('/readMore/{id}', [\App\Http\Controllers\ProjectController::class, 'readMore'])->name('readMore');





Route::middleware('auth')->group(function () {


    // Route::middleware('urole')->group(function () {

        Route::get('/HomeAsGPC', function () {
            // dd('here we are');
            if(Auth::user()->user_type=='superviser'){
                return view('Home');
            }
            return view('HomeAsGPC');
        });

        Route::get('/HomeAsGPC', function () {
            
            if(Auth::user()->user_type=='student' || Auth::user()->user_type=='superviser'){
                return view('Home');
            }else{
            return view('HomeAsGPC');
            }
        });
        
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        });
        // Route::get('/UploadProjects', function () {
        //     // dd('here');
        //     return view('UploadProjects');
        // });
        // Route::get('/UploadProjects',[\App\Http\Controllers\ProjectController::class, 'getUserFav'])->name('user.fav');
        Route::get('/favList',[\App\Http\Controllers\ProjectController::class, 'getUserFav'])->name('user.fav');
        Route::get('/removeFav/{id}/{r_proj?}',[\App\Http\Controllers\ProjectController::class, 'removeFav'])->name('remove.fav');
        Route::post('uploadProject', [\App\Http\Controllers\ProjectController::class, 'uploadProject'])->name('uploadProject');
        Route::post('/showFavoriteProjects', [\App\Http\Controllers\ProjectController::class, 'showFavoriteProjects'])->name('showFavoriteProjects');
        Route::get('/UploadProposal', function () {
            if(Auth::user()->user_type=='student'){
                return redirect()->back();
            }
            return view('UploadProposal');
        });
        
        Route::get('contact', [\App\Http\Controllers\ProblemController::class, 'showProblems'])->name('showProblems');
Route::get('/contact', function () {
    return view('Problems');
});
Route::get('problems', [\App\Http\Controllers\ProblemController::class, 'problems'])->name('problems');


        Route::post('addProposals', [\App\Http\Controllers\ProposalController::class, 'addProposals'])->name('addProposals');
        Route::get('proposals', [\App\Http\Controllers\ProposalController::class, 'approvedProposals'])->name('proposals');

        Route::get('/get_proposals',[\App\Http\Controllers\ProposalController::class, 'adminProposal'])->name('admin.proposal');
        Route::get('showProposal', [\App\Http\Controllers\ProposalController::class, 'showProposal'])->name('showProposal');
        Route::post('editProposal', [\App\Http\Controllers\ProposalController::class, 'editProposal'])->name('editProposal');
        Route::get('delProposal', [\App\Http\Controllers\ProposalController::class, 'delProposal'])->name('delProposal');
        
        
        
        
        Route::post('addProblems', [\App\Http\Controllers\ProblemController::class, 'addProblems'])->name('addProblems');
        Route::get('delproblem', [\App\Http\Controllers\ProblemController::class, 'delProblem'])->name('delProb');

       
        
        
        
        
        Route::get('/AddGroup', function () {
            return view('AddGroup');
        });
        Route::post('addGroup', [\App\Http\Controllers\GroupCotroller::class, 'addGroup'])->name('addGroup');
        Route::get('group', [\App\Http\Controllers\GroupCotroller::class, 'group'])->name('group');
        Route::get('showGroup', [\App\Http\Controllers\GroupCotroller::class, 'showGroup'])->name('showGroup');
        Route::get('delGroup', [\App\Http\Controllers\GroupCotroller::class, 'delGroup'])->name('delGroup');
        Route::post('editGroup', [\App\Http\Controllers\GroupCotroller::class, 'editGroup'])->name('editGroup');
        Route::post('addSurvay', [\App\Http\Controllers\GroupCotroller::class, 'addSurvay'])->name('addSurvay');
        Route::get('delSurvay', [\App\Http\Controllers\GroupCotroller::class, 'delSurvay'])->name('delSurvay');
        Route::get('/get_groups',[GroupCotroller::class,'adminGroups'])->name('admin.groups');
        
        
        
     
        
        
        
        Route::get('readMoreProposal', [\App\Http\Controllers\ProposalController::class, 'readMoreProposal'])->name('readMoreProposal');
        
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('/addProjects', function () {
            return view('admin.addProjects');
        })->name('addProjects');
        Route::post('addProjects', [\App\Http\Controllers\ProjectController::class, 'add'])->name('add');
        Route::get('delProjects', [\App\Http\Controllers\ProjectController::class, 'delProjects'])->name('delProjects');
        Route::get('showProject', [\App\Http\Controllers\ProjectController::class, 'showProject'])->name('showProject');
        Route::post('editProject', [\App\Http\Controllers\ProjectController::class, 'editProject'])->name('editProject');
        Route::get('projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects');
        Route::get('notApprovedProjects', [\App\Http\Controllers\ProjectController::class, 'notApprovedProjects'])->name('notApprovedProjects');
        Route::get('approveProject', [\App\Http\Controllers\ProjectController::class, 'approveProject'])->name('approveProject');
        Route::get('rejectProject', [\App\Http\Controllers\ProjectController::class, 'rejectProject'])->name('rejectProject');
        Route::get('favouriteProject', [\App\Http\Controllers\ProjectController::class, 'favouriteProject'])->name('favouriteProject');


        Route::get('notApprovedProposals', [\App\Http\Controllers\ProposalController::class, 'notApprovedProposals'])->name('notApprovedProposals');
        Route::get('approveProposal', [\App\Http\Controllers\ProposalController::class, 'approveProposal'])->name('approveProposal');
        Route::get('rejectProposal', [\App\Http\Controllers\ProposalController::class, 'rejectProposal'])->name('rejectProposal');



        
        Route::get('adminProblems', [\App\Http\Controllers\ProblemController::class, 'adminProblems'])->name('adminProblems');



        Route::post('updateType', [\App\Http\Controllers\UserController::class, 'updateType'])->name('updateType');
        
    // });

    



    Route::view('about', 'about')->name('about');
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});
