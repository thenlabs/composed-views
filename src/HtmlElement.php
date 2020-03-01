<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

use ThenLabs\Components\DependencyInterface;
use ThenLabs\Components\EditableDependencyTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class HtmlElement extends AbstractCompositeView implements DependencyInterface
{
    use EditableDependencyTrait;

    protected $tagName = 'div';
    protected $attributes = [];

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getView(array $data = []): string
    {
        // return $this->content;
    }

    public function getTagName(): string
    {
        return $this->tagName;
    }

    public function setTagName(string $tagName): void
    {
        $this->tagName = $tagName;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }
}
