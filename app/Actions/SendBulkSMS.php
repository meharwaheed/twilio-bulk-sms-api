<?php

namespace App\Actions;

use App\Models\Campaign;
use Lorisleiva\Actions\Concerns\AsAction;
use Twilio\Rest\Client;

class SendBulkSMS
{
    use AsAction;

    public function send(Campaign $campaign)
    {
        $sid    = env( 'TWILIO_SID' );
        $token  = env( 'TWILIO_TOKEN' );
        $client = new Client( $sid, $token );

        foreach ($campaign->campaignNumbers as $number) {
            $client->messages->create(
                $number,
                [
                    'from' => $campaign->from_number,
                    'body' => $campaign->message,
                ]
            );
        }
        $campaign->update(['status' => 'delivered']);
    }
}
