<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class HtmlElement extends AbstractView
{
    protected $tagName = 'div';
    protected $attributes = [];
    protected $content;
    protected $closeTag = true;

    protected function getView(): string
    {
        return '<div></div>';
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getCloseTag(): ?bool
    {
        return $this->closeTag;
    }

    public function setCloseTag(?bool $closeTag): void
    {
        $this->closeTag = $closeTag;
    }
}
