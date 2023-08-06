<?php
namespace App\Services;

use App\Models\Campaign;
use Illuminate\Support\LazyCollection;

class CampaignService {

    /**
     * import campaigns csv to database
     *
     * @param $campaign_id
     * @param $file
     * @return void
     */
    public function importCampaigns($campaign_id, $file): void
    {
        LazyCollection::make(function () use ($campaign_id, $file) {
            $handle = fopen($file, 'r');

            while (($line = fgetcsv($handle, 4096)) !== false) {
                $dataString = implode(", ", $line);
                $row = explode(',', $dataString);
                yield $row;
            }

            fclose($handle);
        })
        ->skip(1)
        ->chunk(500)
        ->each(function (LazyCollection $chunk) use($campaign_id) {
            $records = $chunk->map(function ($row) {
              return [
                  "phone" => $row[0],
              ];
            })->toArray();

            $campaign = Campaign::findOrFail($campaign_id);
            $campaign->campaignNumbers()->createMany($records);
        });
    }
}
