<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

use ThenLabs\Components\DependencyInterface;
use ThenLabs\Components\EditableDependencyTrait;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class RawView extends AbstractView implements DependencyInterface
{
    use EditableDependencyTrait;

    protected $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getView(array $data = []): string
    {
        return $this->content;
    }
}
