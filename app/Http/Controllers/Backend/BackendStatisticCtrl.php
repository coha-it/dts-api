<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Question;
use App\User;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class BackendStatisticCtrl extends Controller
{

    public function getSurveysStatistics (Request $request) {
        switch ($request->type) {
            case 1:
                # code...
                return $this->statsOne($request);
                break;

            case 2:
                return $this->statsTwo($request);
                break;
        }
    }

    public function statsTwo (Request $request) {
        $ids = is_array($request->ids) ? $request->ids : [$request->ids];
        $statistics = [
            'surveys' => []
        ];

        foreach ($ids as $i => $val) {

            // Build DAta
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

    public function statsOne (Request $request) {
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
