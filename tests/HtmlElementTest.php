<?php

namespace NubecuLabs\ComposedViews\Tests;

use NubecuLabs\Components\DependencyInterface;
use NubecuLabs\ComposedViews\AbstractView;
use NubecuLabs\ComposedViews\HtmlElement;
use NubecuLabs\ComposedViews\Tests\TestCase;
use Artyum\HtmlElement\HtmlElement as ArtyumHtmlElement;
use BadMethodCallException;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('HtmlAssetTest.php', function () {
    setUp(function () {
        $this->element = new HtmlElement;
    });

    test('is a view', function () {
        $this->assertInstanceOf(AbstractView::class, $this->element);
    });

    test('is a component dependency', function () {
        $this->assertInstanceOf(DependencyInterface::class, $this->element);
    });

    test('testing setName()', function () {
        $newName = uniqid();

        $this->element->setName($newName);

        $this->assertEquals($newName, $this->element->getName());
    });

    test('is a div element by default', function () {
        $artyumElement = $this->element->getArtyumHtmlElement();

        $this->assertInstanceOf(ArtyumHtmlElement::class, $artyumElement);
        $this->assertEquals('div', $artyumElement->getName());
    });

    test('the element type it is specified by the constructor', function () {
        $tag = uniqid();
        $element = new HtmlElement($tag);

        $artyumElement = $element->getArtyumHtmlElement();

        $this->assertInstanceOf(ArtyumHtmlElement::class, $artyumElement);
        $this->assertEquals($tag, $artyumElement->getName());
    });

    test('getView() returns view from the artyum element', function () {
        $expectedView = uniqid();

        $artyumElement = $this->createMock(ArtyumHtmlElement::class);
        $artyumElement->method('toHtml')->willReturn($expectedView);

        $element = new HtmlElement;
        $element->setArtyumHtmlElement($artyumElement);

        $this->assertEquals($expectedView, $element->getView());
    });

    test('is a proxy to the artyum element property', function () {
        $argument = uniqid();
        $result = uniqid();
        $method = uniqid('method');

        $artyumElement = $this->getMockBuilder(ArtyumHtmlElement::class)
            ->disableOriginalConstructor()
            ->setMethods([$method])
            ->getMock();
        $artyumElement->expects($this->once())
            ->method($method)
            ->with($this->equalTo($argument))
            ->willReturn($result);

        $element = new HtmlElement;
        $element->setArtyumHtmlElement($artyumElement);

        $this->assertEquals($result, $element->{$method}($argument));
    });

    test('throwns an BadMethodCallException when the called method not exists', function () {
        $method = uniqid('method');
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage("Unknow method '{$method}'.");

        $element = new HtmlElement;
        $element->{$method}();
    });
});
