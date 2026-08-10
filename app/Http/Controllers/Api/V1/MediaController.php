<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class MediaController extends Controller
{
    public function show(string $path): BinaryFileResponse|Response
    {
        if (request()->isMethod('OPTIONS')) {
            return response('', 204, $this->corsHeaders());
        }

        $path = str_replace(['..', '\\'], ['', '/'], $path);
        $fullPath = storage_path('app/public/'.$path);

        if (! is_file($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath, array_merge($this->corsHeaders(), [
            'Cache-Control' => 'public, max-age=3600',
        ]));
    }

    /** @return array<string, string> */
    private function corsHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'ngrok-skip-browser-warning, Accept, Content-Type, Authorization',
        ];
    }
}
