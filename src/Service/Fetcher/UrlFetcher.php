<?php

declare(strict_types=1);

namespace App\Service\Fetcher;

use App\Exception\UrlFetchException;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UrlFetcher implements UrlFetcherInterface
{
    private HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    /**
     * @param string $url
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws UrlFetchException
     */
    public function fetchUrl(string $url): array
    {
        try {
            $response = $this->httpClient->request('GET', $url);
        } catch (Exception $e) {
            throw new UrlFetchException(sprintf('Error fetching URL %s: %s', $url, $e->getMessage()), 0, $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw new UrlFetchException(
                sprintf('Error fetching URL %s: HTTP status code %s', $url, $response->getStatusCode())
            );
        }

        return $response->toArray();
    }
}
