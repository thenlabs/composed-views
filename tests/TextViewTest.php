<?php

namespace ThenLabs\ComposedViews\Tests;

use ThenLabs\ComposedViews\AbstractView;
use ThenLabs\ComposedViews\TextView;
use ThenLabs\Components\DependencyInterface;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('TextViewTest.php', function () {
    setUp(function () {
        $this->content = uniqid();
        $this->view = new TextView($this->content);
    });

    test('is instance of AbstractView', function () {
        $this->assertInstanceOf(AbstractView::class, $this->view);
    });

    test('is instance of DependencyInterface', function () {
        $this->assertInstanceOf(DependencyInterface::class, $this->view);
    });

    test('the content of the view is assigned in the constructor', function () {
        $this->assertEquals($this->content, $this->view->render());
    });

    testCase('$view->setName($name)', function () {
        setUp(function () {
            $this->name = uniqid();
            $this->view->setName($this->name);
        });

        test('$view->getName() === $name', function () {
            $this->assertEquals($this->name, $this->view->getName());
        });
    });
});
