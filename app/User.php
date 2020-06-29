<?php

namespace App;

use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Survey;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'company_id', 'department_id', 'location_id', 'created_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'newsletter',
        'is_panuser',
        'is_adminuser'
    ];

    public function getNewsletterAttribute()
    {
        return $this->newsletter();
    }

    public function getIsPanUserAttribute()
    {
        return $this->isPanUser();
    }

    public function getIsAdminUserAttribute()
    {
        return $this->isAdminUser();
    }

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the info record associated with the user.
     */
    public function info()
    {
        return $this->hasOne('App\UserInfo', 'user_id');
    }

    /**
     * Get the right record associated with the user.
     */
    public function right()
    {
        return $this->hasOne('App\UserRight', 'user_id');
    }

    public function canUseHtml()
    {
        return $this->isAdminUser() || (
                $this->right &&
                $this->right->use_html
        );
    }

    /**
     * Get the pan record associated with the user.
     */
    public function pan()
    {
        return $this->hasOne('App\UserPan', 'user_id');
    }

    /**
     * Get the newsletter record associated with the user.
     */
    public function newsletter()
    {
        return $this->hasOne('App\UserNewsletter', 'user_id');
    }

    /**
     * Get the company record associated with the user.
     */
    public function company()
    {
        return $this->belongsTo('App\UserCompany');
    }

    /**
     * Get the company records created by the user.
     */
    public function companies()
    {
        return $this->hasMany('App\UserCompany', 'created_by');
    }

    /**
     * Get the company record associated with the user.
     */
    public function department()
    {
        return $this->belongsTo('App\UserDepartment');
    }

    /**
     * Get the company record associated with the user.
     */
    public function departments()
    {
        return $this->hasMany('App\UserDepartment', 'created_by');
    }

    /**
     * Get the company record associated with the user.
     */
    public function location()
    {
        return $this->belongsTo('App\UserLocation');
    }

    /**
     * Get the company record associated with the user.
     */
    public function locations()
    {
        return $this->hasMany('App\UserLocation', 'created_by');
    }

    /**
     * Get the company record associated with the user.
     */
    public function users()
    {
        return $this->hasMany('App\User', 'created_by')->with([
            'pan', 'groups', 'company', 'department', 'location'
        ]);
    }

    /**
     * Get the company record associated with the user.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get the company record associated with the user.
     */
    public function groups()
    {
        return $this->belongsToMany('App\Group')->withPivot(['is_mod', 'is_member']);
    }

    /**
     * Get the company record associated with the user.
     */
    public function groupsModerating()
    {
        return $this->groups()->wherePivot('is_mod', 1)->withPivot(['is_mod', 'is_member']);
    }

    /**
     * Get the company record associated with the user.
     */
    public function groupsMembering()
    {
        return $this->groups()->wherePivot('is_member', 1)->withPivot(['is_mod', 'is_member']);
    }

    /**
     * Get the company record associated with the user.
     */
    public function createdSurveys()
    {
        return $this->hasMany('App\Survey', 'created_by');
    }

    /**
     * Get Surveys where User is a Member
     */
    public function memberingSurveys()
    {
        return Survey::
            join('survey_group',      'surveys.id',             '=', 'survey_group.survey_id')->
            join('groups',            'groups.id',              '=', 'survey_group.group_id')->
            join('group_user',        'group_user.group_id',    '=', 'groups.id')->
            where('group_user.user_id', '=', $this->id)->
            where('group_user.is_member', '=', 1)->
            select(
                'surveys.*',
                'groups.id AS group_id',
                'survey_group.survey_id AS survey_id',
                'group_user.group_id'
            )->
            get()-> // Hols dir
            unique('survey_id') // Eindeutige IDs
        ;
    }

    /**
     * Get the Surveys inside the Group
     */
    public function groupSurveys()
    {
        return Survey::
            join('survey_group',      'surveys.id',             '=', 'survey_group.survey_id')->
            join('groups',            'groups.id',              '=', 'survey_group.group_id')->
            join('group_user',        'group_user.group_id',    '=', 'groups.id')->
            where('group_user.is_mod', '=', 1)->
            select(
                'surveys.*',
                'groups.id AS group_id',
                'survey_group.survey_id AS survey_id',
                'group_user.group_id'
            )->
            get()-> // Hols dir
            unique('survey_id') // Eindeutige IDs
        ;
    }

    /**
     * Get all the Allowed Surveys
     */
    public function allowedSurveys()
    {
        return $this
            ->createdSurveys()
            ->get()
            ->unique('id')
            ->merge(
                $this->groupSurveys()
            )
        ;
    }

    /**
     * Get all the Fillable Surveys
     */
    public function fillableSurveys()
    {
        return $this
                ->memberingSurveys()
                ->where('is_fillable', true)
                ->whereNull('user_finished');
    }

    /**
     * Get the Fillable Survey
     */
    public function fillableSurvey($id)
    {
        return $this->fillableSurveys()->find($id)->getSelfWithQuestionsAndUsersAwnser();
    }

    /**
     * Get the info record associated with the user.
     */
    public function getSelfWithRelations()
    {
        return $this->with(['pan', 'right', 'groupsModerating', 'company', 'companies', 'department', 'departments', 'location', 'locations', 'newsletter'])->find($this->id);
    }

    /**
     * Get the info record associated with the user.
     */
    public function getSelfWithPanUserRelations()
    {
        return $this->with(['pan', 'groups', 'company', 'department', 'location'])->find($this->id);
    }

    /**
     * Get the info record associated with the user.
     */
    public function getPanUsersWithRelations()
    {
        return $this->hasMany('App\User', 'created_by')->with(['pan', 'groups']);
    }

    /**
     * Check if this User Is a Pan User
     */
    public function isPanUser() {
        return $this->pan && $this->pan->is_pan_user === 1;
    }

    /**
     * Check if this User is a E-Mail and not a PAN-User
     */
    public function isEmailUser() {
        return !$this->isPanUser();
    }

    /**
     * Check if Is Admin
     */
    public function isAdminUser() {
        return $this->right && $this->right->admin === 1;
    }

    /**
     * Check if User can create other Users
     */
    public function canCreateUsers() {
        return $this->right && $this->right->create_users === 1;
    }

    /**
     * Check if User can create Groups
     */
    public function canCreateGroups() {
        return $this->right && $this->right->create_groups === 1;
    }

    /**
     * Check if User can create Surveys
     */
    public function canCreateSurveys() {
        return $this->right && $this->right->create_surveys === 1;
    }
}
