<?php

createMacro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php', function () {
    testCase('the render() method returns result for string convertion', function () {
        setUp(function () {
            $this->result = randomString();

            $this->view = $this->getMockBuilder($this->getViewClass())
                ->setMethods(['render'])
                ->getMock();
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
