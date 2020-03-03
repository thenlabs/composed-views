<?php

namespace ThenLabs\ComposedViews\Tests\Asset;

use ThenLabs\ComposedViews\Asset\Stylesheet;
use ThenLabs\ComposedViews\Asset\Script;
use ThenLabs\ComposedViews\Tests\TestCase;
use ThenLabs\ClassBuilder\ClassBuilder;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('ScriptTest.php', function () {
    setUp(function () {
        $this->name = uniqid('name');
        $this->version = uniqid();
        $this->uri = uniqid('uri');
        $this->basePath = uniqid('http://localhost:8080/');
    });

    createMacro('tests for constructor arguments', function () {
        test('$script->getName() === $name', function () {
            $this->assertSame($this->name, $this->script->getName());
        });

        test('$script->getVersion() === $version', function () {
            $this->assertSame($this->version, $this->script->getVersion());
        });

        test('$script->getUri() === $uri', function () {
            $this->assertSame($this->uri, $this->script->getUri());
        });
    });

    testCase('$script = new Script($name, $version, $uri)', function () {
        setUp(function () {
            $this->script = new Script($this->name, $this->version, $this->uri);
        });

        useMacro('tests for constructor arguments');

        test('render() returns the expected view', function () {
            $expected = "<script src=\"{$this->basePath}{$this->uri}\"></script>";

            $this->assertEquals($expected, $this->script->render(['basePath' => $this->basePath]));
        });
    });

    testCase('exists a custom script', function () {
        setUp(function () {
            $this->source = $source = uniqid();

            $this->script = (new ClassBuilder)->extends(Script::class)
                ->addMethod('getSource', function () use ($source): ?string {
                    return $source;
                })->end()
                ->newInstance($this->name, $this->version, $this->uri)
            ;
        });

        useMacro('tests for constructor arguments');

        test('render() returns the expected view', function () {
            $expected = "<script src=\"{$this->basePath}{$this->uri}\">{$this->source}</script>";

            $this->assertEquals($expected, $this->script->render(['basePath' => $this->basePath]));
        });
    });
});
