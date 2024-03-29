<?php

namespace ThenLabs\ComposedViews\Tests\Asset;

use ThenLabs\ComposedViews\Asset\Stylesheet;
use ThenLabs\ComposedViews\Tests\TestCase;

setTestCaseClass(TestCase::class);

testCase('test-Stylesheet.php', function () {
    testCase('$stylesheet = new Stylesheet($name, $version, $uri)', function () {
        setUp(function () {
            $this->name = uniqid('name');
            $this->version = uniqid();
            $this->uri = uniqid('uri');
            $this->stylesheet = new Stylesheet($this->name, $this->version, $this->uri);
        });

        test('$stylesheet->getName() === $name', function () {
            $this->assertSame($this->name, $this->stylesheet->getName());
        });

        test('$stylesheet->getVersion() === $version', function () {
            $this->assertSame($this->version, $this->stylesheet->getVersion());
        });

        test('$stylesheet->getUri() === $uri', function () {
            $this->assertSame($this->uri, $this->stylesheet->getUri());
        });

        test('render() returns the expected view', function () {
            $basePath = uniqid('http://localhost:8080/');
            $expected = "<link rel=\"stylesheet\" href=\"{$basePath}{$this->uri}\">";

            $this->assertEquals($expected, $this->stylesheet->render(compact('basePath')));
        });
    });
});
