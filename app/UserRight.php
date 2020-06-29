<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserRight extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'u_rights';
    protected $primaryKey = 'user_id';

    /**
     * Get the user record associated with the info.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id');
    }
}
