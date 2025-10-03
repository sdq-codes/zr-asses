<?php

namespace App\Rules\Cron;

use Closure;
use Cron\CronExpression;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidCronExpression implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        if (!CronExpression::isValidExpression($value)) {
            $fail("The {$attribute} must be a valid cron expression.");
        }
    }
}
