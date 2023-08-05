<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\AutoResponse;
use Illuminate\Http\Request;

class AutoResponseController extends Controller
{

    /**
     * Store auto response for phone number
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $auto_response = AutoResponse::create($validated);

        return $this->respond(data: $auto_response, message: 'Auto response store successfully');
    }
}
