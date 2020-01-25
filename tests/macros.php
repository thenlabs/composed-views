<?php

use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\ClassBuilder\ClassBuilder;
use NubecuLabs\ComposedViews\Event\RenderEvent;
use Wa72\HtmlPageDom\HtmlPageCrawler;

createMacro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php', function () {
    test('the view is instance of NubecuLabs\Components\ComponentInterface', function () {
        $view = $this->getMockForAbstractClass($this->getViewClass());

        $this->assertInstanceOf(ComponentInterface::class, $view);
    });

    test('render($data) returns result of getView($data)', function () {
        $viewStr = randomString();
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
        $closure = function () {};

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

    testCase('__toString() returns result of the render() method', function () {
        setUp(function () {
            $this->result = randomString();

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

        testCase('annotation by default', function () {
            setUp(function () {
                $this->propertyName = uniqid('property');
                $this->propertyValue = uniqid();

                $this->getterName = 'get'.ucfirst($this->propertyName);
                $this->setterName = 'set'.ucfirst($this->propertyName);

                $this->classBuilder
                    ->addProperty($this->propertyName)
                        ->setAccess('protected')
                        ->addComment('@NubecuLabs\ComposedViews\Annotation\ViewData')
                        ->setValue($this->propertyValue)
                    ->end();

                $this->view = $this->classBuilder->newInstance();
            });

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
    });
});
