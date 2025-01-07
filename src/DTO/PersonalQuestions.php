<?php declare(strict_types=1);

namespace App\DTO;

use App\Enum\PaymentPeriodEnum;
use DateTimeInterface;

class PersonalQuestions
{
    public DateTimeInterface $dateOfBirth {
        get => $this->dateOfBirth;
        set (DateTimeInterface $value) {
            $this->dateOfBirth = $value;
        }
    }

    public int $grossIncome = 0 {
        get => $this->grossIncome;
        set (string|int $value) {
            $this->grossIncome = (int) $value;
        }
    }

    public ?PaymentPeriodEnum $grossPayment = null {
        get => $this->grossPayment;
        set (PaymentPeriodEnum|string|null $value) {
            if ($value === null) {
                $this->grossPayment = null;
                return;
            }

            $this->grossPayment = $value instanceof PaymentPeriodEnum
                ? $value
                : PaymentPeriodEnum::tryFrom($value);
        }
    }

    public bool $hasHolidayPayment = true {
        get => $this->hasHolidayPayment;
        set (bool $value) {
            $this->hasHolidayPayment = $value;
        }
    }

    public bool $hasExtraMonth = true {
        get => $this->hasExtraMonth;
        set (bool $value) {
            $this->hasExtraMonth = $value;
        }
    }

    public float $totalGrossPayment {
        get {
            $amount = $this->grossIncome;
            if ($this->grossPayment === PaymentPeriodEnum::MONTHLY) {
                $amount = ($amount * 12) + ($this->hasExtraMonth ? $this->grossIncome : 0);
            }

            if ($this->grossPayment === PaymentPeriodEnum::YEARLY && $this->hasExtraMonth) {
                $amount += $this->grossIncome / 12;
            }

            if ($this->hasHolidayPayment) {
                $amount += $this->grossIncome * 0.8;
            }

            return $amount;
        }
    }
}
