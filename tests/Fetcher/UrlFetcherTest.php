<?php

namespace App\Tests\Fetcher;

use App\Exception\UrlFetchException;
use App\Service\Fetcher\UrlFetcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UrlFetcherTest extends TestCase
{
    protected function setUp(): void
    {
        $parameterBag = $this->createMock(ParameterBagInterface::class);
        $parameterBag->method('get')->willReturnMap([
            ['BINLIST_URL', 'https://lookup.binlist.net/'],
            ['CURRENCY_EXCHANGE_RATES_URL', 'https://developers.paysera.com/tasks/api/currency-exchange-rates'],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws UrlFetchException
     * @throws ClientExceptionInterface
     */
    public function testFetchUrlReturnsArray()
    {
        $url = "https://developers.paysera.com/tasks/api/currency-exchange-rates";

        $urlFetcher = new UrlFetcher();
        $result = $urlFetcher->fetchUrl($url);

        $this->assertIsArray($result);
    }
}
