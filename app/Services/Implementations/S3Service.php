<?php

namespace App\Services\Implementations;

use App\Services\Contracts\S3ServiceInterface;
use Illuminate\Support\Facades\Storage;
use Exception;

class S3Service implements S3ServiceInterface
{
    public function upload($file, string $folder): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename  = $folder . '/' . uniqid() . '_' . time() . '.' . $extension;

        Storage::disk('s3')->put($filename, file_get_contents($file), 'public');

        return Storage::disk('s3')->url($filename);
    }

    public function delete(string $url): void
    {
        try {
            $path = parse_url($url, PHP_URL_PATH);
            $path = ltrim($path, '/');
            Storage::disk('s3')->delete($path);
        } catch (Exception $e) {
            // Si falla el borrado en S3 no interrumpimos el flujo
            // Se loggea en MongoDB
        }
    }
}