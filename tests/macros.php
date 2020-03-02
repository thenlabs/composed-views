<?php

namespace ThenLabs\ComposedViews\Tests;

use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\DependencyInterface;
use ThenLabs\ClassBuilder\ClassBuilder;
use ThenLabs\ComposedViews\Sidebar;
use ThenLabs\ComposedViews\Exception\UnexistentSidebarException;
use ThenLabs\ComposedViews\Exception\UnexistentPropertyException;
use ThenLabs\ComposedViews\Event\RenderEvent;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use BadMethodCallException;

createMacro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php', function () {
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

    testCase('exists a view', function () {
        setUp(function () {
            $this->view = (new ClassBuilder)->extends($this->getViewClass())
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (array $data = []): string {
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
    });

    testCase('exists a view with an annotated data', function () {
        setUp(function () {
            $this->classBuilder = (new ClassBuilder)->extends($this->getViewClass())
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (array $data = []): string {
                        return '';
                    })
                ->end();
        });

        createMacro('getter and setter tests', function () {
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
                        ->addComment('@ThenLabs\ComposedViews\Annotation\ViewData')
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
                        ->addComment("@ThenLabs\ComposedViews\Annotation\ViewData(getter=\"{$this->getterName}\", setter=\"{$this->setterName}\")")
                        ->setValue($this->propertyValue)
                    ->end();

                $this->view = $this->classBuilder->newInstance();
            });

            useMacro('getter and setter tests');
        });
    });

    testCase('exists a view with an annotated data where it is used for render the view', function () {
        setUp(function () {
            $this->view = (new ClassBuilder)->extends($this->getViewClass())
                ->addProperty('attrName')
                    ->setAccess('protected')
                    ->addComment('@ThenLabs\ComposedViews\Annotation\ViewData')
                ->end()
                ->addProperty('value')
                    ->setAccess('protected')
                    ->addComment('@ThenLabs\ComposedViews\Annotation\ViewData')
                ->end()
                ->addProperty('content')
                    ->setAccess('protected')
                    ->addComment('@ThenLabs\ComposedViews\Annotation\ViewData')
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

    testCase('the renderPropertyView() method', function () {
        test('throwns an UnexistentPropertyException when the specified property not exists', function () {
            $property = uniqid('property');

            $this->expectException(UnexistentPropertyException::class);
            $this->expectExceptionMessage("The property '{$property}' not exists.");

            $view = (new ClassBuilder)->extends($this->getViewClass())
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (array $data = []) use ($property): string {
                        $this->renderPropertyView($property); // throwns exception.
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
                    ->setClosure(function (array $data = []) use ($property): string {
                        return $this->renderPropertyView($property);
                    })
                ->end()
                ->newInstance()
            ;

            $this->assertEmpty($view->render());
        });

        test('returns view of the view component in property', function () {
            $property = uniqid('property');
            $expectedResult = uniqid();
            $data = range(1, mt_rand(1, 10));
            $dispatchRenderEvent = boolval(mt_rand(0, 1));

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
                    ->setClosure(function (array $argData = [], bool $dispatchArg = true) use ($property, $data, $dispatchRenderEvent): string {
                        return $this->renderPropertyView($property, $data, $dispatchRenderEvent);
                    })
                ->end()
                ->newInstance()
            ;

            $view->{$property} = $otherView;

            $this->assertEquals($expectedResult, $view->render($data, $dispatchRenderEvent));
        });
    });
});
