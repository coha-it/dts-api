<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Awnser extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'awnsers';
    protected $dates = ['deleted_at'];
    protected $with = ['awnser_options'];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'updated_at',
        'created_at',
        'deleted_at'
    ];

    /**
     * Relations
     */

    // Survey
    public function survey()
    {
        return $this->belongsTo('App\Survey');
    }

    // Survey
    public function user()
    {
        return $this->hasOne('App\User');
    }

    /**
     * Question
     */
    public function question()
    {
        return $this->hasOne('App\Question');
    }

    /**
     * Question
     */
    public function awnser_options()
    {
        return $this->belongsToMany('App\QuestionOption', 'awnser_options', 'awnser_id', 'option_id');
    }


    // public function delete()
    // {
    //     // delete all related selected options
    //     foreach ($this->options as $option) {
    //         $option->delete();
    //     }

    //     // delete the question
    //     return parent::delete();
    // }

}
