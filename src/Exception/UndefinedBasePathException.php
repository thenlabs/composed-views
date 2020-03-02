<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Exception;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class UndefinedBasePathException extends \Exception
{
    public function __construct()
    {
        parent::__construct('The base path should be defined.');
    }
}
