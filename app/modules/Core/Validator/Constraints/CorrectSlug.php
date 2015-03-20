<?php

namespace Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CorrectSlug extends Constraint
{
    public $message = 'Slug содержит недопустимые символы';
}
