<?php

namespace App\DTO\Mapper;

use App\DTO\TransactionDto;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

class TransactionDtoMapper implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping('array', TransactionDto::class)
            ->forMember('bin', function ($source) {
                return $source['bin'];
            })
            ->forMember('amount', function ($source) {
                return (float)$source['amount'];
            })
            ->forMember('currency', function ($source) {
                return $source['currency'];
            })
        ;
    }
}
