<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use App\Models\Compain;



class CompainController extends Controller
{
    public function store()
    {

    }


    private function importCompains($file)
    {
        LazyCollection::make(function () {
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
            foreach($chunk as $item) {
                Compain::updateOrCreate(['title' => $item[0]]);
            }
        });
    }
}
