<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Voip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Twilio\TwiML\VoiceResponse;

class VoipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['respondToIncomingCall']);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'file' => 'required|file|mimes:mpeg,mp3,3gp,ogg,wav'
        ]);

        $existingVoip = Voip::wherePhone($validated['phone'])->first();
        if (isset($existingVoip)) {
            Storage::disk(env('STORAGE_DISK', 'public'))->delete($existingVoip->file);
        }

        if ($request->hasFile('file')) {
            $path = 'voips/'.rand(1000, 9999) . $request->file->getClientOriginalName();
            Storage::disk(env('STORAGE_DISK', 'public'))->put($path, file_get_contents($request->file));
            $validated['file'] = $path;
        }
        $voip = Voip::updateOrCreate(
            [
                'phone' => '+' . str_replace('+', '', $validated['phone']),
                'user_id' => auth()->user()->id
            ],
            ['file' => $validated['file']],
        );

        return $this->respond(data: $voip, message: 'Voip store successfully');
    }


    /**
     * Sample Request
     * @param Request $request
     * @return JsonResponse|VoiceResponse
     *
     * {
     * "AccountSid": "ACdbb8fc72130b33147183886b3080c10b",
     * "ApiVersion": "2010-04-01",
     * "CallSid": "CAe965ea5c9d78ed096d52b08c2306a200",
     * "CallStatus": "ringing",
     * "CallToken": "%7B%22parentCallInfoToken%22%3A%22eyJhbGciOiJFUzI1NiJ9.eyJjYWxsU2lkIjoiQ0FlOTY1ZWE1YzlkNzhlZDA5NmQ1MmIwOGMyMzA2YTIwMCIsImZyb20iOiIrOTIzMDc3MDIwMTYzIiwidG8iOiIrMTQ3MDUxNjI1MTgiLCJpYXQiOiIxNjkyMzk5MDgwIn0.spjrBeaFgzN3Uo82RI8sJ6VezQwr8kySuh6Qz9KlTrlLRSW8s8KX9fQt64P6mo4CJyZzp1h0Q0f5KJT3GNTepg%22%2C%22identityHeaderTokens%22%3A%5B%5D%7D",
     * "Called": "+14705162518",
     * "CalledCity": "ATLANTA",
     * "CalledCountry": "US",
     * "CalledState": "GA",
     * "CalledZip": null,
     * "Caller": "+923077020163",
     * "CallerCity": null,
     * "CallerCountry": "PK",
     * "CallerState": null,
     * "CallerZip": null,
     * "Direction": "inbound",
     * "From": "+923077020163",
     * "FromCity": null,
     * "FromCountry": "PK",
     * "FromState": null,
     * "FromZip": null,
     * "To": "+14705162518",
     * "ToCity": "ATLANTA",
     * "ToCountry": "US",
     * "ToState": "GA",
     * "ToZip": null
     * }
     */
    public function respondToIncomingCall(Request $request)
    {
        $response = new VoiceResponse;

        $voip = Voip::wherePhone($request->To)->first();
        if (isset($voip)) {
            $response->play($voip->file_path);
            return $response;
        } else {
            return $this->error(message: 'Voip not found');
        }
    }
}
