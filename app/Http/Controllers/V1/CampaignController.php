<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Services\CampaignService;
use Exception;

class CampaignController extends Controller
{
    public function __construct(private CampaignService $campaign_service){

    }
    /**
     * Import Campaigns CSVs
     */
    public function store(Request $request)
    {
        /**
         * Validate Request
         */
        $request->validate(['file' => 'required|mimes:csv']);

        try{

            /**
             * Import CSV Campaigns into DB
             */
            $this->campaign_service->importCampaigns($request->file);
            return $this->respond(Campaign::all(), 'Campaigns imported successfully');

        } catch(Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}
