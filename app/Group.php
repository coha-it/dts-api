<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Group;

class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description_public', 'description_mods', 'pivot', 'is_mod', 'is_member'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = ['description_mods'];

    /**
     * Get the user record associated with the info.
     */
    public function users()
    {
        return $this->belongsToMany('App\User')->withPivot(['is_member', 'is_mod'])->with(['pan']);
    }

    /**
     * Get the user record associated with the info.
     */
    public function members()
    {
        return $this->belongsToMany('App\User')->wherePivot('is_member', 1);
    }

    /**
     * Get the user record associated with the info.
     */
    public function moderators()
    {
        return $this->belongsToMany('App\User')->wherePivot('is_mod', 1);
    }

    /**
     * Get the Survey records associated with the Group.
     */
    public function surveys()
    {
        return $this->belongsToMany('App\Survey', 'survey_group');
    }
}
