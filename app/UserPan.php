<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserPan extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'u_pans';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $visible = [
        'is_pan_user',
        'pan',

        'contact_mail',
        'last_mail_date',
        'last_mail_status',
        'import_comment',

        'created_at',
        'updated_at',
        'deleted_at',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $fillable = [
        'pan',
        'pin',

        'contact_mail',
        'last_mail_date',
        'last_mail_status',
        'import_comment',
    ];

    /**
     * Get the user record associated with the info.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id');
    }
}
