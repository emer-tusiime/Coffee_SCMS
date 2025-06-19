<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendStakeholderReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;
    protected $stakeholders;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Report $report
     * @param \Illuminate\Support\Collection|\App\Models\User[] $stakeholders
     *        (e.g., User::where('role', 'stakeholder')->get())
     */
    public function __construct(Report $report, $stakeholders)
    {
        $this->report = $report;
        $this->stakeholders = $stakeholders;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->stakeholders as $stakeholder) {
            Mail::to($stakeholder->email)->send(new \App\Mail\StakeholderReportMail($this->report, $stakeholder));
        }
    }
}