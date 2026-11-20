<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImageStorage
{
    /** @return array{location: string, file_id: ?string} */
    public function store(UploadedFile $file, string $folder): array
    {
        $privateKey = config('services.imagekit.private_key');

        if (config('services.imagekit.driver') !== 'imagekit') {
            $path = $file->store($folder, 'public');

            return ['location' => $path, 'file_id' => null];
        }

        if (! $privateKey) {
            throw new \RuntimeException('ImageKit is enabled but IMAGEKIT_PRIVATE_KEY is not configured.');
        }

        $fileName = Str::uuid().'.'.$file->guessExtension();
        $response = Http::timeout(30)
            ->withBasicAuth($privateKey, '')
            ->attach('file', $file->getContent(), $fileName, [
                'Content-Type' => $file->getMimeType(),
            ])
            ->post('https://upload.imagekit.io/api/v1/files/upload', [
                'fileName' => $fileName,
                'folder' => '/'.$folder,
                'useUniqueFileName' => 'false',
            ])
            ->throw()
            ->json();

        return [
            'location' => $response['url'],
            'file_id' => $response['fileId'],
        ];
    }

    public function delete(?string $location, ?string $fileId = null): void
    {
        if (! $location) {
            return;
        }

        $privateKey = config('services.imagekit.private_key');

        if ($fileId && $privateKey) {
            try {
                $response = Http::timeout(15)
                    ->withBasicAuth($privateKey, '')
                    ->delete('https://api.imagekit.io/v1/files/'.rawurlencode($fileId));

                if ($response->failed()) {
                    Log::warning('ImageKit did not delete an image.', [
                        'file_id' => $fileId,
                        'status' => $response->status(),
                    ]);
                }
            } catch (Throwable $exception) {
                Log::warning('ImageKit image deletion failed.', [
                    'file_id' => $fileId,
                    'message' => $exception->getMessage(),
                ]);
            }

            return;
        }

        Storage::disk('public')->delete($location);
    }

    public function url(?string $location): ?string
    {
        if (! $location) {
            return null;
        }

        if (Str::startsWith($location, ['https://', 'http://'])) {
            return $location;
        }

        return Storage::disk('public')->url($location);
    }
}
