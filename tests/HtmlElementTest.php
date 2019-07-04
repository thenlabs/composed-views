<?php

namespace NubecuLabs\ComposedViews\Tests;

use NubecuLabs\ComposedViews\AbstractView;
use NubecuLabs\ComposedViews\HtmlElement;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('HtmlElementTest.php', function () {
    testCase('it is created a new instance of html element with default arguments', function () {
        setUp(function () {
            $this->element = new HtmlElement;
        });

        test('is instance of AbstractView', function () {
            $this->assertInstanceOf(AbstractView::class, $this->element);
        });

        test('getTagName() == "div"', function () {
            $this->assertEquals('div', $this->element->getTagName());
        });

        test('getAttributes() == []', function () {
            $this->assertEquals([], $this->element->getAttributes());
        });

        test('getContent() === null', function () {
            $this->assertNull($this->element->getContent());
        });

        test('getCloseTag() === true', function () {
            $this->assertTrue($this->element->getCloseTag());
        });

        test('render() returns the expected view', function () {
            $this->assertEquals('<div></div>', $this->element->render());
        });

        testCase("it is assigned a new tag name", function () {
            setUp(function () {
                $this->tagName = randomString('tag');
                $this->element->setTagName($this->tagName);
            });

            test('getTagName() returns the new tag name value', function () {
                $this->assertEquals($this->tagName, $this->element->getTagName());
            });
        });

        testCase("it is assigned a new attributes", function () {
            setUp(function () {
                $this->attributes = [
                    'attr1' => randomString(),
                    'attr2' => ['val1', 'val2']
                ];

                $this->element->setAttributes($this->attributes);
            });

            test('getAttributes() returns the new attributes', function () {
                $this->assertEquals($this->attributes, $this->element->getAttributes());
            });
        });

        testCase("it is assigned a new content", function () {
            setUp(function () {
                $this->content = randomString();
                $this->element->setContent($this->content);
            });

            test('getContent() returns the new inner html value', function () {
                $this->assertEquals($this->content, $this->element->getContent());
            });
        });

        testCase("it is assigned false to close tag", function () {
            setUp(function () {
                $this->element->setCloseTag(false);
            });

            test('getCloseTag() returns false', function () {
                $this->assertFalse($this->element->getCloseTag());
            });
        });
    });
});
