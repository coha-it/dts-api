<?php

namespace App\Http\Controllers\Backend;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Survey as Survey;
use App\Question as Question;

class BackendSurveyCtrl extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([
            'auth',
            'auth.user.email',
            'auth.user.can_create_surveys'
        ]);
    }

    // Get the Allowed Surveys
    public function getAllowedSurveys(Request $request) {
        return $request->user()->allowedSurveys()->toJson();
    }

    // Get the Allowed Survey
    public function getAllowedSurvey(Request $request) {
        // Validate
        $request->validate(
            ['id' => 'required|int|min:1']
        );

        // Search Survey
        $survey = $request->user()->allowedSurveys()->find($request->id);
        if($survey) {
            return $survey->getSelfWithRelations()->toJson();
        }
    }

    public function connectSurveyAndGroups($self, $survey, $aGroups) {
        // Go through requeste's User-Groups
        $aSync = [];
        foreach ($aGroups as $group)
        {
            // If Self has Rights
            if ( $self->groupsModerating->find($group['id']) ) {
                array_push($aSync, $group['id']);
            }
        }
        $survey->groups()->sync($aSync);
    }

    public function updateOrCreateQuestionOptions($bIsNew, $question, $reqOptions)
    {
        // Go Through all Requestet Question-Options
        foreach ($reqOptions as $j => $reqOption)
        {
            // Remove Dialog
            unset($reqOption['dialog']);

            // Request Option Id
            $reqOptionId = array_key_exists('id', $reqOption) ? $reqOption['id'] : null;

            // Create if New
            if($bIsNew) {
                unset($reqOptionId);
                $option = $question->options()->updateOrCreate($reqOption);
            }
            // Update if not
            else {
                $option = $question->options()->updateOrCreate(
                    ['id' => $reqOptionId],
                    $reqOption
                );
            }

            // Save Option
            $option->save();

        }
    }

    public function updateOrCreateQuestions($self, $survey, $reqQuestions)
    {
        // Go Through all Requested Questions
        foreach ($reqQuestions as $i => $reqQuestion)
        {
            // Update or Create the Question
            $tmp = $reqQuestion;
            $bIsNew = array_key_exists('is_new', $reqQuestion) ? $reqQuestion['is_new'] : false;

            // Unset
            unset($tmp['options']);
            unset($tmp['is_new']);

            // Req Question Id
            $reqQuestionId = array_key_exists('id', $reqQuestion) ? $reqQuestion['id'] : null;
            $questions = $survey->questions();

            // If Is New
            if($bIsNew) {
                unset($tmp['id']);
                $tmp['created_by'] = $self->id;
                $question = $questions->updateOrCreate($tmp);
            }
            else {
                $question = $questions->updateOrCreate(['id' => $reqQuestionId], $tmp);
            }

            // Save Question
            $question->save();

            // update or Create Options
            $this->updateOrCreateQuestionOptions( $bIsNew, $question, $reqQuestion['options'] ?? [] );
        }
    }

    public function updateOrCreateSurvey($self, $request)
    {
        // Get Request-Survey
        $reqSurvey = $request->survey;

        // Update
        if( $survey = $self->allowedSurveys()->find($reqSurvey['id'] ?? 0) ) {
            $survey->update($reqSurvey);
        }
        // Or Create
        else {
            $survey = $self->createdSurveys()->create($reqSurvey);
        }

        // Check use HTML
        $this->setUseHtml($self, $survey, $reqSurvey);

        // Connect survey with group
        $this->connectSurveyAndGroups($self, $survey, $reqSurvey['groups'] ?? []);

        // UpdateOrCreate the questions (and options)
        $this->updateOrCreateQuestions($self, $survey, $reqSurvey['questions'] ?? []);

        // Try to Delete some Questions Options
        $this->deleteQuestionOptions($survey, $request);

        // Try to Delete Some Questions
        $this->deleteQuestions($survey, $request);

        // If Delete Survey
        $this->tryDeleteSurvey($survey, $request);

        // Return the Survey
        return $survey;
    }

    // Set the Use of HTML by User-Rights
    public function setUseHtml($self, $survey, $reqSurvey) {
        $survey->use_html = $self->canUseHtml() && $reqSurvey['use_html'] ? 1 : 0;
        $survey->save();
    }

    // Update the Created Survey
    public function tryUpdateAllowedSurvey(Request $request)
    {
        // 1. Validate the Requests
        $request->validate([
            'survey' => 'required'
        ]);

        $reqSurvey = $request->survey;
        $self = $request->user();

        // If Activated
        if($reqSurvey['active']) {
            // Validate Start and End Datetime
            $request->validate([
                'survey.start_datetime' => 'required|date_format:Y-m-d H:i:s',
                'survey.end_datetime' => 'required|date_format:Y-m-d H:i:s'
            ]);
        }

        // 3. Check if Request has ID
        if(
            array_key_exists('id', $reqSurvey) &&
            $survey = $self->allowedSurveys()->find( $reqSurvey['id'])
        ) {
            // 3.1 If ID exists and survey found - Check if Survey is editable
            if($survey->isEditable())
            {
                $survey = $this->updateOrCreateSurvey($self, $request);
                return $survey->getSelfWithRelations()->toJson();
            }


            // Return Error
            return back()->withErrors('your error message');
        }

        // 3.2 If no ID is in Params
        else {
            //  - then create a survey
            $survey = $this->updateOrCreateSurvey($self, $request);
            return $survey->getSelfWithRelations()->toJson();
        }
    }

    public function deleteQuestions($survey, $req)
    {
        foreach ($survey->questions->find($req['delete_questions_ids'] ?? []) as $question)
        {
            $question->delete();
        }
    }

    public function tryDeleteSurvey($survey, $r)
    {
        $k = 'force_delete';

        if ($r[$k] === true) {
            $survey->delete();
        }
    }

    public function deleteQuestionOptions($survey, $req)
    {
        $aDeleteOptions = $req['delete_options_ids'] ?? [];

        // Go Through all Question-IDs and try to delete an deleted Option
        foreach ($survey->questions as $question)
        {
            // Try to Delete Options
            foreach($aDeleteOptions as $id)
            {
                $option = $question->options->find($id);
                if($option) $option->delete();
            }
        }
    }



}
