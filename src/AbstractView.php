<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\ComposedViews\Event\RenderEvent;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractView implements ComponentInterface
{
    use ComponentTrait;

    abstract protected function getView(array $data = []): string;

    public function render(array $data = []): string
    {
        $renderEvent = new RenderEvent($this->getView($data));

        $this->dispatchEvent('render', $renderEvent);

        return $renderEvent->getView();
    }

    public function __toString()
    {
        return $this->render();
    }
}
