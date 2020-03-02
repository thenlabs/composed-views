<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Exception;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class UnexistentPropertyException extends \Exception
{
    public function __construct(string $property)
    {
        parent::__construct("The property '{$property}' not exists.");
    }
}
