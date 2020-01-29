<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews\Asset;

use NubecuLabs\ComposedViews\AbstractView;
use NubecuLabs\ComposedViews\Annotation\ViewData;
use NubecuLabs\ComposedViews\HtmlElement;
use NubecuLabs\Components\DependencyInterface;
use NubecuLabs\Components\EditableDependencyTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class HtmlAsset extends HtmlElement implements DependencyInterface
{
    /**
     * @ViewData
     */
    protected $basePath;

    /**
     * @ViewData
     */
    protected $packagePath;

    /**
     * @ViewData
     */
    protected $filename;
}
