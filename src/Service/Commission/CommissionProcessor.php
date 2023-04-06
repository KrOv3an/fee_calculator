<?php

declare(strict_types=1);

namespace App\Service\Commission;

use App\DTO\TransactionDto;
use App\Service\Fetcher\UrlFetcherInterface;
use App\Service\Helper\CountryHelper;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use AutoMapperPlus\AutoMapperInterface;

class CommissionProcessor
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly UrlFetcherInterface $urlFetcher,
        private readonly AutoMapperInterface $mapper,
    ) {
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface|UnregisteredMappingException
     */
    public function processTransactionsFromFile(string $filename): array
    {
        $result = [];
        $handle = fopen($filename, 'r');
        if (!$handle) {
            throw new LogicException('Error opening file: ' . $filename);
        }

        while (!feof($handle)) {
            $block = fread($handle, 8192); // read the file in blocks of 8192 bytes
            $lines = explode("\n", $block); // split the block into lines
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $transaction = json_decode($line, true);
                    if ($transaction === null) {
                        fclose($handle);
                        throw new InvalidArgumentException('Invalid JSON data in file: ' . $filename);
                    }

                    $transactionDto = $this->mapper->map($transaction, TransactionDto::class);
                    $result[] = $this->processTransaction($transactionDto);
                }
            }
        }

        fclose($handle);
        return $result;
    }

    /**
     * @param TransactionDto $transactionDto
     * @return float
     */
    public function processTransaction(TransactionDto $transactionDto): float
    {
        $binData = $this->urlFetcher->fetchUrl($this->parameterBag->get('BINLIST_URL') . $transactionDto->getBin());

        $rate = $this->urlFetcher->fetchUrl(
            $this->parameterBag->get('CURRENCY_EXCHANGE_RATES_URL')
        )['rates'][$transactionDto->getCurrency()];

        if ($transactionDto->getCurrency() === $transactionDto::CURRENCY_EUR || $rate === 0) {
            $amountFixed = $transactionDto->getAmount();
        } else {
            $amountFixed = $transactionDto->getAmount() / $rate;
        }

        $isEu = CountryHelper::isEu($binData['country']['alpha2']);
        if ($isEu) {
            $calculator = new CommissionCalculator(new EuCommissionStrategy());
        } else {
            $calculator = new CommissionCalculator(new DefaultCommissionStrategy());
        }
        $commission = $calculator->calculateCommission((float)$amountFixed);

        return round($commission, 2);
    }
}
