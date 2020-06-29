<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';
    protected $dates = ['deleted_at'];
    protected $with = ['options'];

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

    /**
     * Relations
     */

    // Survey
    public function survey()
    {
        return $this->belongsTo('App\Survey');
    }

    public function awnsers()
    {
        return $this->hasMany('App\Awnser');
    }

    public function usersAwnser()
    {
        return $this
                ->hasOne('App\Awnser')
                ->where(
                    'user_id',
                    Auth()->user()->id
                );
    }

    public function options()
    {
        return $this->hasMany('App\QuestionOption')->orderBy('order');
    }

    public function question_options()
    {
        return $this->hasMany('App\QuestionOption')->orderBy('order');
    }

    public function delete()
    {
        // delete all related photos
        foreach ($this->options as $option) {
            $option->delete();
        }

        // delete the question
        return parent::delete();
    }

    public function isSkippable() {
        return $this->is_skippable || $this->format === 'info_only';
    }

}
