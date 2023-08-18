<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\CampaignNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwilioCallBackController extends Controller
{
    /**
     * Change SMS Delivery Status
     * @param Request $request
     * @param $compaign_id
     * @return string
     * Example Request with Failed Status
     * {
     * "ErrorCode": "21408",
     * "SmsSid": "SMc0c172f34fad9c96438ccd4e93c1fa01",
     * "SmsStatus": "failed",
     * "MessageStatus": "failed",
     * "To": "+923014313201",
     * "MessagingServiceSid": "MGd056dcd468c3ce12c4a80ee3d06d1737",
     * "MessageSid": "SMc0c172f34fad9c96438ccd4e93c1fa01",
     * "AccountSid": "ACdbb8fc72130b33147183886b3080c10b",
     * "From": "+14705162518",
     * "ApiVersion": "2010-04-01"
     * }
     */
    public function changeSMSDeliveryStatus(Request $request, $compaign_id) {
        Log::info($request);
        $request->validate(['SmsStatus' => 'required', 'To' => 'required']);

        CampaignNumber::whereCampaignId($compaign_id)
            ->where('phone',$request->To)
            ->update(['status' => $request->SmsStatus ]);

        return "SMS Delivery Status Updated Successfully";
    }
}
