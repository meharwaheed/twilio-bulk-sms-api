<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Imports\OptOutImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OptOutController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        Excel::import(new OptOutImport, $request->file('file'));

        return $this->respond(message: 'Opt outs imported successfully');
    }
}
