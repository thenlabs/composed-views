<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Sidebar extends AbstractCompositeView
{
    public function getView(array $data = []): string
    {
        return $this->renderChildren();
    }
}
