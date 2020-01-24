<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\CompositeComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractCompositeView extends AbstractView implements CompositeComponentInterface
{
    use CompositeComponentTrait;

    public function validateChild(ComponentInterface $child): bool
    {
        return $child instanceof AbstractView ? true : false;
    }

    public function renderChildren(): string
    {
        $result = '';

        foreach ($this->children(false) as $child) {
            $result .= $child->render();
        }

        return $result;
    }
}
