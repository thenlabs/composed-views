<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Exception;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class InvalidPropertyValueException extends \Exception
{
    public function __construct(string $property, string $value)
    {
        parent::__construct("The value '{$value}' is invalid for the property '{$property}'.");
    }
}
