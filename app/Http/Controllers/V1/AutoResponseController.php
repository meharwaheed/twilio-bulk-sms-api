<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\AutoResponse;
use App\Models\CampaignNumber;
use App\Models\OptOut;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AutoResponseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['autoResponder']);
    }

    /**
     * Get auto response
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $per_page = $request->get('per_page', 10);

        $auto_response = AutoResponse::whereUserId(auth()->user()->id)
            ->latest()
            ->paginate($per_page);

        return $this->respond($auto_response);
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
                'phone' => "+" . str_replace('+', '', $validated['phone']),
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

        Log::info($request);

        $request->validate(['from' => 'required']);

        try {
            $outKeyword = OptOut::whereOutKeywords($request->user_message)->first();
            $inKeyword = OptOut::whereInKeywords($request->user_message)->first();

            isset($outKeyword) && CampaignNumber::wherePhone($request->from)->update(['is_active' => 0]);
            isset($inKeyword) && CampaignNumber::wherePhone($request->from)->update(['is_active' => 1]);


            /**
             * Finding auto response message for incoming sms & send auto response to incoming number
             */
            $autoResponse = AutoResponse::wherePhone($request->from)->first();
            Log::info($autoResponse);
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


    /**
     * Delete Specific auto response from database
     *
     * @param $id
     * @return JsonResponse
     */
    public function delete($id) {
        $auto_response = AutoResponse::findOrFail($id);
        $auto_response->delete();

        return $this->respond($auto_response, 'Auto response delete successfully');
    }
}
