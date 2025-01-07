<?php declare(strict_types=1);

namespace App\Command;

use App\Client\ClientInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CalculateMaximumMortgageByValueCommand extends Command
{
    protected static string $name = 'calculate:maximumMortgageByValue';
    protected static string $description = 'Calculates the maximum mortgage base on value';

    public function __construct(
        private readonly string          $maximumByHouseValueEndpoint,
        private readonly ClientInterface $client,
    )
    {
        parent::__construct(static::$name);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Calculate your maximum mortgage by value');

        try {
            $result = $this->client->get(
                endpoint: $this->maximumByHouseValueEndpoint,
                parameters: [
                    'objectvalue' => $io->ask('What is the property value?'),
                ]
            );

            $total = json_decode((string)$result->getContent(), false, 512, JSON_THROW_ON_ERROR);
            $io->writeln('Your total is: ' . $total->result);
        } catch (Exception) {
            $io->writeln('There went something wrong with parsing your data. Try again later');

            return COMMAND::FAILURE;
        }

        return Command::SUCCESS;
    }
}
