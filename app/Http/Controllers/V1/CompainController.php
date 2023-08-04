<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use App\Models\Compain;
use App\Models\CompainNumber;


class CompainController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv']);
        $this->importCompains($request->file);
        return $this->respond(Compain::all(), 'Compains imported successfully');
    }


    /**
     * import csv to databas
     */
    private function importCompains($file)
    {
        LazyCollection::make(function () use ($file) {
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
        ->each(function (LazyCollection $chunk) {
            $records = $chunk->map(function ($row) {
              return [
                  "title" => $row[0],
                  "phone" => $row[1],
              ];
            })->toArray();

            foreach($records as $row) {
                $compain = Compain::updateOrCreate(['title' => $row['title']]);
                $compain_number = CompainNumber::updateOrCreate(['compain_id' => $compain->id, 'phone' => $row['phone']]);
            }
        });
    }
}
