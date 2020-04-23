<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

use ThenLabs\Components\DependencyInterface;
use ThenLabs\Components\EditableDependencyTrait;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use Spatie\HtmlElement\HtmlElement as SpatieHtmlElement;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class TextView extends AbstractView implements DependencyInterface
{
    use EditableDependencyTrait;
    use ProxyToCrawlerTrait;

    public function __construct(string $content)
    {
        $this->crawler = new HtmlPageCrawler($content);
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getView(array $data = []): string
    {
        return $this->crawler->saveHTML();
    }

    public static function createFromSyntax(string $syntax): self
    {
        $textView = new self(SpatieHtmlElement::render($syntax));
        return $textView;
    }
}
