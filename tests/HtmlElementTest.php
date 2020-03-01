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

        test('$element->getInnerHtml() === ""', function () {
            $this->assertSame('', $this->element->getInnerHtml());
        });

        test('$element->hasEndTag() === true', function () {
            $this->assertTrue($this->element->hasEndTag());
        });

        test('$element->hasSelfClosingTag() === false', function () {
            $this->assertFalse($this->element->hasSelfClosingTag());
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

        testCase('$element->setInnerHtml($innerHtml)', function () {
            setUp(function () {
                $this->innerHtml = uniqid();
                $this->element->setInnerHtml($this->innerHtml);
            });

            test('$element->getInnerHtml() === $innerHtml', function () {
                $this->assertSame($this->innerHtml, $this->element->getInnerHtml());
            });
        });

        testCase('$element->setEndTag(false)', function () {
            setUp(function () {
                $this->element->setEndTag(false);
            });

            test('$element->hasEndTag() === false', function () {
                $this->assertFalse($this->element->hasEndTag());
            });
        });

        testCase('$element->setSelfClosingTag(false)', function () {
            setUp(function () {
                $this->element->setSelfClosingTag(false);
            });

            test('$element->hasSelfClosingTag() === false', function () {
                $this->assertFalse($this->element->hasSelfClosingTag());
            });
        });
    });
});
