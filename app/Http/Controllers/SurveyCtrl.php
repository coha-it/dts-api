<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\SurveyFinishedData;

class SurveyCtrl extends Controller
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
        ]);
    }

    // Get the Allowed Surveys
    public function getAllowedSurveys(Request $request) {
        return json_encode(
            $request->user()->allowedSurveys()->toArray()
        );
    }

    // Get the Membering Surveys
    public function getMemberingSurveys(Request $request) {
        return json_encode(
            $request->user()->memberingSurveys()
        );
    }

    // Get the Membering and Fillable Surveys
    public function getFillableSurvey(Request $request) {
        return json_encode(
            $request
                ->user()
                ->fillableSurvey($request->id)
        );
    }

    // Get the Membering and Fillable Surveys
    public function getFirstSurveyFillable(Request $request) {
        return json_encode(
            $request
                ->user()
                ->fillableSurveys()
                ->first()
        );
    }

    public function updateOrCreateAnswer($question, $request) {
        // If requested Answer was found
        $reqAw              = $request->answer;
        $reqAw['user_id']   = Auth()->user()->id;
        unset($reqAw['answer_options']); // Remove Useless

        // If Question is not Skippable
        if(!$question->isSkippable()) {
            // Not Skippable
            $reqAw['skipped'] = 0;
        }

        // Update Or Create
        $answer = $question->usersAnswer()->updateOrCreate(
            ['id' => $reqAw['id'] ?? 0],
            $reqAw
        );

        // If Question is skippable AND skipped
        if($question->is_skippable && $answer->skipped) {
            // Answer is Skipped
            $this->syncWithOptions($answer);
        } else {
            // Create and De-/Re- Connect
            $this->syncWithOptions($answer, $request->answer['answer_options']); // Connect selected Options with Answer's Options
        }

        // REturn the Answer
        return $answer;
    }

    public function syncWithOptions($answer, $arr = []) {
        $answer->answer_options()->sync(
            array_column($arr, 'id')
        );
    }

    public function httpUpdateOrCreateAnswer(Request $request) {
        // Validate Data
        $request->validate([
            'survey_id' => 'required',
            'question_id' => 'required',
            'answer' => 'required'
        ]);

        // Find Variables like self or Survey
        $self       = $request->user();
        $survey     = $self->fillableSurvey($request->survey_id);
        $question   = $survey->question($request->question_id);

        // Update or Create the Answer
        $answer = $this->updateOrCreateAnswer($question, $request); // Update Answer

        // Variables
        return $question->usersAnswer()->find($answer->id)->toJson();
    }

    public function httpFinishSurvey(Request $request) {
        // Validate Data
        $request->validate([
            'survey_id' => 'required',
        ]);

        // Find Variables like self or Survey
        $self       = $request->user();
        $survey     = $self->fillableSurvey($request->survey_id);

        // Check if Answers count bigger or equal the questions
        if (
            $survey->questions()->count() > $survey->userAnswers()->count()
        ) return abort(403, 'Es müssen zuerst alle Fragen beantwortet werden');

        // Finish Survey
        $survey->finishSurvey();
        $survey->save();

        // Extra Survey Data
        $this->saveExtraSurveyData($request, $survey, $self);

        // Return Survey
        return $survey->toJson();
    }

    public function saveExtraSurveyData($request, $survey, $user)
    {
        SurveyFinishedData::updateOrCreate([
            'survey_id' => $survey->id,
            'user_id' => $user->id,
            'ip_v4' => $request->ip() ?? null,
            'ip_v6' => $request->getClientIp() ?? null,
            'navigator' => json_encode($request->navigator ?? null),
            'json_data' => json_encode($request->json_data ?? null),
        ]);
    }

    // // Get the Allowed Survey
    // public function getAllowedSurvey(Request $request) {
    //     // Validate
    //     $request->validate(
    //         ['id' => 'required|int|min:1']
    //     );

    //     // Search Survey
    //     $survey = $request->user()->allowedSurveys()->find($request->id);
    //     if($survey) {
    //         return $survey->getSelfWithRelations()->toJson();
    //     }
    // }

}
