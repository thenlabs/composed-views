<?php

namespace ThenLabs\ComposedViews\Tests\Event;

use ThenLabs\ComposedViews\Asset\LinkAsset;
use ThenLabs\ComposedViews\Tests\TestCase;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('LinkAssetTest.php', function () {
    testCase('$link = new LinkAsset($name, $uri)', function () {
        setUp(function () {
            $this->name = uniqid('name');
            $this->version = uniqid();
            $this->uri = uniqid('uri');
            $this->link = new LinkAsset($this->name, $this->version, $this->uri);
        });

        test('$link->getName() === $name', function () {
            $this->assertSame($this->name, $this->link->getName());
        });

        test('$link->getVersion() === $version', function () {
            $this->assertSame($this->version, $this->link->getVersion());
        });

        test('$link->getUri() === $uri', function () {
            $this->assertSame($this->uri, $this->link->getUri());
        });
    });
});
