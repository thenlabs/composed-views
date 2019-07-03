<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\ComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractView implements ComponentInterface
{
    use ComponentTrait;

    protected $view;

    abstract protected function getView(): string;

    public function setView(?string $view): void
    {
        $this->view = $view;
    }

    public function render(): string
    {
        if (is_string($this->view) && $this->view != '') {
            return $this->view;
        }

        return $this->getView();
    }

    public function __toString()
    {
        return $this->render();
    }
}
