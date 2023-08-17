<?php

namespace App\Console\Commands;

use App\Jobs\scheduleBulkSms;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        Log::info("Send Scheduled SMS Command is Working");
        $campaigns = Campaign::whereIsSchedule(true)
            ->whereStatus('pending')
            ->whereDate('converted_date', '<=', Carbon::now())
            ->count();
        if($campaigns > 0) {
            scheduleBulkSms::dispatch();
        }
        return Command::SUCCESS;
    }
}
