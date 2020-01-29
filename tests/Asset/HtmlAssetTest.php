<?php

namespace NubecuLabs\ComposedViews\Tests\Asset;

use NubecuLabs\Components\DependencyInterface;
use NubecuLabs\ComposedViews\AbstractView;
use NubecuLabs\ComposedViews\Asset\HtmlAsset;
use NubecuLabs\ComposedViews\Tests\TestCase;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('HtmlAssetTest.php', function () {
    setUp(function () {
        $this->asset = new HtmlAsset;
    });

    test('is a component dependency', function () {
        $this->assertInstanceOf(DependencyInterface::class, $this->asset);
    });

    test('is a view', function () {
        $this->assertInstanceOf(AbstractView::class, $this->asset);
    });

    test('the base path is empty', function () {
        $this->assertEmpty($this->asset->getBasePath());
    });

    test('the package path is empty', function () {
        $this->assertEmpty($this->asset->getPackagePath());
    });

    test('the filename is empty', function () {
        $this->assertEmpty($this->asset->getFilename());
    });
});
