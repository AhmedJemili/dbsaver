<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Database extends Constraint
{
    public string $message = 'Database connection test errored: "{{ error }}".';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
