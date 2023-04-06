<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:ugly-code-run',
    description: 'Base and ugly example of code from test task.',
)]
class UglyCodeRunCommand extends Command
{
    public function __construct(private readonly ParameterBagInterface $parameterBag, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::OPTIONAL, 'Transactions filename.')
        ;
    }

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

        foreach (explode("\n", file_get_contents($filePath)) as $row) {
            if (empty($row)) {
                break;
            }
            $p = explode(",", $row);
            $p2 = explode(':', $p[0]);
            $value[0] = trim($p2[1], '"');
            $p2 = explode(':', $p[1]);
            $value[1] = trim($p2[1], '"');
            $p2 = explode(':', $p[2]);
            $value[2] = trim($p2[1], '"}');

            $binResults = file_get_contents('https://lookup.binlist.net/' . $value[0]);
            if (!$binResults) {
                die('error!');
            }
            $r = json_decode($binResults);
            $isEu = $this->isEu($r->country->alpha2);

            $rate = @json_decode(
                file_get_contents($this->parameterBag->get('CURRENCY_EXCHANGE_RATES_URL')),
                true
            )['rates'][$value[2]];
            if ($value[2] == 'EUR' or $rate == 0) {
                $amntFixed = $value[1];
            }
            if ($value[2] != 'EUR' or $rate > 0) {
                $amntFixed = $value[1] / $rate;
            }

            echo $amntFixed * ($isEu == 'yes' ? 0.01 : 0.02);
            print "\n";
        }

        return Command::SUCCESS;
    }

    private function isEu($c): string
    {
        $result = false;
        switch ($c) {
            case 'AT':
            case 'BE':
            case 'BG':
            case 'CY':
            case 'CZ':
            case 'DE':
            case 'DK':
            case 'EE':
            case 'ES':
            case 'FI':
            case 'FR':
            case 'GR':
            case 'HR':
            case 'HU':
            case 'IE':
            case 'IT':
            case 'LT':
            case 'LU':
            case 'LV':
            case 'MT':
            case 'NL':
            case 'PO':
            case 'PT':
            case 'RO':
            case 'SE':
            case 'SI':
            case 'SK':
                $result = 'yes';
                return $result;
            default:
                $result = 'no';
        }
        return $result;
    }
}
