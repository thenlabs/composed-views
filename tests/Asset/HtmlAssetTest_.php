<?php

namespace NubecuLabs\ComposedViews\Tests\Asset;

use NubecuLabs\Components\DependencyInterface;
use NubecuLabs\ComposedViews\AbstractView;
use NubecuLabs\ComposedViews\Asset\HtmlAsset;
use NubecuLabs\ComposedViews\Tests\TestCase;
use Artyum\HtmlElement\HtmlElement;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('HtmlAssetTest.php', function () {
    setUp(function () {
        $this->name = uniqid();
        $this->element = new HtmlElement;

        $this->asset = new HtmlAsset($this->name, $this->element);
    });

    test('is a component dependency', function () {
        $this->assertInstanceOf(DependencyInterface::class, $this->asset);
    });

    test('is a view', function () {
        $this->assertInstanceOf(AbstractView::class, $this->asset);
    });

    test('testing setName()', function () {
        $newName = uniqid();

        $this->asset->setName($newName);

        $this->assertEquals($newName, $this->asset->getName());
    });

    test('getView() returns result of the html element', function () {
        $name = uniqid();
        $expectedContent = uniqid();

        $element = $this->createMock(HtmlElement::class);
        $element->method('toHtml')->willReturn($expectedContent);

        $asset = new HtmlAsset($name, $element);

        $this->assertEquals($expectedContent, $asset->getView());
    });

    test('the dependency name it is specified on the constructor', function () {
        $this->assertEquals($this->name, $this->asset->getName());
    });

    test('the artyum html element it is specified on the constructor', function () {
        $this->assertEquals($this->element, $this->asset->getHtmlElement());
    });
});
