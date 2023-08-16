<?php

namespace App\Console\Commands;

use App\Jobs\scheduleBulkSms;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduledSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:scheduled-sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Scheduled SMS';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $campaigns = Campaign::whereIsSchedule(true)
            ->whereStatus('pending')
            ->whereRaw('converted_date', '<=', Carbon::now())
            ->count();
        if($campaigns > 0) {
            scheduleBulkSms::dispatch();
        }
        return Command::SUCCESS;
    }
}
