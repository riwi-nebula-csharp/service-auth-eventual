<?php

namespace App\Services\Contracts;

interface S3ServiceInterface
{
    public function upload($file, string $folder): string;
    public function delete(string $url): void;
}