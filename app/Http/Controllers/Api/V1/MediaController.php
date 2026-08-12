<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\AdvertisementMedia;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Entrega mídia dos anúncios (proxy S3 privado ou disco local).
 */
final class MediaController extends Controller
{
    public function show(string $path): BinaryFileResponse|StreamedResponse|Response
    {
        if (request()->isMethod('OPTIONS')) {
            return response('', 204, $this->corsHeaders());
        }

        $path = str_replace(['..', '\\'], ['', '/'], $path);
        $relative = ltrim($path, '/');

        $fullPath = storage_path('app/public/'.$relative);

        if (is_file($fullPath)) {
            return response()->file($fullPath, array_merge($this->corsHeaders(), [
                'Cache-Control' => 'public, max-age=3600',
            ]));
        }

        $disk = Storage::disk(AdvertisementMedia::disk());
        $contents = AdvertisementMedia::read($relative);

        if ($contents === null) {
            abort(404);
        }

        $mime = AdvertisementMedia::mimeType($relative)
            ?? $disk->mimeType($relative)
            ?? $this->guessMimeType($relative);

        return response($contents, 200, array_merge($this->corsHeaders(), [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=3600',
        ]));
    }

    private function guessMimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            default => 'application/octet-stream',
        };
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
