<?php

namespace ThenLabs\ComposedViews\Tests;

use ThenLabs\ComposedViews\Sidebar;
use ThenLabs\ComposedViews\AbstractView;
use ThenLabs\ComposedViews\AbstractCompositeView;
use ThenLabs\Components\ComponentInterface;
use ThenLabs\Components\ComponentTrait;
use ThenLabs\Components\CompositeComponentInterface;
use ThenLabs\Components\DependencyInterface;
use ThenLabs\Components\Exception\InvalidChildException;
use ThenLabs\ClassBuilder\ClassBuilder;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('AbstractCompositeViewTest.php', function () {
    createMethod('getViewClass', function () {
        return AbstractCompositeView::class;
    });

    useMacro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php');

    test('is instance of ThenLabs\Components\CompositeComponentInterface', function () {
        $view = $this->createMock($this->getViewClass());

        $this->assertInstanceOf(CompositeComponentInterface::class, $view);
    });

    test('renderChildren() returns result of render each child', function () {
        $result1 = '<div>'.uniqid().'</div>';
        $child1 = $this->createMock(AbstractView::class);
        $child1->method('getId')->willReturn('child1');
        $child1->method('render')->willReturn($result1);

        $result2 = '<div>'.uniqid().'</div>';
        $child2 = $this->createMock(AbstractView::class);
        $child2->method('getId')->willReturn('child2');
        $child2->method('render')->willReturn($result2);

        $parentView = $this->getMockForAbstractClass(AbstractCompositeView::class);
        $parentView->addChilds($child1, $child2);

        $this->assertEquals($result1.$result2, $parentView->renderChildren());
    });

    test('getAdditionalDependencies() includes the sidebars dependencies', function () {
        $dependencyName1 = uniqid('dep');
        $dependencyName2 = uniqid('dep');

        $dependency1 = $this->createMock(DependencyInterface::class);
        $dependency1->method('getName')->willReturn($dependencyName1);

        $dependency2 = $this->createMock(DependencyInterface::class);
        $dependency2->method('getName')->willReturn($dependencyName2);

        $child1 = $this->createMock($this->getViewClass());
        $child1->method('getId')->willReturn('child1');
        $child1->method('getDependencies')->willReturn([$dependency1]);

        $child2 = $this->createMock($this->getViewClass());
        $child2->method('getId')->willReturn('child2');
        $child2->method('getDependencies')->willReturn([$dependency2]);

        $property1 = uniqid('property');
        $property2 = uniqid('property');

        $view = (new ClassBuilder)->extends(AbstractCompositeView::class)
            ->addMethod('getView')
                ->setAccess('protected')
                ->setClosure(function (array $data = []): string {
                    return '';
                })
            ->end()
            ->addProperty($property1)
                ->setAccess('protected')
                ->addComment('@ThenLabs\ComposedViews\Annotation\Sidebar')
            ->end()
            ->addProperty($property2)
                ->setAccess('protected')
                ->addComment('@ThenLabs\ComposedViews\Annotation\Sidebar')
            ->end()
            ->newInstance()
        ;

        $view->{$property1}->addChild($child1);
        $view->{$property2}->addChild($child2);

        $this->assertEquals(
            [$dependencyName1 => $dependency1, $dependencyName2 => $dependency2],
            $view->getAdditionalDependencies()
        );
    });

    testCase('throws an ThenLabs\Components\Exception\InvalidChildException when attempt insert a child that is not a view', function () {
        setUp(function () {
            $this->expectException(InvalidChildException::class);

            $this->view = $this->getMockForAbstractClass(AbstractCompositeView::class);
        });

        test(function () {
            $child = $this->getMockForAbstractClass(ComponentInterface::class);

            $this->view->addChild($child);
        });

        test(function () {
            $component = new class implements ComponentInterface {
                use ComponentTrait;
            };

            $component->setParent($this->view);
        });
    });

    testCase('exists a view with a property with the sidebar annotation', function () {
        setUp(function () {
            $this->propertyName = uniqid('property');

            $this->classBuilder = (new ClassBuilder)->extends($this->getViewClass())
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (array $data = []): string {
                        return '';
                    })
                ->end()
                ->addProperty($this->propertyName)
                    ->setAccess('protected')
                    ->addComment('@ThenLabs\ComposedViews\Annotation\Sidebar')
                ->end();
            ;

            $this->view = $this->classBuilder->newInstance();
        });

        test('the view instance has a public sidebar instance', function () {
            $this->assertInstanceOf(Sidebar::class, $this->view->{$this->propertyName});
        });
    });
});
