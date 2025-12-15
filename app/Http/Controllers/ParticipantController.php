<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\Response;
use App\Models\Answer;
use App\Mail\SurveySubmittedMail;
use Illuminate\Support\Facades\Mail;

class ParticipantController extends Controller
{
    public function dashboard()
    {
        $surveys = Survey::where('status', 'final')->get();
        return view('participant.dashboard', compact('surveys'));
    }

    public function submit(Request $request, $id)
    {
        // check already submitted
        $exists = Response::where('survey_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($exists) {
            return redirect('/participant/dashboard')
                ->with('msg', 'You have already submitted this survey.');
        }

        // create response
        $response = Response::create([
            'survey_id' => $id,
            'user_id'   => auth()->id()
        ]);

        // save answers
        if ($request->answer) {
            foreach ($request->answer as $qid => $ans) {
                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $qid,
                    'answer'      => $ans
                ]);
            }
        }

        // 🔔 SEND EMAIL TO SURVEYOR (QUEUE)
        $survey = Survey::find($id);

        Mail::to($survey->user->email)
            ->queue(new SurveySubmittedMail($survey));

        return redirect('/participant/dashboard')
            ->with('msg', 'Survey submitted successfully.');
    }
}
