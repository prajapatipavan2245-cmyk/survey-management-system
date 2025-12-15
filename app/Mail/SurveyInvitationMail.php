<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SurveyInvitationMail extends Mailable
{
    public $survey;

    public function __construct($survey)
    {
        $this->survey = $survey;
    }

    public function build()
    {
        return $this->subject('Survey Invitation')
            ->view('emails.survey_invitation');
    }
}
