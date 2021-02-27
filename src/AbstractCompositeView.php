<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\CompositeComponentInterface;
use ThenLabs\Components\CompositeComponentTrait;
use ThenLabs\Components\AdditionalDependenciesFromAnnotationsTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractCompositeView extends AbstractView implements CompositeComponentInterface
{
    use CompositeComponentTrait, AdditionalDependenciesFromAnnotationsTrait {
        AdditionalDependenciesFromAnnotationsTrait::getAdditionalDependencies insteadof CompositeComponentTrait;
    }

    public function validateChild(ComponentInterface $child): bool
    {
        return $child instanceof AbstractView ? true : false;
    }

    public function renderChildren(): string
    {
        $result = '';

        foreach ($this->children(false) as $child) {
            if ($child instanceof AbstractView) {
                $result .= $child->render();
            }
        }

        return $result;
    }
}
