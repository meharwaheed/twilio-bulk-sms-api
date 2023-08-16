<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Twilio\Rest\Client;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Get twilio client
     *
     * @return Client
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    protected function twilioClient()
    {
        $sid    = env('TWILIO_SID');
        $token  = env('TWILIO_TOKEN');

        return new Client( $sid, $token );
    }
    protected function respond($data = [], string $message = 'Request Successful', bool $success = true, int $code = 200) {
        return response()->json([
            'status' => $success,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message = 'Error', bool $success = false, int $code = 422) {
        return response()->json([
            'status' => $success,
            'message' => $message,
        ], $code);
    }
}
