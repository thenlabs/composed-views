<?php

namespace ThenLabs\ComposedViews\Tests;

use BadMethodCallException;
use stdClass;
use ThenLabs\ClassBuilder\ClassBuilder;
use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\DependencyInterface;
use ThenLabs\ComposedViews\AbstractView;
use ThenLabs\ComposedViews\Asset\AbstractAsset;
use ThenLabs\ComposedViews\Asset\Script;
use ThenLabs\ComposedViews\Asset\Style;
use ThenLabs\ComposedViews\Asset\Stylesheet;
use ThenLabs\ComposedViews\Event\RenderEvent;
use ThenLabs\ComposedViews\Exception\InvalidPropertyValueException;
use ThenLabs\ComposedViews\Exception\UnexistentPropertyException;
use ThenLabs\ComposedViews\Sidebar;
use Wa72\HtmlPageDom\HtmlPageCrawler;

macro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php', function () {
    test('the view is instance of ThenLabs\Components\ComponentInterface', function () {
        $view = $this->getMockForAbstractClass($this->getViewClass());

        $this->assertInstanceOf(ComponentInterface::class, $view);
    });

    test('render($data) returns result of getView($data)', function () {
        $viewStr = uniqid();
        $args = range(0, mt_rand(0, 10));

        $view = $this->getMockBuilder($this->getViewClass())
            ->setMethods(['getView'])
            ->getMockForAbstractClass();
        $view->expects($this->once())
            ->method('getView')
            ->with($this->equalTo($args))
            ->willReturn($viewStr)
        ;

        $this->assertSame($viewStr, $view->render($args));
    });

    test('addFilter($closure) is alias for on("render", $closure)', function () {
        $closure = function () {
        };

        $view = $this->getMockBuilder($this->getViewClass())
            ->setMethods(['on'])
            ->getMockForAbstractClass();
        $view->expects($this->once())
            ->method('on')
            ->with(
                $this->equalTo('render'),
                $this->equalTo($closure)
            );

        $view->addFilter($closure);
    });

    test('setBasePath($value) invoke to setData("basePath", $value)', function () {
        $value = uniqid();

        $view = $this->getMockBuilder($this->getViewClass())
            ->setMethods(['setData'])
            ->getMockForAbstractClass();
        $view->expects($this->once())
            ->method('setData')
            ->with(
                $this->equalTo('basePath'),
                $this->equalTo($value)
            )
        ;

        $view->setBasePath($value);
    });

    test('getBasePath() returns value from getTopData("basePath")', function () {
        $view = $this->getMockBuilder($this->getViewClass())
            ->setMethods(['getTopData'])
            ->getMockForAbstractClass();
        $view->expects($this->once())
            ->method('getTopData')
            ->with($this->equalTo('basePath'))
            ->willReturn(uniqid())
        ;

        $view->getBasePath();
    });

    testCase('__toString() returns result of the render() method', function () {
        setUp(function () {
            $this->result = uniqid();

            $this->view = $this->getMockBuilder($this->getViewClass())
                ->setMethods(['render'])
                ->getMockForAbstractClass();
            $this->view->method('render')->willReturn($this->result);
        });

        test(function () {
            $this->assertSame($this->result, strval($this->view));
        });

        test(function () {
            $this->assertSame($this->result, (string) $this->view);
        });

        test(function () {
            echo $this->view;

            $this->expectOutputString($this->result);
        });
    });

    test('renderAsset($asset) returns result of $asset->render(["basePath" => $basePath])', function () {
        $result = uniqid();
        $basePath = uniqid('http://localhost/');

        $asset = $this->getMockBuilder(AbstractAsset::class)
            ->disableOriginalConstructor()
            ->setMethods(['render'])
            ->getMockForAbstractClass();
        $asset->expects($this->once())
            ->method('render')
            ->with(
                $this->callback(function ($data) use ($basePath) {
                    return $data['basePath'] === $basePath;
                })
            )
            ->willReturn($result)
        ;

        $view = (new ClassBuilder)->extends($this->getViewClass())
            ->addMethod('getDependencies', function () use ($asset): array {
                return [$asset];
            })->end()

            ->addMethod('getView')
                ->setAccess('protected')
                ->setClosure(function (): string {
                    $assets = $this->getDependencies();
                    $asset = array_pop($assets);

                    return $this->renderAsset($asset);
                })
            ->end()

            ->newInstance()
        ;

        $view->setBasePath($basePath);

        $this->assertEquals($result, $view->render());
    });

    test('$view->renderAssets([$asset1, $asset2, ...]) returns result of invoke each $view->renderAsset($asset1), $view->renderAsset($asset2), ...', function () {
        $result1 = uniqid();
        $result2 = uniqid();
        $basePath = uniqid('http://localhost/');

        $asset1 = $this->getMockBuilder(AbstractAsset::class)
            ->disableOriginalConstructor()
            ->setMethods(['render'])
            ->getMockForAbstractClass();
        $asset1->expects($this->once())
            ->method('render')
            ->with(
                $this->callback(function ($data) use ($basePath) {
                    return $data['basePath'] === $basePath;
                })
            )
            ->willReturn($result1)
        ;

        $asset2 = $this->getMockBuilder(AbstractAsset::class)
            ->disableOriginalConstructor()
            ->setMethods(['render'])
            ->getMockForAbstractClass();
        $asset2->expects($this->once())
            ->method('render')
            ->with(
                $this->callback(function ($data) use ($basePath) {
                    return $data['basePath'] === $basePath;
                })
            )
            ->willReturn($result2)
        ;

        $view = (new ClassBuilder)->extends($this->getViewClass())
            ->addMethod('getDependencies', function () use ($asset1, $asset2): array {
                return [$asset1, $asset2];
            })->end()

            ->addMethod('getView')
                ->setAccess('protected')
                ->setClosure(function (): string {
                    return $this->renderAssets($this->getDependencies());
                })
            ->end()

            ->newInstance()
        ;

        $view->setBasePath($basePath);

        $this->assertEquals("{$result1}\n{$result2}\n", $view->render());
    });

    test('getAdditionalDependencies() includes the sidebars dependencies', function () {
        $dependencyName1 = uniqid('dep');
        $dependencyName2 = uniqid('dep');

        $dependency1 = $this->createMock(DependencyInterface::class);
        $dependency1->method('getName')->willReturn($dependencyName1);

        $dependency2 = $this->createMock(DependencyInterface::class);
        $dependency2->method('getName')->willReturn($dependencyName2);

        $child1 = $this->createMock($this->getViewClass());
        $child1->method('getId')->willReturn('child1');
        $child1->method('getDependencies')->willReturn([$dependency1]);

        $child2 = $this->createMock($this->getViewClass());
        $child2->method('getId')->willReturn('child2');
        $child2->method('getDependencies')->willReturn([$dependency2]);

        $property1 = uniqid('property');
        $property2 = uniqid('property');

        $view = (new ClassBuilder)->extends($this->getViewClass())
            ->addMethod('getView')
                ->setAccess('protected')
                ->setClosure(function (): string {
                    return '';
                })
            ->end()
            ->addProperty($property1)
                ->setAccess('protected')
                ->addComment('@ThenLabs\ComposedViews\Annotation\Sidebar')
            ->end()
            ->addProperty($property2)
                ->setAccess('protected')
                ->addComment('@ThenLabs\ComposedViews\Annotation\Sidebar')
            ->end()
            ->newInstance()
        ;

        $view->{$property1}->addChild($child1);
        $view->{$property2}->addChild($child2);

        $this->assertEquals(
            [$dependencyName1 => $dependency1, $dependencyName2 => $dependency2],
            $view->getAdditionalDependencies()
        );
    });

    test(function () {
        $view = (new ClassBuilder)->extends($this->getViewClass())
            ->addProperty('property1')
            ->end()
            ->addMethod('__construct')
                ->setClosure(function () {
                    $this->property1 = new class extends AbstractView {
                        public function getView(array $data = []): string
                        {
                            return "{$data['basePath']}";
                        }
                    };
                })
            ->end()
            ->addMethod('getView')
                ->setAccess('protected')
                ->setClosure(function (): string {
                    return $this->renderProperty('property1');
                })
            ->end()
            ->newInstance()
        ;

        $view->setBasePath($basePath = uniqid());

        $this->assertEquals($basePath, $view->render());
    });

    testCase('exists a view', function () {
        setUp(function () {
            $this->view = (new ClassBuilder)->extends($this->getViewClass())
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (): string {
                        return '<div>'.uniqid().'</div>';
                    })
                ->end()
                ->newInstance();
        });

        test('throwns an BadMethodCallException when the called method not exists', function () {
            $method = uniqid('method');
            $this->expectException(BadMethodCallException::class);
            $this->expectExceptionMessage("Unknow method '{$method}'.");

            $this->view->{$method}();
        });

        test('throwns an UnexistentPropertyException when attempts access to an unaccesible property', function () {
            $property = uniqid('property');
            $this->expectException(UnexistentPropertyException::class);
            $this->expectExceptionMessage("The property '{$property}' not exists.");

            $this->view->{$property};
        });

        testCase('it is applied a filter where all content is assigned', function () {
            setUp(function () {
                $this->expectedView = '<label>'.uniqid().'</label>';

                $this->view->on('render', function (RenderEvent $event) {
                    $event->setView($this->expectedView);
                });
            });

            test('the render result contains filter changes', function () {
                $this->assertSame($this->expectedView, $this->view->render());
            });

            test('the result not contains the changes when render it is called with false as second argument', function () {
                $this->assertNotEquals($this->expectedView, $this->view->render([], false));
            });
        });

        testCase('it is applied a filter using the page dom interface', function () {
            setUp(function () {
                $this->attrName = uniqid('attr-');
                $this->attrValue = uniqid();

                $this->view->on('render', function (RenderEvent $event) {
                    $event->setAttribute($this->attrName, $this->attrValue);
                });
            });

            test('the render result contains changes of the filter', function () {
                $crawler = new HtmlPageCrawler($this->view->render());

                $this->assertCount(1, $crawler->filter("div[{$this->attrName}=\"{$this->attrValue}\"]"));
            });
        });

        testCase('testing getStyles() and getScripts() methods', function () {
            setUp(function () {
                $this->style1 = $this->createMock(Style::class);
                $this->style2 = $this->createMock(Style::class);

                $this->script1 = $this->createMock(Script::class);
                $this->script2 = $this->createMock(Script::class);
                $this->script3 = $this->createMock(Script::class);

                $this->stylesheet1 = $this->createMock(Stylesheet::class);

                $this->assets = [
                    $this->style1,
                    $this->style2,
                    $this->script1,
                    $this->script2,
                    $this->script3,
                    $this->stylesheet1,
                ];
            });

            test('getStyles(array $assets) returns array with instances of Stylesheet and Style', function () {
                $assets = $this->assets;
                $result = [];

                $view = (new ClassBuilder)->extends($this->getViewClass())
                    ->addMethod('getView', function () use (&$result): string {
                        $result = $this->getStyles();
                        return '';
                    })->end()

                    ->addMethod('getDependencies', function () use ($assets): array {
                        return $assets;
                    })->end()

                    ->newInstance()
                ;

                $view->render();

                $this->assertCount(3, $result);
                $this->assertContains($this->style1, $result);
                $this->assertContains($this->style2, $result);
                $this->assertContains($this->stylesheet1, $result);
            });

            test('getScripts(array $assets) returns array with instances of Script', function () {
                $assets = $this->assets;
                $result = [];

                $view = (new ClassBuilder)->extends($this->getViewClass())
                    ->addMethod('getView', function () use (&$result): string {
                        $result = $this->getScripts();
                        return '';
                    })->end()

                    ->addMethod('getDependencies', function () use ($assets): array {
                        return $assets;
                    })->end()

                    ->newInstance()
                ;

                $view->render();

                $this->assertCount(3, $result);
                $this->assertContains($this->script1, $result);
                $this->assertContains($this->script2, $result);
                $this->assertContains($this->script3, $result);
            });

            test('renderStyles()', function () {
                $result1 = uniqid();
                $result2 = uniqid();

                $style1 = $this->createMock(Style::class);
                $style1->method('render')->willReturn($result1);

                $style2 = $this->createMock(Style::class);
                $style2->method('render')->willReturn($result2);

                $assets = [$style1, $style2];

                $view = (new ClassBuilder)->extends($this->getViewClass())
                    ->addMethod('getView', function (): string {
                        return $this->renderStyles();
                    })->end()

                    ->addMethod('getDependencies', function () use ($assets): array {
                        return $assets;
                    })->end()

                    ->newInstance()
                ;

                $result = $view->render();

                $this->assertStringContainsString($result1, $result);
                $this->assertStringContainsString($result2, $result);
            });

            test('renderScripts()', function () {
                $result1 = uniqid();
                $result2 = uniqid();

                $script1 = $this->createMock(Script::class);
                $script1->method('render')->willReturn($result1);

                $script2 = $this->createMock(Script::class);
                $script2->method('render')->willReturn($result2);

                $assets = [$script1, $script2];

                $view = (new ClassBuilder)->extends($this->getViewClass())
                    ->addMethod('getView', function (): string {
                        return $this->renderScripts();
                    })->end()

                    ->addMethod('getDependencies', function () use ($assets): array {
                        return $assets;
                    })->end()

                    ->newInstance()
                ;

                $result = $view->render();

                $this->assertStringContainsString($result1, $result);
                $this->assertStringContainsString($result2, $result);
            });
        });
    });

    testCase('exists a view with an annotated data', function () {
        setUp(function () {
            $this->classBuilder = (new ClassBuilder)->extends($this->getViewClass())
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (): string {
                        return '';
                    })
                ->end();
        });

        macro('getter and setter tests', function () {
            test('exists a magic getter for the property', function () {
                $this->assertEquals(
                    $this->propertyValue,
                    call_user_func([$this->view, $this->getterName])
                );
            });

            test('exists a magic setter for the property', function () {
                $newValue = uniqid();

                // set the new value to the property.
                call_user_func([$this->view, $this->setterName], $newValue);

                $this->assertEquals(
                    $newValue,
                    call_user_func([$this->view, $this->getterName])
                );
            });
        });

        testCase('annotation by default', function () {
            setUp(function () {
                $this->propertyName = uniqid('property');
                $this->propertyValue = uniqid();

                $this->getterName = 'get'.ucfirst($this->propertyName);
                $this->setterName = 'set'.ucfirst($this->propertyName);

                $this->classBuilder
                    ->addProperty($this->propertyName)
                        ->setAccess('protected')
                        ->addComment('@ThenLabs\ComposedViews\Annotation\Data')
                        ->setValue($this->propertyValue)
                    ->end();

                $this->view = $this->classBuilder->newInstance();
            });

            useMacro('getter and setter tests');
        });

        testCase('annotation specifying custom methods', function () {
            setUp(function () {
                $this->propertyName = uniqid('property');
                $this->propertyValue = uniqid();

                $this->getterName = uniqid('getter');
                $this->setterName = uniqid('setter');

                $this->classBuilder
                    ->addProperty($this->propertyName)
                        ->setAccess('protected')
                        ->addComment("@ThenLabs\ComposedViews\Annotation\Data(getter=\"{$this->getterName}\", setter=\"{$this->setterName}\")")
                        ->setValue($this->propertyValue)
                    ->end();

                $this->view = $this->classBuilder->newInstance();
            });

            useMacro('getter and setter tests');
        });

        testCase('annotation specifying values', function () {
            setUp(function () {
                $this->propertyName = uniqid('property');

                $this->classBuilder
                    ->addProperty($this->propertyName)
                        ->setAccess('protected')
                        ->addComment('@ThenLabs\ComposedViews\Annotation\Data(values={"value1", "value2"})')
                    ->end()
                ;

                $this->view = $this->classBuilder->newInstance();
            });

            test('throwns an InvalidPropertyValueException when sets an non specified value', function () {
                $value = uniqid();

                $this->expectException(InvalidPropertyValueException::class);
                $this->expectExceptionMessage("The value '{$value}' is invalid for the property '{$this->propertyName}'.");

                call_user_func([$this->view, 'set'.ucfirst($this->propertyName)], $value);
            });

            test('', function () {
                $this->view->{'set'.ucfirst($this->propertyName)}('value2');

                $this->assertEquals(
                    'value2',
                    call_user_func([$this->view, 'get'.ucfirst($this->propertyName)])
                );
            });
        });
    });

    testCase('exists a view with an annotated data where it is used for render the view', function () {
        setUp(function () {
            $this->view = (new ClassBuilder)->extends($this->getViewClass())
                ->addProperty('attrName')
                    ->setAccess('protected')
                    ->addComment('@ThenLabs\ComposedViews\Annotation\Data')
                ->end()
                ->addProperty('value')
                    ->setAccess('protected')
                    ->addComment('@ThenLabs\ComposedViews\Annotation\Data')
                ->end()
                ->addProperty('content')
                    ->setAccess('protected')
                    ->addComment('@ThenLabs\ComposedViews\Annotation\Data')
                ->end()
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (array $data = []): string {
                        extract($data);
                        return "<label data-{$attrName}=\"{$value}\">{$content}</label>";
                    })
                ->end()
                ->newInstance();

            $this->attrName = uniqid();
            $this->value = uniqid();
            $this->content = uniqid();

            $this->view->setAttrName($this->attrName);
            $this->view->setValue($this->value);
            $this->view->setContent($this->content);
        });

        test('getView() receive the annotated properties as data', function () {
            $this->assertEquals(
                "<label data-{$this->attrName}=\"{$this->value}\">{$this->content}</label>",
                $this->view->render()
            );
        });

        test('across the render method it is possible override data of the view', function () {
            $newValue = uniqid();

            $this->assertEquals(
                "<label data-{$this->attrName}=\"{$newValue}\">{$this->content}</label>",
                $this->view->render(['value' => $newValue])
            );
        });
    });

    testCase('exists a view with a component view property', function () {
        setUp(function () {
            $this->propertyName = uniqid('property');

            $this->classBuilder = (new ClassBuilder)->extends($this->getViewClass())
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (): string {
                        return '';
                    })
                ->end()
                ->addProperty($this->propertyName)
                    ->setAccess('protected')
                    ->addComment('@ThenLabs\ComposedViews\Annotation\View')
                ->end();
            ;

            $this->view = $this->classBuilder->newInstance();
        });

        test('the annotated component view properties has public access', function () {
            $instance = new stdClass;

            // inject the instance inside the view.
            (function ($propertyName, $instance) {
                $this->{$propertyName} = $instance;
            })->call($this->view, $this->propertyName, $instance);

            $this->assertSame($instance, $this->view->{$this->propertyName});
        });
    });

    testCase('the renderProperty() method', function () {
        test('throwns an UnexistentPropertyException when the specified property not exists', function () {
            $property = uniqid('property');

            $this->expectException(UnexistentPropertyException::class);
            $this->expectExceptionMessage("The property '{$property}' not exists.");

            $view = (new ClassBuilder)->extends($this->getViewClass())
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function () use ($property): string {
                        $this->renderProperty($property); // throwns exception.
                    })
                ->end()
                ->newInstance()
            ;

            $view->render();
        });

        test('returns an empty string when the property not contains a view component', function () {
            $property = uniqid('property');

            $view = (new ClassBuilder)->extends($this->getViewClass())
                ->addProperty($property)
                    ->setAccess('protected')
                ->end()
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function () use ($property): string {
                        return $this->renderProperty($property);
                    })
                ->end()
                ->newInstance()
            ;

            $this->assertEmpty($view->render());
        });

        test('returns view of the view component in property', function () {
            $property = uniqid('property');
            $expectedResult = uniqid();
            $dispatchRenderEvent = boolval(mt_rand(0, 1));

            $data = range(1, mt_rand(1, 10));
            $data['basePath'] = uniqid();

            $otherView = $this->getMockBuilder($this->getViewClass())
                ->setMethods(['render'])
                ->getMockForAbstractClass();
            $otherView->expects($this->once())
                ->method('render')
                ->with(
                    $this->equalTo($data),
                    $this->equalTo($dispatchRenderEvent)
                )
                ->willReturn($expectedResult)
            ;

            $view = (new ClassBuilder)->extends($this->getViewClass())
                ->addProperty($property)
                ->end()
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function () use ($property, $data, $dispatchRenderEvent): string {
                        return $this->renderProperty($property, $data, $dispatchRenderEvent);
                    })
                ->end()
                ->newInstance()
            ;

            $view->{$property} = $otherView;

            $this->assertEquals($expectedResult, $view->render($data, $dispatchRenderEvent));
        });
    });

    testCase('bug solutions', function () {
        test(function () {
            $property = uniqid('property');
            $viewClass = (new ClassBuilder)->extends($this->getViewClass())
                ->addProperty($property)
                    ->setAccess('protected')
                    ->addComment('@ThenLabs\ComposedViews\Annotation\Sidebar')
                ->end()
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (): string {
                        return '';
                    })
                ->end()
            ;

            $viewClass->install();
            $className = $viewClass->getFCQN();

            $view1 = new $className;
            $view2 = new $className;
            $view3 = new $className;

            $this->assertInstanceOf(Sidebar::class, $view1->{$property});
            $this->assertInstanceOf(Sidebar::class, $view2->{$property});
            $this->assertInstanceOf(Sidebar::class, $view3->{$property});
        });
    });
});
