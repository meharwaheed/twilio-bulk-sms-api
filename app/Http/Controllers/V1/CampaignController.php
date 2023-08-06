<?php

namespace App\Http\Controllers\V1;

use App\Actions\SendBulkSMS;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBulkSmsRequest;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Services\CampaignService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    /**
     * @param CampaignService $campaign_service
     */
    public function __construct(private CampaignService $campaign_service)
    {
        $this->middleware('auth:sanctum');
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
        $data = Campaign::whereIsSchedule(true)->paginate($per_page);

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

            $campaign = Campaign::create($validated);

            /**
             * Import CSV Campaigns into DB
             */
            $this->campaign_service->importCampaigns($campaign->id, $request->csv_file);
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

}
