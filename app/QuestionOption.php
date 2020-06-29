<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionOption extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'question_options';
    protected $dates = ['deleted_at'];

    /**
     * Cast Column to specific Data-Type
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'array'
    ];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function awnser()
    {
        return $this->belongsTo('App\Awnser', 'awnser_options');
    }

    public function awnser_option()
    {
        return $this->hasMany('App\AwnserOption', 'option_id');
    }
}
