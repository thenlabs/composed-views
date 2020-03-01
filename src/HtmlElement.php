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
    protected $innerHtml = '';
    protected $endTag = true;
    protected $selfClosingTag = false;

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

    public function getInnerHtml(): string
    {
        return $this->innerHtml;
    }

    public function setInnerHtml(string $innerHtml): void
    {
        $this->innerHtml = $innerHtml;
    }

    public function hasEndTag(): bool
    {
        return $this->endTag;
    }

    public function setEndTag(bool $endTag): void
    {
        $this->endTag = $endTag;
    }

    public function hasSelfClosingTag(): bool
    {
        return $this->selfClosingTag;
    }

    public function setSelfClosingTag(bool $selfClosingTag): void
    {
        $this->selfClosingTag = $selfClosingTag;
    }
}
