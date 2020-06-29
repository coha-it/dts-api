<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('loginpan', 'Auth\LoginController@attemptLoginPan');
    Route::post('register', 'Auth\RegisterController@register');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('email/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('user', 'User\UserController@self');

    // Get all users surveys which he is membering
    Route::get('surveys-membering', 'SurveyCtrl@getMemberingSurveys');
    Route::get('survey-fillable', 'SurveyCtrl@getFillableSurvey');
    Route::get('first-survey-fillable', 'SurveyCtrl@getFirstSurveyFillable');

    // Update or create Awnser
    Route::post('update-or-create-awnser', 'SurveyCtrl@httpUpdateOrCreateAwnser');

    // Finish Survey
    Route::post('finish-survey', 'SurveyCtrl@httpFinishSurvey');

    // Update Profile
    Route::patch('settings/profile', 'Settings\ProfileController@update');

    // Only Email Users
    Route::group(['middleware' => ['auth.user.email' ] ], function() {

        // Change Password
        Route::patch('settings/password', 'Settings\PasswordController@update');

        // Company
        Route::get('companies/all', 'User\UserController@getCompanies');
        Route::patch('user/company/set', 'User\UserController@setCompanyId');
        Route::post('company/create', 'User\UserController@createCompany');
        Route::patch('company/update', 'User\UserController@updateCompany');

        // Department
        Route::get('departments/all', 'User\UserController@getDepartments');
        Route::patch('user/department/set', 'User\UserController@setDepartmentId');
        Route::post('department/create', 'User\UserController@createDepartment');
        Route::patch('department/update', 'User\UserController@updateDepartment');

        // Locations
        Route::get('locations/all', 'User\UserController@getLocations');
        Route::patch('user/location/set', 'User\UserController@setLocationId');
        Route::post('location/create', 'User\UserController@createLocation');
        Route::patch('location/update', 'User\UserController@updateLocation');

        // Groups
        Route::get('groups-moderating', 'Backend\BackendGroupCtrl@getGroupsModerating'); // Moderating Groups
        Route::get('get-group', 'Backend\BackendGroupCtrl@getGroup'); // Get Group Content
        Route::post('group/create', 'Backend\BackendGroupCtrl@createGroup');
        Route::patch('group/update', 'Backend\BackendGroupCtrl@updateGroup');

        // Get Users
        Route::get('users-created', 'BackendCtrl@loadCreatedUsers'); // Created Users

        // Get a Generated PAN
        Route::get('get-random-pan', 'BackendCtrl@getRandomPan');

        // Update Users Groups
        Route::post('add-user-to-group', 'Backend\BackendGroupCtrl@addUserToGroup');
        Route::post('remove-user-from-group', 'Backend\BackendGroupCtrl@removeUserFromGroup');

        // Change User(s)
        Route::patch('update-created-users', 'BackendCtrl@updateCreatedUsers');
        Route::patch('delete-created-user', 'BackendCtrl@deleteUser');
        Route::post('create-users', 'BackendCtrl@createUsers')->middleware('auth.user.can_create_users');

        // Reload User
        Route::post('reload-user', 'BackendCtrl@getUser');

        // Send Contact Mail to Pan-Users
        Route::post('send-entrance-mail', 'MailCtrl@sendEntranceMail');

        // Route for Importing CSV Files
        Route::post('import-csv', 'ImportCtrl@importCsv');

        // User who can Create Surveys
        Route::group(['middleware' => ['auth.user.can_create_surveys'], 'prefix' => 'backend' ], function()
        {
            // Get Survey(s)
            Route::get('surveys-allowed', 'Backend\BackendSurveyCtrl@getAllowedSurveys');
            Route::get('survey-allowed',  'Backend\BackendSurveyCtrl@getAllowedSurvey')->name('backend.survey');

            // Change Surveys
            Route::patch('update-allowed-survey', 'Backend\BackendSurveyCtrl@tryUpdateAllowedSurvey');
            Route::patch('delete-allowed-survey', 'Backend\BackendSurveyCtrl@deleteSurvey');
            Route::post('create-survey', 'Backend\BackendSurveyCtrl@createUsers');

            // Delete Questions
            Route::patch('delete-questions', 'Backend\BackendSurveyCtrl@deleteQuestions');

            // Statistics
            Route::post('surveys-allowed-filtered', 'Backend\BackendStatisticCtrl@getAllowedFilteredSurveys');
            Route::post('surveys-statistics', 'Backend\BackendStatisticCtrl@getSurveysStatistics');
        });
    });
});
