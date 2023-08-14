<?php

namespace App\Imports;

use App\Models\OptOut;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class OptOutImport implements ToModel, ShouldQueue, WithStartRow, WithChunkReading
{
    /**
    * @param  $row
    */
    public function model(array $row)
    {
        return new OptOut([
            'in_keywords' => $row[0],
            'out_keywords' => $row[1],
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
