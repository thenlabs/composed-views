<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews\Asset;

use ThenLabs\ComposedViews\HtmlElement;
use ThenLabs\ComposedViews\Annotation\Data;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractAsset extends HtmlElement
{
    /**
     * @Data
     */
    protected $uri;

    public function __construct(string $name, ?string $version, string $uri)
    {
        $this->name = $name;
        $this->version = $version;
        $this->uri = $uri;
    }
}
