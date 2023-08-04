<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBulkSmsRequest;
use App\Models\BulkSms;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Services\CampaignService;
use Exception;
use Illuminate\Http\JsonResponse;

class CampaignController extends Controller
{
    public function __construct(private CampaignService $campaign_service)
    {

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
        $data = BulkSms::whereIsSchedule(true)->paginate($per_page);
        return $this->respond($data, 'success');
    }





    /**
     * Import Campaigns CSVs
     *
     * @param CreateBulkSmsRequest $request
     * @return JsonResponse
     */
    public function store(CreateBulkSmsRequest $request): JsonResponse
    {
        /**
         * Validate Request
         */
        $validated = $request->validated();

        try{
            if($request->hasFile('csv_file')) {
                $path = rand(0000, 9999). $request->csv_file->getClientOriginalName();
                $request->file('csv_file')->store($path, ['disk' => 'public']);
                $validated['csv_file'] = $path;
            }

            $bulk_sms = BulkSms::create($validated);

            /**
             * Import CSV Campaigns into DB
             */
            $this->campaign_service->importCampaigns($bulk_sms->id, $request->csv_file);
            return $this->respond(Campaign::all(), 'Campaigns imported successfully');

        } catch(Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}
