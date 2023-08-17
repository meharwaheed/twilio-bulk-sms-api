<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\AutoResponse;
use App\Models\CampaignNumber;
use App\Models\OptOut;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AutoResponseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['autoResponder']);
    }


    /**
     * Store auto response for phone number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $auto_response = AutoResponse::updateOrCreate(
            [
                'phone' => $validated['phone'],
                'user_id' => auth()->user()->id
            ],
            ['message' => $validated['message']]
        );

        return $this->respond(data: $auto_response, message: 'Auto response store successfully');
    }


    /**
     * Auto response to incoming sms
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function autoResponder(Request $request): JsonResponse
    {
        $request->validate(['user_message' => 'required', 'from' => 'required']);

        try {
            $outKeyword = OptOut::whereOutKeywords($request->user_message)->first();
            $inKeyword = OptOut::whereInKeywords($request->user_message)->first();

            isset($outKeyword) && CampaignNumber::wherePhone($request->from)->update(['is_active' => 0]);
            isset($inKeyword) && CampaignNumber::wherePhone($request->from)->update(['is_active' => 1]);


            /**
             * Finding auto response message for incoming sms & send auto response to incoming number
             */
            $autoResponse = AutoResponse::wherePhone($request->from)->first();
            $twilioClient = $this->twilioClient();

            if (isset($autoResponse)) {
                $twilioClient->messages->create($request->from, [
                    "body" => $autoResponse->message,
                    "from" => env('TWILIO_FROM_NUMBER')
                ]);

                return $this->respond(message: 'success');
            }

            return $this->error(message: 'auto response not found');

        } catch (\Exception $e) {
            return $this->error(message: $e->getMessage());
        }
    }
}
