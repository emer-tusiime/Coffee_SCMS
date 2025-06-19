<?php

namespace App\Mail;

use App\Models\Report;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StakeholderReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $report;
    public $stakeholder;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Report $report
     * @param \App\Models\User $stakeholder
     */
    public function __construct(Report $report, User $stakeholder)
    {
        $this->report = $report;
        $this->stakeholder = $stakeholder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Stakeholder Report')
            ->view('emails.stakeholder_report')
            ->with([
                'report' => $this->report,
                'stakeholder' => $this->stakeholder,
            ]);
    }
}