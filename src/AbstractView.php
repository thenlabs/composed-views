<?php
declare(strict_types=1);

namespace ThenLabs\ComposedViews;

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\ComponentTrait;
use ThenLabs\Components\AdditionalDependenciesFromAnnotationsTrait;
use ThenLabs\ComposedViews\Asset\AbstractAsset;
use ThenLabs\ComposedViews\Asset\Script;
use ThenLabs\ComposedViews\Asset\Style;
use ThenLabs\ComposedViews\Asset\Stylesheet;
use ThenLabs\ComposedViews\Event\RenderEvent;
use ThenLabs\ComposedViews\Annotation\Data as DataAnnotation;
use ThenLabs\ComposedViews\Annotation\View as ViewAnnotation;
use ThenLabs\ComposedViews\Annotation\Sidebar as SidebarAnnotation;
use ThenLabs\ComposedViews\Exception\UnexistentPropertyException;
use ThenLabs\ComposedViews\Exception\InvalidPropertyValueException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionClass;
use BadMethodCallException;

AnnotationRegistry::registerFile(__DIR__.'/Annotation/Data.php');
AnnotationRegistry::registerFile(__DIR__.'/Annotation/Sidebar.php');
AnnotationRegistry::registerFile(__DIR__.'/Annotation/View.php');

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class AbstractView implements ComponentInterface
{
    use ComponentTrait, AdditionalDependenciesFromAnnotationsTrait {
        AdditionalDependenciesFromAnnotationsTrait::getAdditionalDependencies insteadof ComponentTrait;
    }

    private $_basePath;
    private $_dependencies = [];

    public function __construct()
    {
        $model = $this->getModel();
        foreach ($model['sidebars'] as $sidebarName => $sidebarData) {
            $this->{$sidebarName} = new Sidebar;
        }
    }

    abstract protected function getView(): string;

    public function render(array $data = [], bool $dispatchRenderEvent = true): string
    {
        $this->_basePath = $this->getBasePath();
        $this->_dependencies = $this->getDependencies();

        $currentData = [];
        foreach ($this->getModel()['data'] as $propertyName => $propertyInfo) {
            $currentData[$propertyName] = $this->{$propertyName};
        }

        $data = array_merge($currentData, $data);
        $content = $this->getView($data);

        $this->_basePath = [];
        $this->_dependencies = [];

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
        foreach ($this->getModel()['data'] as $propertyName => $propertyInfo) {
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

    public function __get($name)
    {
        $model = $this->getModel();

        if (! isset($model['views'][$name])) {
            throw new UnexistentPropertyException($name);
        }

        return $this->{$name};
    }

    public function getModel(): array
    {
        static $model = null;

        if (! $model) {
            $model = [
                'data' => [],
                'views' => [],
                'sidebars' => [],
            ];

            $class = new ReflectionClass($this);
            $reader = new AnnotationReader();

            foreach ($class->getProperties() as $property) {
                foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                    $propertyName = $property->getName();

                    if ($annotation instanceof DataAnnotation) {
                        $model['data'][$propertyName] = [
                            'getter' => $annotation->getter ?? 'get'.ucfirst($propertyName),
                            'setter' => $annotation->setter ?? 'set'.ucfirst($propertyName),
                            'values' => $annotation->values,
                        ];
                    }

                    if ($annotation instanceof ViewAnnotation) {
                        $model['views'][$propertyName] = [];
                    }

                    if ($annotation instanceof SidebarAnnotation) {
                        $model['sidebars'][$propertyName] = [];
                    }
                }
            }
        }

        return $model;
    }

    public function setBasePath(string $basePath): void
    {
        $this->setData('basePath', $basePath);
    }

    public function getBasePath(): ?string
    {
        $basePath = $this->getTopData('basePath');

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

    protected function renderAsset(AbstractAsset $asset): string
    {
        return $asset->render(['basePath' => $this->_basePath]);
    }

    protected function renderAssets(array $assets): string
    {
        $result = '';

        foreach ($assets as $asset) {
            $result .= $this->renderAsset($asset);
        }

        return $result;
    }

    protected function renderStyles(): string
    {
        $result = '';

        foreach ($this->getStyles() as $style) {
            $result .= $this->renderAsset($style);
        }

        return $result;
    }

    protected function renderScripts(): string
    {
        $result = '';

        foreach ($this->getScripts() as $script) {
            $result .= $this->renderAsset($script);
        }

        return $result;
    }

    protected function getStyles(): array
    {
        return array_filter($this->_dependencies, function ($asset) {
            return ($asset instanceof Style || $asset instanceof Stylesheet);
        });
    }

    protected function getScripts(): array
    {
        return array_filter($this->_dependencies, function ($asset) {
            return $asset instanceof Script;
        });
    }
}
