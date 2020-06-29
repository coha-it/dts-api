<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyFinishedData extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'survey_finished_data';
    protected $primaryKey = 'id';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $visible = ['id'];
    protected $protected = ['id'];
    protected $guarded = ['id', 'updated_at', 'created_at', 'deleted_at'];

    /**
     * Get the user record associated with the info.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'user_id');
    }

    /**
     * Get the user record associated with the info.
     */
    public function survey()
    {
        return $this->hasOne('App\Survey', 'survey_id');
    }
}
