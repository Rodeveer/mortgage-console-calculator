<?php declare(strict_types=1);

namespace App\Helper;

use App\DTO\FinancialQuestions;
use App\DTO\PersonalQuestions;
use App\Enum\LoanDebtSystemEnum;
use App\Enum\PaymentPeriodEnum;
use DateTime;
use RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;

class MortgageQuestionHelper
{
    public SymfonyStyle $io {
        get => $this->io;
        set (SymfonyStyle $value) {
            $this->io = $value;
        }
    }

    public function askGeneralQuestions(PersonalQuestions $personalQuestions): void
    {
        $personalQuestions->dateOfBirth = $this->io->ask(
            question: 'Date of birth (E.g. 01-01-1970)',
            validator: static fn(string|null $value) => ($value && ($parsedValue = DateTime::createFromFormat('d-m-Y', $value)) !== false)
                ? $parsedValue
                : throw new RuntimeException('Invalid birthday'),
        );

        $personalQuestions->grossPayment = $this->io->choice(
            question: 'Payment monthly or yearly',
            choices: array_map(
                static fn(PaymentPeriodEnum $enum) => $enum->value,
                PaymentPeriodEnum::cases(),
            ),
        );

        $personalQuestions->grossIncome = $this->io->ask(
            question: 'Gross income per ' . $personalQuestions->grossPayment?->value,
            default: '0',
        );

        $personalQuestions->hasHolidayPayment = $this->io->confirm(
            question: 'Holiday payment?',
            default: false,
        );

        $personalQuestions->hasExtraMonth = $this->io->confirm(
            question: 'Extra month?',
            default: false,
        );
    }

    public function askFinancialQuestions(FinancialQuestions $financialQuestions): void
    {
        $financialQuestions->isNHG = $this->io->confirm(
            question: 'Loan with NHG?',
        );

        if ($this->io->confirm(
            question: 'Student debt?',
            default: false,
        )) {
            $financialQuestions->studentDebtCosts = $this->io->ask(
                question: 'How much student debt?',
                default: '0',
            );

            $financialQuestions->studentDebtLoanSystem = $this->io->choice(
                question: 'Loan system since?',
                choices: array_map(
                    static fn(LoanDebtSystemEnum $enum) => $enum->value,
                    LoanDebtSystemEnum::cases(),
                ),
            );
        }

        if ($this->io->confirm(
            question: 'Alimony costs? (monthly)',
            default: false,
        )) {
            $financialQuestions->alimonyToPartnerCosts = $this->io->ask(
                question: 'How much alimony costs?',
                default: '0',
            );
        }

        if ($this->io->confirm(
            question: 'Credits costs?',
            default: false,
        )) {
            $financialQuestions->creditsCosts = $this->io->ask(
                question: 'How much credits costs?',
                default: '0',
            );
        }

        if ($this->io->confirm(
            question: 'Private lease costs?',
            default: false,
        )) {
            $financialQuestions->privateLeaseCosts = $this->io->ask(
                question: 'How much private lease costs?',
                default: '0',
            );

            $financialQuestions->privateLeaseDuration = $this->io->ask(
                question: 'Duration left?',
                default: '12',
            );
        }

        if ($this->io->confirm(
            question: 'Ground rent?',
            default: false,
        )) {
            $financialQuestions->groundRent = $this->io->ask(
                question: 'How much ground rent?',
                default: '0',
            );
        }

        if ($this->io->confirm(
            question: 'Have got non deductible amount?',
            default: false,
        )) {
            $financialQuestions->nonDeductible = $this->io->ask(
                question: 'How much non deductible amount?',
                default: '0',
            );
        }
    }
}
