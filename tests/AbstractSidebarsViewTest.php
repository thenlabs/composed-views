<?php

namespace NubecuLabs\ComposedViews\Tests;

use NubecuLabs\ComposedViews\AbstractView;
use NubecuLabs\ComposedViews\AbstractCompositeView;
use NubecuLabs\ComposedViews\AbstractSidebarsView;
use NubecuLabs\ComposedViews\Sidebar;
use NubecuLabs\ClassBuilder\ClassBuilder;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('AbstractSidebarsViewTest.php', function () {
    testCase('exists a sidebars view that not specify none sidebar name', function () {
        setUp(function () {
            $this->sidebarsView = new class extends AbstractSidebarsView
            {
                public function getView(array $data = []): string
                {
                    return '';
                }
            };
        });

        test('is a composite view', function () {
            $this->assertInstanceOf(AbstractCompositeView::class, $this->sidebarsView);
        });

        test('getSidebars() returns an empty array', function () {
            $this->assertEmpty($this->sidebarsView->getSidebars());
        });

        test('getSidebar($name) returns null when not exists none sidebar with that name', function () {
            $this->assertNull($this->sidebarsView->getSidebar(uniqid()));
        });
    });

    testCase('exists a sidebars view that specify two sidebars', function () {
        setUp(function () {
            $this->name1 = uniqid();
            $this->name2 = uniqid();

            $this->classBuilder = (new ClassBuilder)->extends(AbstractSidebarsView::class)
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (array $data = []): string {
                        return '';
                    })
                ->end();
        });

        createMacro('check expected sidebars', function () {
            test('the view has the expected sidebars', function () {
                $sidebars = $this->sidebarsView->getSidebars();

                $this->assertCount(2, $sidebars);
                $this->assertEquals("sidebar-{$this->name1}", $sidebars[0]->getName());
                $this->assertEquals("sidebar-{$this->name2}", $sidebars[1]->getName());
            });

            test('getSidebar($name) returns the expected sidebar', function () {
                $sidebar2 = $this->sidebarsView->getSidebar($this->name2);

                $this->assertInstanceOf(Sidebar::class, $sidebar2);
                $this->assertEquals("sidebar-{$this->name2}", $sidebar2->getName());
            });
        });

        testCase('across getSidebarsNames() method', function () {
            setUp(function () {
                $name1 = $this->name1;
                $name2 = $this->name2;

                $this->classBuilder
                    ->addMethod('getSidebarsNames')
                        ->setAccess('public')
                        ->setClosure(function () use ($name1, $name2): array {
                            return [$name1, $name2];
                        })
                    ->end();

                $this->sidebarsView = $this->classBuilder->newInstance();
            });

            useMacro('check expected sidebars');
        });

        testCase('across the sidebarsNames property', function () {
            setUp(function () {
                $this->classBuilder
                    ->addProperty('sidebarsNames')
                        ->setAccess('protected')
                        ->setValue([$this->name1, $this->name2])
                    ->end();

                $this->sidebarsView = $this->classBuilder->newInstance();
            });

            useMacro('check expected sidebars');
        });
    });
});
