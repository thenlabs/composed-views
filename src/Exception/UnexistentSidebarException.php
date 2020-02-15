<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Exception;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class UnexistentSidebarException extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct("The sidebar '{$name}' not exists.");
    }
}
