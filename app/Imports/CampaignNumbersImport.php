<?php

namespace App\Imports;

use App\Models\CampaignNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CampaignNumbersImport implements ToModel, WithChunkReading, WithStartRow
{

    private $campaign_id;
    public function  __construct($campaign_id)
    {
        $this->campaign_id = $campaign_id;
    }


    /**
    * @param array $row
    */
    public function model(array $row)
    {
        return new CampaignNumber([
            'campaign_id' => $this->campaign_id,
            'phone' => $row[0],
        ]);
    }


    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}
