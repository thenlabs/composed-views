<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Asset;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Script extends AbstractAsset
{
    protected $tagName = 'script';
    protected $attributes = ['src' => ''];
    protected $innerHtml = '';
    protected $endTag = true;
    protected $selfClosingTag = false;

    public function __construct(string $name, ?string $version, string $uri)
    {
        parent::__construct($name, $version, $uri);

        $this->addFilter(function ($event) {
            $basePath = $event->getData()['basePath'];
            $event->setAttribute('src', $basePath . $this->uri);
        });
    }
}
