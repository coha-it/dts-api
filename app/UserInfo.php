<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserInfo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'u_infos';
    protected $primaryKey = 'user_id';

    /**
     * Get the user record associated with the info.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id');
    }
}
