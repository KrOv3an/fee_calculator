<?php

declare(strict_types=1);

namespace App\Service\Fetcher;

interface UrlFetcherInterface
{
    public function fetchUrl(string $url): array;
}
