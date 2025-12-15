<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SurveySubmittedMail extends Mailable
{
    public $survey;

    public function __construct($survey)
    {
        $this->survey = $survey;
    }

    public function build()
    {
        return $this->subject('Survey Submitted')
            ->view('emails.survey_submitted');
    }
}
