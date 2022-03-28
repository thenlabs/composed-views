<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Event;

use ThenLabs\Components\Event\Event;
use ThenLabs\ComposedViews\ProxyToCrawlerTrait;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class RenderEvent extends Event
{
    use ProxyToCrawlerTrait;

    /**
     * @var string|null
     */
    protected $view;

    /**
     * @var array
     */
    protected $data;

    public function __construct(string $view, array $data)
    {
        $this->crawler = new HtmlPageCrawler($view);
        $this->data = $data;
    }

    public function getView(): string
    {
        return $this->view ? $this->view : strval($this->crawler);
    }

    public function setView(?string $view): void
    {
        $this->view = $view;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
