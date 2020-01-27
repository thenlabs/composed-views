<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews\Asset;

use NubecuLabs\ComposedViews\AbstractView;
use NubecuLabs\Components\DependencyInterface;
use NubecuLabs\Components\EditableDependencyTrait;
use Artyum\HtmlElement\HtmlElement;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class HtmlAsset extends AbstractView implements DependencyInterface
{
    use EditableDependencyTrait;

    protected $name;
    protected $htmlElement;

    public function __construct(string $name, HtmlElement $htmlElement)
    {
        $this->name = $name;
        $this->htmlElement = $htmlElement;
    }

    public function getHtmlElement(): HtmlElement
    {
        return $this->htmlElement;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getView(array $data = []): string
    {
        return $this->htmlElement->toHtml();
    }
}
