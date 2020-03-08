<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\ComponentTrait;
use ThenLabs\ComposedViews\Asset\AbstractAsset;
use ThenLabs\ComposedViews\Asset\Script;
use ThenLabs\ComposedViews\Asset\Style;
use ThenLabs\ComposedViews\Asset\Stylesheet;
use ThenLabs\ComposedViews\Event\RenderEvent;
use ThenLabs\ComposedViews\Annotation\Data as DataAnnotation;
use ThenLabs\ComposedViews\Exception\UnexistentPropertyException;
use ThenLabs\ComposedViews\Exception\UndefinedBasePathException;
use ThenLabs\ComposedViews\Exception\InvalidPropertyValueException;
use Doctrine\Common\Annotations\AnnotationReader;
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

    public function render(array $data = [], bool $dispatchRenderEvent = true): string
    {
        $ownData = [];
        foreach ($this->getModel()['properties'] as $propertyName => $propertyInfo) {
            $ownData[$propertyName] = $propertyInfo['currentValue'];
        }

        $data = array_merge($ownData, $data);
        $content = $this->getView($data);

        if ($dispatchRenderEvent) {
            $renderEvent = new RenderEvent($content, $data);
            $this->dispatchEvent('render', $renderEvent);

            return $renderEvent->getView();
        } else {
            return $content;
        }
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
                $value = $arguments[0];

                if (is_array($propertyInfo['values']) && ! in_array($value, $propertyInfo['values'])) {
                    throw new InvalidPropertyValueException($propertyName, $value);
                }

                $this->{$propertyName} = $value;
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
        new DataAnnotation;

        foreach ($class->getProperties() as $property) {
            foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof DataAnnotation) {
                    $propertyName = $property->getName();

                    $properties[$propertyName] = [
                        'getter'       => $annotation->getter ?? 'get'.ucfirst($propertyName),
                        'setter'       => $annotation->setter ?? 'set'.ucfirst($propertyName),
                        'values'       => $annotation->values,
                        'currentValue' => $this->{$propertyName},
                    ];
                }
            }
        }

        return compact('properties');
    }

    public function setBasePath(string $basePath): void
    {
        $this->setData('basePath', $basePath);
    }

    public function getBasePath(): ?string
    {
        $basePath = $this->getTopData('basePath');

        if ($basePath === null) {
            throw new UndefinedBasePathException;
        }

        return $basePath;
    }

    protected function renderProperty(string $property, array $data = [], bool $dispatchRenderEvent = true): string
    {
        if (! property_exists($this, $property)) {
            throw new UnexistentPropertyException($property);
        }

        if ($this->{$property} instanceof self) {
            return $this->{$property}->render($data, $dispatchRenderEvent);
        } else {
            return '';
        }
    }

    protected function renderAsset(string $basePath, AbstractAsset $asset): string
    {
        return $asset->render(compact('basePath'));
    }

    protected function renderAssets(string $basePath, array $assets): string
    {
        $result = '';

        foreach ($assets as $asset) {
            $result .= $this->renderAsset($basePath, $asset);
        }

        return $result;
    }

    protected function filterStyles(array $assets): array
    {
        return array_filter($assets, function ($asset) {
            return ($asset instanceof Style || $asset instanceof Stylesheet);
        });
    }

    protected function filterScripts(array $assets): array
    {
        return array_filter($assets, function ($asset) {
            return $asset instanceof Script;
        });
    }
}
