<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

use BadMethodCallException;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
trait ProxyToCrawlerTrait
{
    /**
     * @var HtmlPageCrawler
     */
    protected $crawler;

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
