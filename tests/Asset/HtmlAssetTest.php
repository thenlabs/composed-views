<?php

namespace NubecuLabs\ComposedViews\Tests\Asset;

use NubecuLabs\Components\DependencyInterface;
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

    test('the dependency name it is specified on the constructor', function () {
        $this->assertEquals($this->name, $this->asset->getName());
    });

    test('the artyum html element it is specified on the constructor', function () {
        $this->assertEquals($this->element, $this->asset->getHtmlElement());
    });
});
