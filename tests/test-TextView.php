<?php

namespace ThenLabs\ComposedViews\Tests;

use BadMethodCallException;
use ThenLabs\Components\DependencyInterface;
use ThenLabs\ComposedViews\AbstractView;
use ThenLabs\ComposedViews\TextView;
use Wa72\HtmlPageDom\HtmlPageCrawler;

setTestCaseClass(TestCase::class);

testCase('test-TextView.php', function () {
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

    test('the view is the result of the crawler', function () {
        $textView = new TextView('<p>my p <strong>my strong <span>my span</span></strong></p>');
        $attribute = uniqid('attr');
        $value = uniqid();

        $textView->filter('span')->setAttribute($attribute, $value);
        $expected = "<p>my p <strong>my strong <span {$attribute}=\"{$value}\">my span</span></strong></p>";

        $this->assertEquals($expected, $textView->render());
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
