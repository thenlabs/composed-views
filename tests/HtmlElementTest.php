<?php

namespace ThenLabs\ComposedViews\Tests;

use ThenLabs\ComposedViews\AbstractCompositeView;
use ThenLabs\ComposedViews\HtmlElement;
use ThenLabs\ComposedViews\TextView;
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

        test('getAttribute($attribute) === null', function () {
            $this->assertNull($this->element->getAttribute(uniqid()));
        });

        test('hasAttribute($attribute) === false', function () {
            $this->assertFalse($this->element->hasAttribute(uniqid()));
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

            test('hasAttribute($attr1) === true', function () {
                $this->assertTrue($this->element->hasAttribute($this->attr1));
            });

            test('getAttribute($attr1) === $attr1', function () {
                $this->assertEquals($this->value1, $this->element->getAttribute($this->attr1));
            });
        });

        testCase('$element->setAttribute($name, $value)', function () {
            setUp(function () {
                $this->name = uniqid('attr');
                $this->value = uniqid();

                $this->element->setAttribute($this->name, $this->value);
            });

            test('$element->getAttribute($name) === $value', function () {
                $this->assertSame($this->value, $this->element->getAttribute($this->name));
            });

            test('has the expected view', function () {
                $this->assertEquals(
                    "<div {$this->name}=\"{$this->value}\"></div>",
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
                    $this->element->render([], false)
                );
            });
        });

        testCase('$element->setSelfClosingTag(true)', function () {
            setUp(function () {
                $this->element->setSelfClosingTag(true);
            });

            createMacro('has the expected view', function () {
                test('has the expected view', function () {
                    $this->assertEquals(
                        '<div />',
                        $this->element->render([], false)
                    );
                });
            });

            test('$element->hasSelfClosingTag() === true', function () {
                $this->assertTrue($this->element->hasSelfClosingTag());
            });

            test('$element->hasEndTag() === false', function () {
                $this->assertFalse($this->element->hasEndTag());
            });

            useMacro('has the expected view');

            testCase('$element->setInnerHtml($innerHtml)', function () {
                setUp(function () {
                    $this->element->setInnerHtml(uniqid());
                });

                useMacro('has the expected view');
            });
        });

        testCase('$element->addChild($child1)', function () {
            setUp(function () {
                $this->view1 = uniqid();
                $child1 = new TextView($this->view1);
                $this->element->addChild($child1);
            });

            test('$element->getInnerHtml() === $child1->render()', function () {
                $this->assertSame($this->view1, $this->element->getInnerHtml());
            });

            test('has the expected view', function () {
                $this->assertEquals(
                    "<div>{$this->view1}</div>",
                    $this->element->render()
                );
            });

            testCase('$element->addChild($child2)', function () {
                setUp(function () {
                    $this->view2 = uniqid();
                    $child2 = new TextView($this->view2);
                    $this->element->addChild($child2);
                });

                test('$element->getInnerHtml() === $child1->render().$child2->render()', function () {
                    $expected = $this->view1.$this->view2;
                    $this->assertSame($expected, $this->element->getInnerHtml());
                });

                test('has the expected view', function () {
                    $this->assertEquals(
                        "<div>{$this->view1}{$this->view2}</div>",
                        $this->element->render()
                    );
                });

                testCase('$element->setInnerHtml($child3)', function () {
                    setUp(function () {
                        $this->view3 = uniqid();
                        $child3 = new TextView($this->view3);
                        $this->element->setInnerHtml($child3);
                    });

                    test('$element->getInnerHtml() === $child3->render()', function () {
                        $this->assertSame($this->view3, $this->element->getInnerHtml());
                    });

                    test('has the expected view', function () {
                        $this->assertEquals(
                            "<div>{$this->view3}</div>",
                            $this->element->render()
                        );
                    });
                });
            });
        });
    });
});
