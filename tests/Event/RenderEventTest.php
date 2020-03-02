<?php

namespace ThenLabs\ComposedViews\Tests\Event;

use ThenLabs\ComposedViews\Event\RenderEvent;
use ThenLabs\ComposedViews\Tests\TestCase;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use BadMethodCallException;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('RenderEventTest.php', function () {
    test('is a proxy to the proxyCrawler property', function () {
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

        $event = new RenderEvent('', []);

        // install the crawler inside event.
        (function () use ($crawler) {
            $this->crawler = $crawler;
        })->call($event);

        $this->assertEquals($result, $event->{$method}($argument));
    });

    test('throwns an BadMethodCallException when the called method not exists', function () {
        $method = uniqid('method');
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage("Unknow method '{$method}'.");

        $event = new RenderEvent('', []);
        $event->{$method}();
    });
});
