<?php

namespace App\Jobs;

use App\Actions\SendBulkSMS;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class scheduleBulkSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SendBulkSMS $sendBulkSMS)
    {
        $campaigns = Campaign::whereIsSchedule(true)
            ->whereStatus('pending')
            ->whereDate('converted_date', '<=', Carbon::now())
            ->get();

        if (isset($campaigns) && count($campaigns)) {
            foreach ($campaigns as $campaign) {
                $sendBulkSMS->send(campaign: $campaign);
            }
        }
    }
}
