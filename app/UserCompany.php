<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserCompany extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'u_companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'created_by'
    ];

    /**
     * Get the user record associated with the info.
     */
    public function user()
    {
        return $this->hasMany('App\User', 'id');
    }

    /**
     * Get the user record associated with the info.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllowed($query)
    {
        return $query->where(
            'created_by', \Auth::user()->id
        );
    }

}
