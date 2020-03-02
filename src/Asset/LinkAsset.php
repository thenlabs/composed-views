<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Asset;

use ThenLabs\ComposedViews\HtmlElement;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class LinkAsset extends HtmlElement
{
    public function __construct(string $name, ?string $version, string $uri)
    {
        $this->name = $name;
    }
}
