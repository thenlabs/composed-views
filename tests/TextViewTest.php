<?php

namespace ThenLabs\ComposedViews\Tests;

use ThenLabs\ComposedViews\AbstractView;
use ThenLabs\ComposedViews\TextView;
use ThenLabs\Components\DependencyInterface;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use BadMethodCallException;

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

    test('is a proxy to the crawler property', function () {
        $method = uniqid('method');
        $argument = uniqid();
        $result = uniqid();

        $crawler = $this->getMockBuilder(HtmlPageCrawler::class)
            ->disableOriginalConstructor()
            ->setMethods([$method])
            ->getMock();
        $crawler->expects($this->once())
            ->method($method)
            ->with($this->equalTo($argument))
            ->willReturn($result);

        $textView = new TextView('');

        // install the crawler inside the view.
        (function () use ($crawler) {
            $this->crawler = $crawler;
        })->call($textView);

        $this->assertEquals($result, $textView->{$method}($argument));
    });

    test('throwns an BadMethodCallException when the called method not exists', function () {
        $method = uniqid('method');
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage("Unknow method '{$method}'.");

        $textView = new TextView('');
        $textView->{$method}();
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
