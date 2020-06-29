<?php

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

// Route::group(['middleware' => 'auth.user.admin'], function () {

    // Route::get('awnsers-1', function () {
    //     dd(
    //         Auth()->user()->fillableSurveys()->find(22) // find survey
    //         ->questions->find(108) // Find question
    //         ->question_options->find(141) // Find Options
    //         ->awnser_options->find(1) // Find Awnser Options
    //         ->awnser->toArray()
    //     );
    // });

    // Route::get('create-awnser-1', function () {
    //         $aw = Auth()->user()->fillableSurveys()->find(22) // find survey
    //         ->questions->find(109) // Find question
    //         ->awnser()->updateOrCreate([
    //             'user_id' => Auth()->user()->id
    //         ]);

    //         $aw->awnser_options()->updateOrCreate([
    //             'option_id' => 138
    //         ]);
    //         // ->question_options->find(141) // Find Options
    // });

    // Test Surveys
    // Route::get('show-surveys', function() {
    //     dd(
    //         auth()->user()->allowedSurveys()->toArray()
    //         // auth()->user()->allowedSurveys()->first()->user_finished->get()->toArray()
    //     );
    // });

    // // Test 2
    // Route::get('test', function() {

    //     // Get User By Info-Field where User's Info is Johan
    //     $user = App\User::whereHas('info', function ($query) {
    //         $query->where('firstname', '=', 'johan');
    //     })->first();

    //     dd($user);
    // });

    // // Test!
    // Route::get('test-update-relationed-value', function() {
    //     $user = App\User::find(1)->with('info')->first();

    //     $user->info->firstname = "Alexander 123";
    //     $user->info->save();

    //     dd($user->info->firstname);
    // });

    // Test Middlware
    // route::get('middleware', function() {
    //     return "jo";
    // })->middleware('auth.user.pan');

    // // Newsletter
    // Route::get('newsletter', function() {
    //     dd(Auth()->user()->newsletter);
    //     return '';
    // });

    // Create Users company
    // Route::get('create-company', function() {
    //     $usr = Auth()->user();
    //     $usr->company->create([
    //         'name' => 'Neue Firma 123',
    //         'created_by' => $usr->id,
    //     ]);
    // });

    // // Get User's Company
    // Route::get('company', function() {
    //     dd(Auth()->user()->company->name);
    //     return '';
    // });

    // Route::get('update/group', function() {
    //     // Update a Group which you are Moderating
    //     $g = App\User::find(5)->groupsModerating()->find(2);

    //     try {
    //         $g->update(['name' => rand(0, 1000) ]);
    //         return $g;
    //     } catch (\Throwable $th) {
    //         return "ne man";
    //     }
    // });

    // Route::get('groups', function() {

    //     // Get All Groups from User with id 5 which he can moderate
    //     dd(App\User::find(5)->groupsModerating);

    //     // Get All Groups in which user with id 11 is a part of
    //     dd(App\User::find(11)->groups);

    //     // Get all Members from current User's Group
    //     dd(Auth()->user()->groups->first()->users);
    //     // Or like This
    //     dd(Auth()->user()->groups()->first()->users()->get());

    //     // Get all Groups from current User
    //     dd(Auth()->user()->groups);
    // });

    // Route::get('group-surveys', function() {
    //     dd(Auth()->user()->groupSurveys());
    // });


    // Route::get('allowed-surveys', function() {
    //     $self = Auth()->user();
    //     $allowedSurveys = $self->surveys();

    //     foreach ($self->groupsModerating() as $i => $group)
    //     {
    //         foreach ($group->surveys as $j => $survey)
    //         {
    //             if( !$allowedSurveys->find($survey->id) )
    //             {
    //                 $allowedSurveys->push($survey);
    //             }
    //         }
    //     }

    //     dd( $allowedSurveys );
    // });


    // Route::get('survey-question', function() {
    //     // dd(Auth()->user()->surveys()->find(12)->questions);
    //     // dd(App\Question::find(1)->survey);
    //     // dd(App\Question::find(1)->option);

    //     // dd(App\QuestionOption::find(1)->question->with(['option'])->get());
    //     // echo "<pre><code>";
    //     // print_r(App\QuestionOption::find(1)->question->toArray());
    // });

    // // Get PAN and then get User
    // Route::get('user/pan', function() {
    //     dd(
    //         App\UserPan::find(12)->user()->first()->id
    //     );

    //     return "";
    // });

    // // Get Companies created by the User
    // Route::get('companies', function() {
    //     dd(Auth()->user()->createdCompanies);
    //     return '';
    // });

    // Route::get('creator', function() {
    //     dd(Auth()->user()->creator->pan);
    //     return '';
    // });

    // Route::get('users', function() {
    //     dd(Auth()->user()->users);
    //     return '';
    // });

// });

Route::get('{path}', 'RouteCtrl@index')->where('path', '(.*)');
