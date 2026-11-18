<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniversityEmail implements ValidationRule
{
    /**
     * Only accept email addresses of the university domain (see config/helpdesk.php)
     * or one of its subdomains, for example name@student.harbour.space.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $universityDomain = config('helpdesk.email_domain');
        $emailDomain = Str::lower(Str::afterLast((string) $value, '@'));

        $isUniversityDomain = $emailDomain === $universityDomain;
        $isSubdomain = Str::endsWith($emailDomain, '.'.$universityDomain);

        if (! $isUniversityDomain && ! $isSubdomain) {
            $fail("Please use your university email address (ending in @{$universityDomain}).");
        }
    }
}
