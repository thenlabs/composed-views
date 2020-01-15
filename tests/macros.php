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

        test('render() returns result of getView()', function () {
            $view = randomString();
            $args = range(0, mt_rand(0, 10));

            $component = $this->getMockBuilder($this->getViewClass())
                ->setMethods(['getView'])
                ->getMockForAbstractClass();
            $component->expects($this->once())
                ->method('getView')
                ->with($this->equalTo($args))
                ->willReturn($view)
            ;

            $this->assertSame($view, $component->render($args));
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
    });
});
