<?php

use NubecuLabs\Components\ComponentInterface;

createMacro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php', function () {
    testCase("it is created a new view component", function () {
        setUp(function () {
            $this->component = $this->getMockForAbstractClass($this->getViewClass());
        });

        test('is instance of NubecuLabs\Components\ComponentInterface', function () {
            $this->assertInstanceOf(ComponentInterface::class, $this->component);
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

        test('render() returns result of getView()', function () {
            $view = randomString();

            $component = $this->getMockBuilder($this->getViewClass())
                ->setMethods(['getView'])
                ->getMockForAbstractClass();
            $component->expects($this->once())
                ->method('getView')
                ->willReturn($view)
            ;

            $this->assertSame($view, $component->render());
        });

        testCase('it is assigned a custom view by the user', function () {
            setUp(function () {
                $this->view = randomString();

                $this->component = $this->getMockBuilder($this->getViewClass())
                    ->setMethods(['getView'])
                    ->getMockForAbstractClass();
                $this->component->expects($this->never())
                    ->method('getView')
                ;

                $this->component->setView($this->view);
            });

            test('render() returns the user view', function () {
                $this->assertSame($this->view, $this->component->render());
            });
        });
    });
});
