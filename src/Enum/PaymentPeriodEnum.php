<?php declare(strict_types=1);

namespace App\Enum;

enum PaymentPeriodEnum: string
{
    case MONTHLY = 'Monthly';
    case YEARLY = 'Yearly';
}
