<?php

namespace App\Tests\Commission;

use App\DTO\TransactionDto;
use App\Service\Commission\CommissionProcessor;
use App\Service\Fetcher\UrlFetcherInterface;
use App\Tests\Fetcher\MockUrlFetcher;
use AutoMapperPlus\AutoMapperInterface;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CommissionProcessorTest extends TestCase
{
    private CommissionProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();

        $parameterBag = $this->createMock(ParameterBagInterface::class);
        $parameterBag->method('get')->willReturnMap([
            ['BINLIST_URL', 'https://lookup.binlist.net/'],
            ['CURRENCY_EXCHANGE_RATES_URL', 'https://developers.paysera.com/tasks/api/currency-exchange-rates'],
        ]);

        $urlFetcherMock = $this->createMock(UrlFetcherInterface::class);
        $urlFetcherMock
            ->method('fetchUrl')
            ->willReturnMap([
                ['https://lookup.binlist.net/45717360', MockUrlFetcher::BIN_LIST_ONE],
                ['https://lookup.binlist.net/516793', MockUrlFetcher::BIN_LIST_TWO],
                ['https://lookup.binlist.net/45417360', MockUrlFetcher::BIN_LIST_THREE],
                ['https://lookup.binlist.net/41417360', MockUrlFetcher::BIN_LIST_FOUR],
                ['https://lookup.binlist.net/4745030', MockUrlFetcher::BIN_LIST_FIVE],
                ['https://developers.paysera.com/tasks/api/currency-exchange-rates', MockUrlFetcher::RATES],
            ]);

        $mapper = $this->createMock(AutoMapperInterface::class);

        $this->processor = new CommissionProcessor($parameterBag, $urlFetcherMock, $mapper);
    }

    /**
     * @dataProvider transactionsList
     */
    public function testProcessTransaction(array $data)
    {
        $this->assertSame($data['expected'], $this->processor->processTransaction($data['dto']));
    }

    public function transactionsList(): Generator
    {
        yield [
            'data' => ['dto' => new TransactionDto('45717360', 100.00, 'EUR'), 'expected' => 1.0]
        ];
        yield [
            'data' => ['dto' => new TransactionDto('516793', 50.00, 'USD'), 'expected' => 0.44]
        ];
        yield [
            'data' => ['dto' => new TransactionDto('45417360', 10000.00, 'JPY'), 'expected' => 1.53]
        ];
        yield [
            'data' => ['dto' => new TransactionDto('41417360', 130.00, 'USD'), 'expected' => 2.3]
        ];
        yield [
            'data' => ['dto' => new TransactionDto('4745030', 2000.00, 'GBP'), 'expected' => 47.88]
        ];
    }
}
