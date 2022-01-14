<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Question;
use App\User;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class BackendStatisticCtrl extends Controller
{
    public function httpSurveysStatistics (Request $request) {
        $function = $request->statistic_id;
        if(method_exists($this, $function)) {
            return $this->$function($request);
        }
        return ["Error - No valid Statistics with Statistic-View: \"$request->statistic_id\" available"];
    }

    protected function getLimit (Request $request) {
        return $request->limit ?? null;
    }


    protected function answer_options (Request $request)
    {
        // Variables
        $limit  = $this->getLimit($request);
        $ids    = $this->getSelectedSurveysIds($request);

        // Blank SQL-Dump
        $answers = DB::table('surveys')->distinct()
            ->select(
                /* Survey-Data */
                'surveys.id AS survey_id',
                /* Get User-Data */
                'u_pans.pan AS pan',
                /* Get Users-Company / Department / Info */
                'u_companies.name AS company_name',
                'u_departments.name AS department_name',
                'u_locations.name AS location_name',

                /* Get Question */
                'questions.id AS question_id',
                'questions.format AS question_format',
                'questions.title AS question_title',
                'questions.subtitle AS question_subtitle,',
                'questions.description AS question_description',
                /* Answers */
                'answers.id AS answer_id',
                'answers.skipped AS answer_skipped',
                'answers.comment AS answer_comment',
                /* Get Question Options */
                'question_options.value AS option_value',
                'question_options.title AS option_title',
                'question_options.subtitle AS option_subtitle',
                'question_options.color AS option_color',
                'question_options.description AS option_desc'
            )

            /* From Answers*/
            ->from('answers')

            /* Get Users-Data */
            // LEFT JOIN users ON users.id = answers.user_id
            ->leftJoin('users', 'users.id', '=', 'answers.user_id')
            // LEFT JOIN u_pans ON u_pans.user_id = users.id
            ->leftJoin('u_pans', 'u_pans.user_id', '=', 'users.id')

            /* Get Location Department Company */
            // LEFT JOIN u_locations ON users.location_id = u_locations.id
            ->leftJoin('u_locations', 'users.location_id', '=', 'u_locations.id')
            // LEFT JOIN u_departments ON users.department_id = u_departments.id
            ->leftJoin('u_departments', 'users.department_id', '=', 'u_departments.id')
            // LEFT JOIN u_companies ON users.company_id = u_companies.id
            ->leftJoin('u_companies', 'users.company_id', '=', 'u_companies.id')

            /* Get Question-Data */
            // LEFT JOIN questions ON questions.id = answers.question_id
            ->leftJoin('questions', 'questions.id', '=', 'answers.question_id')

            /* Match Survey */
            // LEFT JOIN surveys ON surveys.id = questions.survey_id
            ->leftJoin('surveys', 'surveys.id', '=', 'questions.survey_id')

            /* Get Answer/and Question-Options */
            // LEFT OUTER JOIN answer_options ON answer_options.answer_id = answers.id
            ->join('answer_options', 'answer_options.answer_id', '=', 'answers.id', 'left outer')
            // LEFT OUTER JOIN question_options ON question_options.id = answer_options.option_id
            ->join('question_options', 'question_options.id', '=', 'answer_options.option_id', 'left outer')

            /* Where Statements*/
            // # surveys.id = 2
            // surveys.id IN ('1', '2', '3')
            ->whereIn('surveys.id', $ids)

            // AND users.id IS NOT NULL
            // # AND u_pans.pan = '6CCYBZ'
            // # AND u_pans.user_id = 11
            ->whereNotNull('users.id')

            // ORDER BY
            // u_pans.pan, questions.id
            ->orderBy('u_pans.pan', 'asc')
            ->orderBy('questions.id', 'asc')

            // LIMIT 100
            ->limit($limit ?? NULL)

            // ->where('status', '<>', 1)
            // ->groupBy('status')
            ->get();



        return [
            'surveys' => $this->getSelectedSurveys($request)->toArray(),
            'answers' => $answers,
        ];
    }

    protected function sql_query (Request $request)
    {
        // Variables
        $limit  = $this->getLimit($request);
        $ids    = $this->getSelectedSurveysIds($request);

        // Blank SQL-Dump
        $aStatistics = [
            'header' => [],
            'data' => []
        ];
        $aStatistics['data'] = DB::table('surveys')->distinct()
            ->select(
                /* Survey-Data */
                'surveys.id AS survey_id',
                /* Get User-Data */
                'u_pans.pan AS pan',
                /* Get Users-Company / Department / Info */
                'u_companies.name AS company_name',
                'u_departments.name AS department_name',
                'u_locations.name AS location_name',

                /* Get Question */
                'questions.id AS question_id',
                'questions.format AS question_format',
                'questions.title AS question_title',
                'questions.subtitle AS question_subtitle,',
                'questions.description AS question_description',
                /* Answers */
                'answers.skipped AS answer_skipped',
                'answers.comment AS answer_comment',
                /* Get Question Options */
                'question_options.value AS option_value',
                'question_options.title AS option_title',
                'question_options.subtitle AS option_subtitle',
                'question_options.description AS option_desc'
            )

            /* From Answers*/
            ->from('answers')

            /* Get Users-Data */
	        // LEFT JOIN users ON users.id = answers.user_id
            ->leftJoin('users', 'users.id', '=', 'answers.user_id')
            // LEFT JOIN u_pans ON u_pans.user_id = users.id
            ->leftJoin('u_pans', 'u_pans.user_id', '=', 'users.id')

	        /* Get Location Department Company */
            // LEFT JOIN u_locations ON users.location_id = u_locations.id
            ->leftJoin('u_locations', 'users.location_id', '=', 'u_locations.id')
            // LEFT JOIN u_departments ON users.department_id = u_departments.id
            ->leftJoin('u_departments', 'users.department_id', '=', 'u_departments.id')
            // LEFT JOIN u_companies ON users.company_id = u_companies.id
            ->leftJoin('u_companies', 'users.company_id', '=', 'u_companies.id')

	        /* Get Question-Data */
            // LEFT JOIN questions ON questions.id = answers.question_id
            ->leftJoin('questions', 'questions.id', '=', 'answers.question_id')

	        /* Match Survey */
            // LEFT JOIN surveys ON surveys.id = questions.survey_id
            ->leftJoin('surveys', 'surveys.id', '=', 'questions.survey_id')

	        /* Get Answer/and Question-Options */
            // LEFT OUTER JOIN answer_options ON answer_options.answer_id = answers.id
            ->join('answer_options', 'answer_options.answer_id', '=', 'answers.id', 'left outer')
            // LEFT OUTER JOIN question_options ON question_options.id = answer_options.option_id
            ->join('question_options', 'question_options.id', '=', 'answer_options.option_id', 'left outer')

            /* Where Statements*/
            // # surveys.id = 2
            // surveys.id IN ('1', '2', '3')
            ->whereIn('surveys.id', $ids)

            // AND users.id IS NOT NULL
            // # AND u_pans.pan = '6CCYBZ'
            // # AND u_pans.user_id = 11
            ->whereNotNull('users.id')

            // ORDER BY
            // u_pans.pan, questions.id
            ->orderBy('u_pans.pan', 'asc')
            ->orderBy('questions.id', 'asc')

            // LIMIT 100
            ->limit($limit ?? NULL)

            // ->where('status', '<>', 1)
            // ->groupBy('status')
            ->get();

        // Build Head
        $aStatistics['header'] = array_keys((array) $aStatistics['data'][0]);

        return $aStatistics;
    }

    protected function user_table (Request $request) {
        // Variables
        $limit  = $this->getLimit($request);
        $ids    = $this->getSelectedSurveysIds($request);

        $statistics = [
            'surveys' => []
        ];

        foreach ($ids as $i => $val) {

            // Build Data
            $id = $ids[$i];
            $survey = $request->user()->allowedSurveys()->find($id)->getSelfWithRelations();

            // Build Response Array
            $statistics['surveys'][$id] = [
                'id' => $survey->id,
                'title' => $survey->title,
                'author' => $survey->author,
                'desc_short' => $survey->desc_short,
                'questions' => [],
                'answers' => []
            ];

            // Build Head / Questions
            $statistics['surveys'][$id]['questions'][] = [
                'name' => 'username',
                'field' => 'username',
                'label' => 'username',
                'sortable' => true
            ];
            foreach ($survey->questions as $question) {
                $statistics['surveys'][$id]['questions'][] = [
                    'name' => $question->title,
                    'field' => $question->title,
                    'label' => $question->subtitle,
                    'description' => $question->description,
                    'sortable' => true
                ];
            }

            // Build Data / Body / Answers


            // 1. Go throug all Users
            foreach ($survey->usersFinished as $finishedSurvey) {
                // Empty Array
                $user = $finishedSurvey->user;
                $arr = [
                    'username' => $user->pan->pan
                ];

                // 2. Go through all Questions Titles
                foreach (Question::with('answers')->where('survey_id', $finishedSurvey->survey_id)->get() as $question) {
                    // 3. Set for each title the value
                    $answer = $question->answers->where('user_id', '=', $finishedSurvey->user_id)->first();
                    $aAnswer = [];

                    foreach ($answer->answer_options as $i => $option) {
                        $sAnswer = '';
                        $title = $option->title;
                        $subtitle = $option->subtitle;
                        $description = $option->description;

                        $sAnswer .= $title;

                        if($subtitle || $description) {

                            $sAnswer .= ' (';
                            $sAnswer .= $subtitle;
                            $sAnswer .= $description ? ', ' . $description : '';
                            $sAnswer .= ')';
                        }

                        array_push($aAnswer, $sAnswer);
                    }

                    if($comment = $answer->comment) {
                        array_push($aAnswer, ' (Kommentar): "' . $comment .'" ');
                    }


                    $arr[$question['title']] = join(", ", $aAnswer); //->comment;
                }

                // Push Array to new Data
                $statistics['surveys'][$id]['answers'][] = $arr;
            }

            // Go Through all Users
            // Go Through all Questions
            // foreach ($survey->questions as $question)
            // {
//
                // Go Through all Answers
                // foreach (Question::with('answers')->find($question['id'])->answers as $answer)
                // {
                    // echo $answer->comment . "\n";
                // }
            // }

        }

        return $statistics;
    }

    protected function csv_type (Request $request) {
        // Variables
        $limit  = $this->getLimit($request);
        $ids    = $this->getSelectedSurveysIds($request);

        $statistics = [
            "data" => [],
            "header" => []
        ];

        foreach ($ids as $i => $val) {
            $id = $ids[$i];
            $survey = $request
                ->user()
                ->allowedSurveys()
                ->find($id)
                ->getSelfWithRelations()
            ;

            // echo "Survey: ". $survey['id'] . "\n";
            foreach ($survey->questions->toArray() as $j => $q) {
                $question = Question::with('answers')->find($q['id']);
                $answers = $question->answers;

                $questionKey = $question['title']." (#".$question['id'].")";

                $statistics['data'][$questionKey] = [];
                // $statistics['header'][] = [
                //     'name' => $questionKey,
                //     'label' => $questionKey,
                //     'field' => $questionKey,
                //     'sortable' => true,
                //     'align' => 'left'
                // ];

                // Build Header
                if ($j === 0) {
                    foreach ($answers as $k => $answer) {
                        $statistics['header'][] = " User" .$answer['user_id'];
                    }
                }


                // echo "  Frage: ". $questionKey . "\n";
                foreach ($answers as $k => $answer) {
                    $user = User::find($answer['user_id']);
                    $userAnswer = "Nutzer: (".$user->pan->pan.")";
                    // echo "    " . $userAnswer . "\n";

                    $statistics['data'][$questionKey][$userAnswer] = "";
                    $answerContent = "";

                    if ($answer['skipped']) {
                        // Skipped
                        $answerContent = "Übersprungen";
                        // echo "      -".$answerContent."\n";
                    } else {
                        // Not skipped
                        if ($options = $answer['answer_options']) {
                            foreach ($options as $l => $option) {
                                $awnsr = $option['title'] . " - " . $option['subtitle'];
                                // echo "      -answer_options: " . $awnsr . "\n";
                                $answerContent .= $awnsr;
                            }
                        }

                        if ($comment = $answer['comment']) {
                            $cmnt = "Comment: " . $comment;
                            // echo "      -" . $cmnt . "\n";
                            $answerContent .= $comment;
                        }
                    }



                    $statistics['data'][$questionKey][$userAnswer] = $answerContent;
                }
            }

        }


        return $statistics;
    }

    protected function search($a, $b) {
        return strpos(strtolower($a), strtolower($b)) !== false;
    }

    protected function getSelectedSurveysIds ($request) {
        return $this->getSelectedSurveys($request)->pluck('id')->toArray();
    }

    protected function getSelectedSurveys ($request) {
        return $this->getSurveys($request)->whereIn('id', $request->survey_ids);
    }

    protected function getSurveys(Request $request) {
        return $request->user()->allowedSurveys();
    }

    protected function httpFilteredSurveys(Request $request) {
        $search = $request->search;
        return $this->getSurveys($request)
                ->filter(function ($value, $key) use ($search) {
                    if(!$search) return true;

                    switch ($search) {
                        case $value->id == $search:
                        case $this->search($value->title, $search):
                            return true;
                            break;

                        default:
                            return false;
                            break;
                    }
                })
                ->map
                ->only([
                    'id',
                    'title',
                    'desc_short',
                    'desc_long'
                ])
                ->take(5)
                ->toArray();
    }


}
