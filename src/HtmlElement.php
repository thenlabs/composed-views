<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

use ThenLabs\Components\DependencyInterface;
use ThenLabs\Components\EditableDependencyTrait;
use Artyum\HtmlElement\HtmlElement as ArtyumHtmlElement;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
class HtmlElement extends AbstractView implements DependencyInterface
{
    use EditableDependencyTrait;

    protected $artyumHtmlElement;

    public function __construct(string $tag = 'div')
    {
        $this->artyumHtmlElement = new ArtyumHtmlElement($tag);
    }

    public function getView(array $data = []): string
    {
        return $this->artyumHtmlElement->toHtml();
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getArtyumHtmlElement(): ArtyumHtmlElement
    {
        return $this->artyumHtmlElement;
    }

    public function setArtyumHtmlElement(ArtyumHtmlElement $artyumHtmlElement): void
    {
        $this->artyumHtmlElement = $artyumHtmlElement;
    }

    public function __call($method, $arguments)
    {
        $callback = [$this->artyumHtmlElement, $method];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $arguments);
        } else {
            return parent::__call($method, $arguments);
        }
    }
}
