<?php

namespace ThenLabs\ComposedViews\Tests\Asset;

use ThenLabs\ComposedViews\Asset\Stylesheet;
use ThenLabs\ComposedViews\Asset\Script;
use ThenLabs\ComposedViews\Tests\TestCase;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('ScriptTest.php', function () {
    testCase('$script = new Script($name, $version, $uri)', function () {
        setUp(function () {
            $this->name = uniqid('name');
            $this->version = uniqid();
            $this->uri = uniqid('uri');
            $this->script = new Script($this->name, $this->version, $this->uri);
        });

        test('$script->getName() === $name', function () {
            $this->assertSame($this->name, $this->script->getName());
        });

        test('$script->getVersion() === $version', function () {
            $this->assertSame($this->version, $this->script->getVersion());
        });

        test('$script->getUri() === $uri', function () {
            $this->assertSame($this->uri, $this->script->getUri());
        });

        test('render() returns the expected view', function () {
            $basePath = uniqid('http://localhost:8080/');
            $expected = "<script src=\"{$basePath}{$this->uri}\"></script>";

            $this->assertEquals($expected, $this->script->render(compact('basePath')));
        });
    });
});
