<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\SurveyInvitationMail;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::where('user_id', auth()->id())->get();
        return view('surveyor.index', compact('surveys'));
    }

    public function create()
    {
        return view('surveyor.create');
    }

    public function store(Request $request)
    {
        $survey = Survey::create([
            'title'   => $request->title,
            'status'  => $request->status,
            'user_id' => auth()->id()
        ]);

        if ($request->questions) {
            foreach ($request->questions as $q) {
                if ($q != '') {
                    Question::create([
                        'survey_id' => $survey->id,
                        'question'  => $q
                    ]);
                }
            }
        }

        return redirect('/surveyor/surveys');
    }

    public function show($id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->status != 'final') {
            abort(403);
        }

        return view('participant.fill', compact('survey'));
    }

    public function responses($id)
    {
        $survey = Survey::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $responses = Response::where('survey_id', $id)->with(['answers.question', 'user'])->get();

        return view('surveyor.responses', compact('survey', 'responses'));
    }

    public function sendInvite($id)
    {
        $survey = Survey::findOrFail($id);

        Mail::to('participant@email.com')
            ->queue(new SurveyInvitationMail($survey));

        return back()->with('msg', 'Invitation email sent');
    }
}
