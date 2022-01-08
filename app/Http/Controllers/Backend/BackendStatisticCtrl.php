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

    public function getSurveysStatistics (Request $request) {
        switch ($request->type) {
            case 'csv_type':
                return $this->csvType($request);
                break;

            case 'user_table':
                return $this->userTable($request);
                break;
        
            case 'sql_query':
                return $this->sqlQuery($request);
                break;
            // case 4:
            //     return $this->statsFour($request);
            //     break;
            default:
                return array("Error - No valid Statistics with Statistic-ID: \"$request->type\" available");
                break;
        }
    }

    public function sqlQuery (Request $request)
    {
        $filter                 = is_array($request->filter) ? $request->filter : null;
        $aRequestingSurveyIds   = is_array($request->ids) ? $request->ids : [$request->ids];
        $aAllowedSurveyIds = [];
        $aStatistics = [
            'header' => [],
            'data' => []
        ];


        foreach ($aRequestingSurveyIds as $index => $val) {
            // Check if the Survey is in allowed Surveys
            if($request->user()->allowedSurveys()->find($val)) {
                array_push($aAllowedSurveyIds, $val);
            }
        }

        // Blank SQL-Dump
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
                /* Awnsers */
                'awnsers.skipped AS awnser_skipped',
                'awnsers.comment AS awnser_comment',
                /* Get Question Options */
                'question_options.value AS option_value',
                'question_options.title AS option_title',
                'question_options.subtitle AS option_subtitle',
                'question_options.description AS option_desc'
            )

            /* From Awnsers*/
            ->from('awnsers')

            /* Get Users-Data */
	        // LEFT JOIN users ON users.id = awnsers.user_id
            ->leftJoin('users', 'users.id', '=', 'awnsers.user_id')
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
            // LEFT JOIN questions ON questions.id = awnsers.question_id
            ->leftJoin('questions', 'questions.id', '=', 'awnsers.question_id')

	        /* Match Survey */
            // LEFT JOIN surveys ON surveys.id = questions.survey_id
            ->leftJoin('surveys', 'surveys.id', '=', 'questions.survey_id')

	        /* Get Awnser/and Question-Options */
            // LEFT OUTER JOIN awnser_options ON awnser_options.awnser_id = awnsers.id
            ->join('awnser_options', 'awnser_options.awnser_id', '=', 'awnsers.id', 'left outer')
            // LEFT OUTER JOIN question_options ON question_options.id = awnser_options.option_id
            ->join('question_options', 'question_options.id', '=', 'awnser_options.option_id', 'left outer')

            /* Where Statements*/
            // # surveys.id = 2
            // surveys.id IN ('1', '2', '3')
            ->whereIn('surveys.id', $aAllowedSurveyIds)

            // AND users.id IS NOT NULL
            // # AND u_pans.pan = '6CCYBZ'
            // # AND u_pans.user_id = 11
            ->whereNotNull('users.id')

            // ORDER BY
            // u_pans.pan, questions.id
            ->orderBy('u_pans.pan', 'asc')
            ->orderBy('questions.id', 'asc')

            // LIMIT 100
            ->limit($filter['limit'] ?? NULL)

            // ->where('status', '<>', 1)
            // ->groupBy('status')
            ->get();
        
        // Build Head
        $aStatistics['header'] = array_keys((array) $aStatistics['data'][0]);

        return $aStatistics;
    }

    public function userTable (Request $request) {
        $ids = is_array($request->ids) ? $request->ids : [$request->ids];
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
                'awnsers' => []
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

            // Build Data / Body / Awnsers


            // 1. Go throug all Users
            foreach ($survey->usersFinished as $finishedSurvey) {
                // Empty Array
                $user = $finishedSurvey->user;
                $arr = [
                    'username' => $user->pan->pan
                ];

                // 2. Go through all Questions Titles
                foreach (Question::with('awnsers')->where('survey_id', $finishedSurvey->survey_id)->get() as $question) {
                    // 3. Set for each title the value
                    $awnser = $question->awnsers->where('user_id', '=', $finishedSurvey->user_id)->first();
                    $aAwnser = [];

                    foreach ($awnser->awnser_options as $i => $option) {
                        $sAwnser = '';
                        $title = $option->title;
                        $subtitle = $option->subtitle;
                        $description = $option->description;

                        $sAwnser .= $title;

                        if($subtitle || $description) {

                            $sAwnser .= ' (';
                            $sAwnser .= $subtitle;
                            $sAwnser .= $description ? ', ' . $description : '';
                            $sAwnser .= ')';
                        }

                        array_push($aAwnser, $sAwnser);
                    }

                    if($comment = $awnser->comment) {
                        array_push($aAwnser, ' (Kommentar): "' . $comment .'" ');
                    }


                    $arr[$question['title']] = join(", ", $aAwnser); //->comment;
                }

                // Push Array to new Data
                $statistics['surveys'][$id]['awnsers'][] = $arr;
            }

            // Go Through all Users
            // Go Through all Questions
            // foreach ($survey->questions as $question)
            // {
//
                // Go Through all Awnsers
                // foreach (Question::with('awnsers')->find($question['id'])->awnsers as $awnser)
                // {
                    // echo $awnser->comment . "\n";
                // }
            // }

        }

        return $statistics;
    }

    public function csvType (Request $request) {
        $ids = is_array($request->ids) ? $request->ids : [$request->ids];
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
                $question = Question::with('awnsers')->find($q['id']);
                $awnsers = $question->awnsers;

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
                    foreach ($awnsers as $k => $awnser) {
                        $statistics['header'][] = " User" .$awnser['user_id'];
                    }
                }


                // echo "  Frage: ". $questionKey . "\n";
                foreach ($awnsers as $k => $awnser) {
                    $user = User::find($awnser['user_id']);
                    $userAwnser = "Nutzer: (".$user->pan->pan.")";
                    // echo "    " . $userAwnser . "\n";

                    $statistics['data'][$questionKey][$userAwnser] = "";
                    $awnserContent = "";

                    if ($awnser['skipped']) {
                        // Skipped
                        $awnserContent = "Übersprungen";
                        // echo "      -".$awnserContent."\n";
                    } else {
                        // Not skipped
                        if ($options = $awnser['awnser_options']) {
                            foreach ($options as $l => $option) {
                                $awnsr = $option['title'] . " - " . $option['subtitle'];
                                // echo "      -awnser_options: " . $awnsr . "\n";
                                $awnserContent .= $awnsr;
                            }
                        }

                        if ($comment = $awnser['comment']) {
                            $cmnt = "Comment: " . $comment;
                            // echo "      -" . $cmnt . "\n";
                            $awnserContent .= $comment;
                        }
                    }



                    $statistics['data'][$questionKey][$userAwnser] = $awnserContent;
                }
            }

        }


        return $statistics;
    }

    public function search($a, $b) {
        return strpos(strtolower($a), strtolower($b)) !== false;
    }

    public function getAllowedFilteredSurveys(Request $request) {
        $search = $request->search;
        return $request
                ->user()
                ->allowedSurveys()
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
