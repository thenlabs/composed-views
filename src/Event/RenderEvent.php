<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Event;

use ThenLabs\Components\Event\Event;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use BadMethodCallException;

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
    protected $crawler;

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

    public function __call($method, $arguments)
    {
        $callback = [$this->crawler, $method];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $arguments);
        } else {
            throw new BadMethodCallException("Unknow method '{$method}'.");
        }
    }
}
