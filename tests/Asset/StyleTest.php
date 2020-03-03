<?php

namespace ThenLabs\ComposedViews\Tests\Asset;

use ThenLabs\ComposedViews\Asset\Style;
use ThenLabs\ComposedViews\Tests\TestCase;
use ThenLabs\ClassBuilder\ClassBuilder;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('StyleTest.php', function () {
    setUp(function () {
        $this->name = uniqid('name');
        $this->version = uniqid();
        $this->uri = uniqid('uri');
    });

    createMacro('tests for constructor arguments', function () {
        test('$style->getName() === $name', function () {
            $this->assertSame($this->name, $this->style->getName());
        });

        test('$style->getVersion() === $version', function () {
            $this->assertSame($this->version, $this->style->getVersion());
        });

        test('$style->getUri() === $uri', function () {
            $this->assertSame($this->uri, $this->style->getUri());
        });
    });

    testCase('$style = new Style($name, $version, $uri)', function () {
        setUp(function () {
            $this->style = new Style($this->name, $this->version, $this->uri);
        });

        useMacro('tests for constructor arguments');

        test('render() returns the expected view', function () {
            $expected = "<style></style>";

            $this->assertEquals($expected, $this->style->render());
        });
    });

    testCase('exists a custom style', function () {
        setUp(function () {
            $this->source = $source = uniqid();

            $this->style = (new ClassBuilder)->extends(Style::class)
                ->addMethod('getSource', function () use ($source): ?string {
                    return $source;
                })->end()
                ->newInstance($this->name, $this->version, $this->uri)
            ;
        });

        useMacro('tests for constructor arguments');

        test('render() returns the expected view', function () {
            $expected = "<style>{$this->source}</style>";

            $this->assertEquals($expected, $this->style->render());
        });
    });
});
