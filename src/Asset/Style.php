<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Asset;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Style extends AbstractAsset
{
    protected $tagName = 'style';
    protected $attributes = [];
    protected $innerHtml = '';
    protected $endTag = true;
    protected $selfClosingTag = false;

    public function __construct(string $name, ?string $version, string $uri)
    {
        parent::__construct($name, $version, $uri);

        $this->addFilter(function ($event) {
            $event->setInnerHtml($this->getSource());
        });
    }

    public function getSource(): ?string
    {
        return null;
    }
}
