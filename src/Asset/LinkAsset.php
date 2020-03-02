<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Asset;

use ThenLabs\ComposedViews\HtmlElement;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class LinkAsset extends HtmlElement
{
    protected $tagName = 'link';
    protected $attributes = ['rel' => 'stylesheet', 'href' => ''];
    protected $innerHtml = '';
    protected $endTag = false;
    protected $selfClosingTag = false;
    protected $uri;

    public function __construct(string $name, ?string $version, string $uri)
    {
        $this->name = $name;
        $this->version = $version;
        $this->uri = $uri;

        $this->addFilter(function ($event) {
            $basePath = $event->getData()['basePath'];
            $event->setAttribute('href', $basePath . $this->uri);
        });
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
