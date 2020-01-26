<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews\Asset;

use NubecuLabs\Components\DependencyInterface;
use Artyum\HtmlElement\HtmlElement;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class HtmlAsset implements DependencyInterface
{
    protected $name;
    protected $htmlElement;

    public function __construct(string $name, HtmlElement $htmlElement)
    {
        $this->name = $name;
        $this->htmlElement = $htmlElement;
    }

    public function getHtmlElement(): HtmlElement
    {
        return $this->htmlElement;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): ?string
    {
        return '';
    }

    public function getIncompatibleVersions(): ?string
    {
        return '';
    }

    public function getIncludedDependencies(): array
    {
        return [];
    }

    public function getDependencies(): array
    {
        return [];
    }
}
