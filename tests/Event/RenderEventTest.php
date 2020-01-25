<?php

namespace NubecuLabs\ComposedViews\Tests\Event;

use NubecuLabs\ComposedViews\Event\RenderEvent;
use NubecuLabs\ComposedViews\AbstractView;
use NubecuLabs\ComposedViews\Tests\TestCase;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use BadMethodCallException;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('RenderEventTest.php', function () {
    test('is a proxy to the proxyCrawler property', function () {
        $method = uniqid('method');
        $argument = uniqid();
        $result = uniqid();

        $pageCrawler = $this->getMockBuilder(HtmlPageCrawler::class)
            ->disableOriginalConstructor()
            ->setMethods([$method])
            ->getMock();
        $pageCrawler->expects($this->once())
            ->method($method)
            ->with($this->equalTo($argument))
            ->willReturn($result);

        $event = new RenderEvent('');

        // install the page crawler inside event.
        (function () use ($pageCrawler) {
            $this->pageCrawler = $pageCrawler;
        })->call($event);

        $this->assertEquals($result, $event->{$method}($argument));
    });

    test('throwns an BadMethodCallException when the called method not exists', function () {
        $method = uniqid('method');
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage("Unknow method '{$method}'.");

        $event = new RenderEvent('');
        $event->{$method}();
    });
});
