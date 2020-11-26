<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Asset;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Script extends AbstractAsset
{
    protected $tagName = 'script';
    protected $attributes = [];
    protected $innerHtml = '';
    protected $endTag = true;
    protected $selfClosingTag = false;
    protected $source;

    public function __construct(string $name, ?string $version, string $uri)
    {
        parent::__construct($name, $version, $uri);

        $this->addFilter(function ($script) {
            if ($source = $this->getSource()) {
                $script->setInnerHtml(htmlspecialchars($source));
            } else {
                $basePath = $script->getData()['basePath'];
                $script->setAttribute('src', $basePath . $this->uri);
            }
        });
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }
}
