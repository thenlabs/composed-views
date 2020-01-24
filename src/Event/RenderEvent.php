<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews\Event;

use NubecuLabs\Components\Event\Event;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class RenderEvent extends Event
{
    /**
     * @var string|null
     */
    protected $view;

    /**
     * @var HtmlPageCrawler
     */
    protected $pageCrawler;

    public function __construct(string $view)
    {
        $this->pageCrawler = new HtmlPageCrawler($view);
    }

    public function getView(): string
    {
        return $this->view ? $this->view : $this->pageCrawler->saveHTML();
    }

    public function setView(?string $view): void
    {
        $this->view = $view;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->pageCrawler, $method], $arguments);
    }
}
