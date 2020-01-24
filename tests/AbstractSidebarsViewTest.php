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
    createMacro('is a composite view', function () {
        test('is a composite view', function () {
            $this->assertInstanceOf(AbstractCompositeView::class, $this->sidebarsView);
        });
    });

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

        useMacro('is a composite view');

        test('getSidebars() returns an empty array', function () {
            $this->assertEmpty($this->sidebarsView->getSidebars());
        });
    });

    testCase('exists a sidebars view that specify two sidebars across getSidebarsNames() method', function () {
        setUp(function () {
            $this->name1 = $name1 = uniqid();
            $this->name2 = $name2 = uniqid();

            $this->sidebarsView = (new ClassBuilder)->extends(AbstractSidebarsView::class)
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (array $data = []): string {
                        return '';
                    })
                ->end()
                ->addMethod('getSidebarsNames')
                    ->setAccess('public')
                    ->setClosure(function () use ($name1, $name2): array {
                        return [$name1, $name2];
                    })
                ->end()
                ->newInstance();
        });

        useMacro('is a composite view');

        test('the view has the expected sidebars', function () {
            $sidebars = $this->sidebarsView->getSidebars();

            $this->assertCount(2, $sidebars);
            $this->assertEquals("sidebar-{$this->name1}", $sidebars[0]->getName());
            $this->assertEquals("sidebar-{$this->name2}", $sidebars[1]->getName());
        });
    });
});
