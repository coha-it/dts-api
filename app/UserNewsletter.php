<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserNewsletter extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'u_newsletter';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];

    /**
     * Get the user record associated with the info.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id');
    }
}
