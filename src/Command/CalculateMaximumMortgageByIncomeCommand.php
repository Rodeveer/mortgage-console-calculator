<?php declare(strict_types=1);

namespace App\Command;

use App\Client\ClientInterface;
use App\DTO\FinancialQuestions;
use App\DTO\PersonalQuestions;
use App\Helper\MortgageQuestionHelper;
use DateTimeInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CalculateMaximumMortgageByIncomeCommand extends Command
{
    protected static string $name = 'calculate:maximumMortgageByIncome';
    protected static string $description = 'Calculates the maximum mortgage based on income';

    public function __construct(
        private readonly string                 $maximumByIncomeEndpoint,
        private readonly ClientInterface        $client,
        private readonly MortgageQuestionHelper $questionHelper,
    )
    {
        parent::__construct(static::$name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->questionHelper->io = new SymfonyStyle($input, $output);
        $io->title('Calculate your maximum mortgage');

        $personalQuestions = new PersonalQuestions;
        $financialQuestions = new FinancialQuestions;

        $this->questionHelper->askGeneralQuestions($personalQuestions);
        $this->questionHelper->askFinancialQuestions($financialQuestions);

        $queryData = $this->getBaseQueryData($personalQuestions, $financialQuestions);

        if ($io->confirm(
            question: 'Do you have a partner?',
            default: false,
        )) {
            $partnerQuestions = new PersonalQuestions;
            $financialPartnerQuestions = new FinancialQuestions;

            $this->questionHelper->askGeneralQuestions($partnerQuestions);
            $this->questionHelper->askFinancialQuestions($financialPartnerQuestions);

            $queryData['person'][] = $this->getPersonData($partnerQuestions, $financialPartnerQuestions);
            $queryData['private_lease_amount'] += $financialPartnerQuestions->privateLeaseCosts;
            $queryData['private_lease_duration'] += $financialPartnerQuestions->privateLeaseDuration;
            $queryData['notDeductible'] += $financialPartnerQuestions->nonDeductible;
            $queryData['groundRent'] += $financialPartnerQuestions->groundRent;
        }


        try {
            $result = $this->client->get(
                $this->maximumByIncomeEndpoint,
                array_merge(
                    $queryData,
                    [
                        'duration' => 360,
                        'percentage' => 1.501,
                        'rateFixation' => 10,
                    ]
                ),
            );

            $total = json_decode((string)$result->getContent(), false, 512, JSON_THROW_ON_ERROR);
            $io->writeln('Your total is: ' . $total->result);
        } catch (Exception $e) {
            $io->writeln('There went something wrong with parsing your data. Try again later');

            return COMMAND::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @return array{person: array{mixed}, private_lease_amount: float|int, private_lease_duration: int, notDeductible: float|int, groundRent: float|int}
     */
    protected function getBaseQueryData(PersonalQuestions $personalQuestions, FinancialQuestions $financialQuestions): array
    {
        return [
            'person' => [
                $this->getPersonData($personalQuestions, $financialQuestions),
            ],
            'private_lease_amount' => $financialQuestions->privateLeaseCosts,
            'private_lease_duration' => $financialQuestions->privateLeaseDuration,
            'notDeductible' => $financialQuestions->nonDeductible,
            'groundRent' => $financialQuestions->groundRent,
        ];
    }

    /**
     * @return array{income: float, dateOfBirth: DateTimeInterface, alimony: float|int, loans: float|int, studentLoans: float|int}
     */
    protected function getPersonData(PersonalQuestions $personalQuestions, FinancialQuestions $financialQuestions): array
    {
        return [
            'income' => $personalQuestions->totalGrossPayment,
            'dateOfBirth' => $personalQuestions->dateOfBirth,
            'alimony' => $financialQuestions->alimonyToPartnerCosts,
            'loans' => $financialQuestions->creditsCosts,
            'studentLoans' => $financialQuestions->studentDebtCosts,
        ];
    }
}
