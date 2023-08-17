<?php

namespace App\Http\Controllers\V1;

use App\Actions\SendBulkSMS;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBulkSmsRequest;
use App\Imports\CampaignNumbersImport;
use App\Models\Campaign;
use App\Models\CampaignNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CampaignController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $per_page = $request->get('per_page', 10);
        $data = $this->getGraphData();

        $campaign_ids = Campaign::whereUserId(auth()->user()->id)
            ->where('from_number', 'like', '%' . $request->from_number . '%')
            ->pluck('id');

        $data['campaign_messages'] = CampaignNumber::whereIn('campaign_id', $campaign_ids)
            ->when($request->to_number, function ($query) use ($request) {
                return $query->where('phone', 'like', '%' . $request->to_number . '%');
            })
            ->when($request->status == 'Pending', function ($query) use ($request) {
                return $query->whereStatus($request->status);
            })
            ->with('campaign:id,blast_name,status,from_number')
            ->latest()
            ->paginate($per_page);

        return $this->respond($data);
    }


    /**
     * Get schedule sms
     *
     * @param Request $request
     * @return JsonResponse;
     */
    public function getScheduleSms(Request $request): JsonResponse
    {
        $per_page = $request->get('per_page', 10);
        $data = Campaign::whereUserId(auth()->user()->id)
            ->whereIsSchedule(true)
            ->latest()
            ->paginate($per_page);

        return $this->respond(data: $data, message: 'success');
    }


    /**
     * Import Campaigns CSVs & Sending bulk sms if sms is not scheduled
     *
     * @param CreateBulkSmsRequest $request
     * @param SendBulkSMS $sendBulkSMS
     * @return JsonResponse
     */
    public function store(CreateBulkSmsRequest $request, SendBulkSMS $sendBulkSMS): JsonResponse
    {
        /**
         * Validate Request
         */
        $validated = $request->validated();

        try{
            if($request->hasFile('csv_file')) {
                $path = 'campaigns/'.rand(0000, 9999). $request->csv_file->getClientOriginalName();
                Storage::disk(env('STORAGE_DISK', 'public'))->put($path, file_get_contents($request->csv_file));
                $validated['csv_file'] = $path;
            }
            $validated['user_id'] = auth()->user()->id;

            if($request->is_schedule) {
                $date = Carbon::parse($request->schedule_date, $request->timezone);
                $date->setTimezone(env('APP_TIMEZONE'));
                $validated['converted_date'] = $date;
            }

            $campaign = Campaign::create($validated);

            /**
             * Import CSV Campaigns into DB
             */

            Excel::import(new CampaignNumbersImport(campaign_id: $campaign->id), filePath: $request->file('csv_file'));
            $message = 'Bulk Sms scheduled successfully';

            /**
             * Sending Bulk Sms if not is_schedule
             */
            if (!$request->is_schedule){
                $sendBulkSMS->send(campaign: $campaign);
                $message = 'Bulk Sms send successfully';
            }

            return $this->respond(data: $campaign, message: $message);

        } catch(Exception $e) {
            return $this->error(message: $e->getMessage());
        }
    }

    /**
     * Get last seven days dates
     * @return array
     */
    private function getDatesArray(): array
    {
        $date = Carbon::today()->subDays(7);
        $dates = [];
        for ($i = 0; $i <= 7; $i++) {
            $dates[] = Carbon::parse($date)->addDays($i);
        }

        return $dates;
    }

    /**
     * Get reports graph
     * @return array
     */
    private function getGraphData(): array
    {
        $lastWeekDates = $this->getDatesArray();

        $data['delivered'] = [];
        $data['pending'] = [];
        $data['undelivered'] = [];

        foreach ($lastWeekDates as $key => $date) {
            $campaigns = Campaign::whereUserId(auth()->user()->id)
                ->whereStatus('delivered')
                ->whereDate('created_at', $date)
                ->get();

            $count = 0;
            foreach ($campaigns as $campaign) {
                $count += $campaign->campaignNumbers()->count();
            }
            $data['delivered'][] = $count > 1 ? $count : (end($data['delivered']) !== false ? end($data['delivered']) : 0);
        }

        foreach ($lastWeekDates as $key => $date) {
            $campaigns = Campaign::whereUserId(auth()->user()->id)
                ->whereStatus('pending')
                ->whereDate('created_at', $date)
                ->get();

            $count = 0;
            foreach ($campaigns as $campaign) {
                $count += $campaign->campaignNumbers()->count();
            }
            $data['pending'][] = $count > 1 ? $count : (end($data['pending']) !== false ? end($data['pending']) : 0);
        }

        foreach ($lastWeekDates as $key => $date) {
            $campaigns = Campaign::whereUserId(auth()->user()->id)
                ->whereStatus('undelivered')
                ->whereDate('created_at', $date)
                ->get();

            $count = 0;
            foreach ($campaigns as $campaign) {
                $count += $campaign->campaignNumbers()->count();
            }
            $data['undelivered'][] = $count > 1 ? $count : (end($data['undelivered']) !== false ? end($data['undelivered']) : 0);
        }

        return $data;
    }
}
