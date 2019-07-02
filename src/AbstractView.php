<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractView
{
    public function render(): string
    {
    }

    public function __toString()
    {
        return $this->render();
    }
}
