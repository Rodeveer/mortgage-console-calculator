<?php declare(strict_types=1);

namespace App\Enum;

enum LoanDebtSystemEnum: string
{
    case LOAN_SYSTEM_BEFORE_2015 = 'Loan system before 1 september 2015';
    case LOAN_SYSTEM_AFTER_2015 = 'Loan system after 1 september 2015';
}
