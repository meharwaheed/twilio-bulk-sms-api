<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Voip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class VoipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'file' => 'required|file|mimes:mpeg,mp3,3gp,ogg,wav'
        ]);

        $existingVoip = Voip::wherePhone($validated['phone'])->first();
        if (isset($existingVoip)) {
            Storage::disk(env('STORAGE_DISK', 'public'))->delete($existingVoip->file);
        }

        if ($request->hasFile('file')) {
            $path = 'voips/'.rand(1000, 9999) . $request->file->getClientOriginalName();
            Storage::disk(env('STORAGE_DISK', 'public'))->put($path, file_get_contents($request->file));
            $validated['file'] = $path;
        }
        $voip = Voip::updateOrCreate(
            [
                'phone' => $validated['phone'],
                'user_id' => auth()->user()->id
            ],
            ['file' => $validated['file']],
        );

        return $this->respond(data: $voip, message: 'Voip store successfully');
    }
}
