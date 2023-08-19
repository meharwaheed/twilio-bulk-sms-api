<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Voip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Twilio\TwiML\VoiceResponse;

class VoipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['respondToIncomingCall']);
    }


    public function index(Request $request)
    {
        $per_page = $request->get('per_page', 10);

        $voips = Voip::whereUserId(auth()->user()->id)
            ->latest()
            ->paginate($per_page);

        return $this->respond($voips);
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
                'phone' => '+' . str_replace('+', '', $validated['phone']),
                'user_id' => auth()->user()->id
            ],
            ['file' => $validated['file']],
        );

        return $this->respond(data: $voip, message: 'Voip store successfully');
    }


    public function respondToIncomingCall(Request $request)
    {
        $response = new VoiceResponse;

        $voip = Voip::wherePhone($request->from)->first();
        if (isset($voip)) {
            $response->play($voip->file_path);
            return $response;
        } else {
            return $this->error(message: 'Voip not found');
        }
    }

    /**
     * Delete Specific voip from database
     *
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $voip = Voip::findOrFail($id);
        if (Storage::exists($voip->file)) {
            Storage::disk(env('STORAGE_DISK', 'public'))->delete($voip->file);
        }
        $voip->delete();

        return $this->respond($voip, 'Voip delete successfully');
    }
}
