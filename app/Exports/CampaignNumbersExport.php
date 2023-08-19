<?php

namespace App\Exports;

use App\Models\CampaignNumber;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CampaignNumbersExport implements FromCollection, WithHeadings
{

    public function __construct(private $campaign_id)
    {

    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return  CampaignNumber::whereCampaignId($this->campaign_id)
            ->whereIsActive(1)
            ->select('phone')
            ->get()
            ->makeHidden(['updated_at_formatted']);
    }


    /**
     * @return string[]
     */
    public function headings(): array
    {
        return ['Phone'];
    }
}
