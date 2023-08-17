<?php

namespace App\Actions;

use App\Models\Campaign;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Twilio\Rest\Client;

class SendBulkSMS
{
    use AsAction;

    public function send(Campaign $campaign)
    {
        $sid    = env( 'TWILIO_SID' );
        $token  = env( 'TWILIO_TOKEN' );
        $notify_sid = env('TWILIO_NOTIFY_SID');

        $client = new Client( $sid, $token );

        $members = [];
        foreach ($campaign->campaignNumbers as $phone) {
            $members[] = json_encode(["binding_type" => "sms", "address" => $phone->phone]);
        }

        $notification = $client->notify->v1->services($notify_sid)
            ->notifications->create([
                "toBinding" => $members,
                "body" => $campaign->message,
                'sms' => ['status_callback' => env('APP_URL_CALLBACK')."/sms-delivery-status-callback/". $campaign->id ]
            ]);

        $campaign->update(['status' => 'delivered']);

    }
}
