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

    protected $tagName;
    protected $attributes;
    protected $endTag;
    protected $selfClosingTag;

    public function __construct(string $tagName = 'div', ?array $attributes = [], ?string $innerHtml = '', bool $endTag = true, bool $selfClosingTag = false)
    {
        $this->setTagName($tagName);
        $this->setEndTag($endTag);
        $this->setSelfClosingTag($selfClosingTag);

        if (is_array($attributes)) {
            $this->setAttributes($attributes);
        }

        if ($innerHtml) {
            $this->setInnerHtml($innerHtml);
        }
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getView(array $data = []): string
    {
        $startTag = "<{$this->tagName}";
        foreach ($this->attributes as $attribute => $value) {
            $startTag .= " {$attribute}";

            if ($value) {
                $startTag .= "=\"{$value}\"";
            }
        }
        $startTag .= $this->selfClosingTag ? ' />' : '>';

        $endTag = "</{$this->tagName}>";

        if (! $this->endTag) {
            $endTag = '';
        }

        if ($this->selfClosingTag) {
            $innerHtml = '';
            $endTag = '';
        }

        return $startTag . $this->getInnerHtml() . $endTag;
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

    public function getAttribute(string $attribute)
    {
        return $this->attributes[$attribute] ?? null;
    }

    public function setAttribute(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->attributes);
    }

    public function getInnerHtml(): string
    {
        $result = '';

        if (! $this->hasSelfClosingTag()) {
            foreach ($this->getChilds() as $child) {
                $result .= strval($child);
            }
        }

        return $result;
    }

    public function setInnerHtml(string $innerHtml): void
    {
        $this->childs = [];

        $this->addChild(new TextView($innerHtml));
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

        if ($selfClosingTag) {
            $this->endTag = false;
        }
    }
}
