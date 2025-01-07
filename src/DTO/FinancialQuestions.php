<?php declare(strict_types=1);

namespace App\DTO;

use App\Enum\LoanDebtSystemEnum;

class FinancialQuestions
{
    public bool $isNHG = true {
        get => $this->isNHG;
        set (bool $value) {
            $this->isNHG = $value;
        }
    }

    public ?LoanDebtSystemEnum $studentDebtLoanSystem = null {
        get => $this->studentDebtLoanSystem;
        set (LoanDebtSystemEnum|string|null $value) {
            if ($value === null) {
                $this->studentDebtLoanSystem = null;
                return;
            }

            $this->studentDebtLoanSystem = $value instanceof LoanDebtSystemEnum
                ? $value
                : LoanDebtSystemEnum::tryFrom($value);
        }
    }

    public float $studentDebtCosts = 0 {
        get => $this->studentDebtCosts;
        set (float|string $value) {
            $this->studentDebtCosts = (float)$value;
        }
    }

    public float $creditsCosts = 0 {
        get => $this->creditsCosts;
        set (float|string $value) {
            $this->creditsCosts = (float)$value;
        }
    }

    public float $alimonyToPartnerCosts = 0 {
        get => $this->alimonyToPartnerCosts;
        set (float|string $value) {
            $this->alimonyToPartnerCosts = (float)$value;
        }
    }

    public float $privateLeaseCosts = 0 {
        get => $this->privateLeaseCosts;
        set (float|string $value) {
            $this->privateLeaseCosts = (float)$value;
        }
    }

    public int $privateLeaseDuration = 0 {
        get => $this->privateLeaseDuration;
        set (int|string $value) {
            $this->privateLeaseDuration = (int)$value;
        }
    }

    public float $groundRent = 0 {
        get => $this->groundRent;
        set (float|string $value) {
            $this->groundRent = (float)$value;
        }
    }

    public float $nonDeductible = 0 {
        get => $this->nonDeductible;
        set (float|string $value) {
            $this->nonDeductible = (float)$value;
        }
    }
}