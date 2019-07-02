<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews;

use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\CompositeComponentTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractCompositeView extends AbstractView implements CompositeComponentInterface
{
    use CompositeComponentTrait;
}
