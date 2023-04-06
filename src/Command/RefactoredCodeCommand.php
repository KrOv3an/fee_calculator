<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Commission\CommissionProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'app:refactored-code',
    description: 'Refactored code from test task.',
)]
class RefactoredCodeCommand extends Command
{
    public function __construct(private readonly CommissionProcessor $commissionProcessor, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::OPTIONAL, 'Transactions filename.')
        ;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filename = $input->getArgument('filename');

        if ($filename) {
            $io->note(sprintf('You passed an argument: %s', $filename));
        }

        $kernel = $this->getApplication()->getKernel();
        $publicPath = $kernel->getProjectDir() . '/public/';
        $filePath = $publicPath . $filename;

        $res = $this->commissionProcessor->processTransactionsFromFile($filePath);

        foreach ($res as $v) {
            echo $v . PHP_EOL;
        }

        return Command::SUCCESS;
    }
}
