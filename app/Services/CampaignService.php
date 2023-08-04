<?php
namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignNumber;
use Illuminate\Support\LazyCollection;

class CampaignService {

    /**
     * import campaigns csv to database
     */
    public function importCampaigns($bulk_sms_id, $file)
    {
        LazyCollection::make(function () use ($bulk_sms_id, $file) {
            $handle = fopen($file, 'r');

            while (($line = fgetcsv($handle, 4096)) !== false) {
                $dataString = implode(", ", $line);
                $row = explode(',', $dataString);
                yield $row;
            }

            fclose($handle);
        })
        ->skip(1)
        ->chunk(1000)
        ->each(function (LazyCollection $chunk) use($bulk_sms_id) {
            $records = $chunk->map(function ($row) {
              return [
                  "title" => $row[0],
                  "phone" => $row[1],
              ];
            })->toArray();

            foreach($records as $row) {
                $campaign = Campaign::updateOrCreate(['title' => $row['title'], 'bulk_sms_id' => $bulk_sms_id]);
                CampaignNumber::updateOrCreate(['campaign_id' => $campaign->id, 'phone' => $row['phone']]);
            }
        });
    }
}
