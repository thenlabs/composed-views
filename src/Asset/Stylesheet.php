<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Asset;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Stylesheet extends AbstractAsset
{
    protected $tagName = 'link';
    protected $attributes = ['rel' => 'stylesheet', 'href' => ''];
    protected $innerHtml = '';
    protected $endTag = false;
    protected $selfClosingTag = false;

    public function __construct(string $name, ?string $version, string $uri)
    {
        parent::__construct($name, $version, $uri);

        $this->addFilter(function ($event) {
            $data = $event->getData();
            $basePath = $data['basePath'] ?? '';
            $event->setAttribute('href', $basePath . $this->uri);
        });
    }
}
