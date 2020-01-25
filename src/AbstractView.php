<?php
declare(strict_types=1);

namespace NubecuLabs\ComposedViews;

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\ComposedViews\Event\RenderEvent;
use NubecuLabs\ComposedViews\Annotation\ViewData;
use Doctrine\Common\Annotations\AnnotationReader;
use Closure;
use ReflectionClass;
use BadMethodCallException;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractView implements ComponentInterface
{
    use ComponentTrait;

    abstract protected function getView(array $data = []): string;

    public function render(array $data = []): string
    {
        $renderEvent = new RenderEvent($this->getView($data));

        $this->dispatchEvent('render', $renderEvent);

        return $renderEvent->getView();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function addFilter(callable $callback): void
    {
        $this->on('render', $callback);
    }

    public function __call($method, $arguments)
    {
        foreach ($this->getModel()['properties'] as $propertyName => $propertyInfo) {
            if ($method == $propertyInfo['getter']) {
                return $this->{$propertyName};
            }

            if ($method == $propertyInfo['setter']) {
                $this->{$propertyName} = $arguments[0];
                return;
            }
        }

        throw new BadMethodCallException("Unknow method '{$method}'.");
    }

    private function getModel(): array
    {
        $properties = [];

        $class = new ReflectionClass($this);
        $reader = new AnnotationReader();
        // Hack for load the annotation class. If is omitted it's throws a doctrine exception.
        new ViewData;

        foreach ($class->getProperties() as $property) {
            foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof ViewData) {
                    $propertyName = $property->getName();

                    $properties[$propertyName] = [
                        'getter' => $annotation->getter ?? 'get'.ucfirst($propertyName),
                        'setter' => $annotation->setter ?? 'set'.ucfirst($propertyName),
                    ];
                }
            }
        }

        return compact('properties');
    }
}
