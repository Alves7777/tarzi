<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class MediaController extends Controller
{
    public function show(string $path): BinaryFileResponse|Response
    {
        $path = str_replace(['..', '\\'], ['', '/'], $path);
        $fullPath = storage_path('app/public/'.$path);

        if (! is_file($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
