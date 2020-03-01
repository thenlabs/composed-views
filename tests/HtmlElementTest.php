<?php

namespace ThenLabs\ComposedViews\Tests;

use ThenLabs\ComposedViews\AbstractCompositeView;
use ThenLabs\ComposedViews\HtmlElement;
use ThenLabs\Components\DependencyInterface;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('HtmlElementTest.php', function () {
    testCase('$element = new HtmlElement', function () {
        setUp(function () {
            $this->element = new HtmlElement;
        });

        test('is instance of AbstractCompositeView', function () {
            $this->assertInstanceOf(AbstractCompositeView::class, $this->element);
        });

        test('is instance of DependencyInterface', function () {
            $this->assertInstanceOf(DependencyInterface::class, $this->element);
        });

        test('$element->getTagName() === "div"', function () {
            $this->assertEquals('div', $this->element->getTagName());
        });

        test('$element->getAttributes() === []', function () {
            $this->assertSame([], $this->element->getAttributes());
        });

        testCase('$element->setTagName($tagName)', function () {
            setUp(function () {
                $this->tagName = uniqid('tag');
                $this->element->setTagName($this->tagName);
            });

            test('$element->getTagName() === $tagName', function () {
                $this->assertEquals($this->tagName, $this->element->getTagName());
            });
        });

        testCase('$element->setAttributes($array)', function () {
            setUp(function () {
                $this->array = range(0, mt_rand(0, 10));
                $this->element->setAttributes($this->array);
            });

            test('$element->getAttributes() === $array', function () {
                $this->assertSame($this->array, $this->element->getAttributes());
            });
        });

        testCase('$element->setName($name)', function () {
            setUp(function () {
                $this->name = uniqid();
                $this->element->setName($this->name);
            });

            test('$element->getName() === $name', function () {
                $this->assertEquals($this->name, $this->element->getName());
            });
        });
    });
});
