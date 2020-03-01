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

        test('has the expected view', function () {
            $this->assertEquals('<div></div>', $this->element->render());
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

            test('has the expected view', function () {
                $this->assertEquals(
                    "<{$this->tagName}></{$this->tagName}>",
                    $this->element->render()
                );
            });
        });

        testCase('$element->setAttributes($attributes)', function () {
            setUp(function () {
                $this->attr1 = uniqid('attr');
                $this->value1 = uniqid();

                $this->attr2 = uniqid('attr');

                $this->attributes = [
                    $this->attr1 => $this->value1,
                    $this->attr2 => null,
                ];

                $this->element->setAttributes($this->attributes);
            });

            test('$element->getAttributes() === $attributes', function () {
                $this->assertSame($this->attributes, $this->element->getAttributes());
            });

            test('has the expected view', function () {
                $this->assertEquals(
                    "<div {$this->attr1}=\"{$this->value1}\" {$this->attr2}></div>",
                    $this->element->render()
                );
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

            test('has the expected view', function () {
                $this->assertEquals(
                    "<div>{$this->innerHtml}</div>",
                    $this->element->render()
                );
            });
        });

        testCase('$element->setEndTag(false)', function () {
            setUp(function () {
                $this->element->setEndTag(false);
            });

            test('$element->hasEndTag() === false', function () {
                $this->assertFalse($this->element->hasEndTag());
            });

            test('has the expected view', function () {
                $this->assertEquals(
                    '<div>',
                    $this->element->render()
                );
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
