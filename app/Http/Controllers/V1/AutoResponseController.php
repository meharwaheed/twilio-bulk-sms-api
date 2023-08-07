<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\AutoResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AutoResponseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    /**
     * Store auto response for phone number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $auto_response = AutoResponse::updateOrCreate(
            [
                'phone' => $validated['phone'],
                'user_id' => auth()->user()->id
            ],
            ['message' => $validated['message']]
        );

        return $this->respond(data: $auto_response, message: 'Auto response store successfully');
    }
}
