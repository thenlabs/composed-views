<?php

namespace ThenLabs\ComposedViews\Tests;

use ThenLabs\ComposedViews\Sidebar;
use ThenLabs\ComposedViews\AbstractView;
use ThenLabs\ComposedViews\AbstractCompositeView;
use ThenLabs\ComposedViews\Asset\AbstractAsset;
use ThenLabs\ComposedViews\Exception\UnexistentSidebarException;
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

    test('getSidebars() returns an empty array', function () {
        $view = $this->createMock($this->getViewClass());

        $this->assertEmpty($view->getSidebars());
    });

    test('renderAsset($basePath, $asset) returns result of $asset->render(["basePath" => $basePath])', function () {
        $result = uniqid();
        $basePath = uniqid('http://localhost/');

        $asset = $this->getMockBuilder(AbstractAsset::class)
            ->disableOriginalConstructor()
            ->setMethods(['render'])
            ->getMockForAbstractClass();
        $asset->expects($this->once())
            ->method('render')
            ->with(
                $this->callback(function ($data) use ($basePath) {
                    return $data['basePath'] === $basePath;
                })
            )
            ->willReturn($result)
        ;

        $view = (new ClassBuilder)->extends($this->getViewClass())
            ->addMethod('getDependencies', function () use ($asset): array {
                return [$asset];
            })->end()

            ->addMethod('getView')
                ->setAccess('protected')
                ->setClosure(function (array $data = []) use ($basePath): string {
                    $assets = $this->getDependencies();
                    $asset = array_pop($assets);

                    return $this->renderAsset($basePath, $asset);
                })
            ->end()

            ->newInstance()
        ;

        $view->setBasePath($basePath);

        $this->assertEquals($result, $view->render());
    });

    test('renderAssets($basePath, [$asset1, $asset2]) returns result of $asset1->render(["basePath" => $basePath]), $asset2->render(["basePath" => $basePath]), ....', function () {
        $result1 = uniqid();
        $result2 = uniqid();
        $basePath = uniqid('http://localhost/');

        $asset1 = $this->getMockBuilder(AbstractAsset::class)
            ->disableOriginalConstructor()
            ->setMethods(['render'])
            ->getMockForAbstractClass();
        $asset1->expects($this->once())
            ->method('render')
            ->with(
                $this->callback(function ($data) use ($basePath) {
                    return $data['basePath'] === $basePath;
                })
            )
            ->willReturn($result1)
        ;

        $asset2 = $this->getMockBuilder(AbstractAsset::class)
            ->disableOriginalConstructor()
            ->setMethods(['render'])
            ->getMockForAbstractClass();
        $asset2->expects($this->once())
            ->method('render')
            ->with(
                $this->callback(function ($data) use ($basePath) {
                    return $data['basePath'] === $basePath;
                })
            )
            ->willReturn($result2)
        ;

        $view = (new ClassBuilder)->extends($this->getViewClass())
            ->addMethod('getDependencies', function () use ($asset1, $asset2): array {
                return [$asset1, $asset2];
            })->end()

            ->addMethod('getView')
                ->setAccess('protected')
                ->setClosure(function (array $data = []) use ($basePath): string {
                    $assets = $this->getDependencies();

                    return $this->renderAssets($basePath, $assets);
                })
            ->end()

            ->newInstance()
        ;

        $view->setBasePath($basePath);

        $this->assertEquals($result1.$result2, $view->render());
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

    testCase('exists a view with two sidebars', function () {
        setUp(function () {
            $this->sidebarName1 = $sidebarName1 = uniqid('sidebar');
            $this->sidebarName2 = $sidebarName2 = uniqid('sidebar');

            $this->view = (new ClassBuilder)->extends($this->getViewClass())
                ->addMethod('__construct')
                    ->setClosure(function () use ($sidebarName1, $sidebarName2) {
                        $this->createSidebar($sidebarName1);
                        $this->createSidebar($sidebarName2);
                    })
                ->end()
                ->addMethod('getView')
                    ->setAccess('protected')
                    ->setClosure(function (array $data = []): string {
                        return '<div>'.uniqid().'</div>';
                    })
                ->end()
                ->newInstance();
        });

        test('getSidebars() returns an array with two instances of Sidebar', function () {
            $sidebars = $this->view->getSidebars();

            $this->assertCount(2, $sidebars);
            $this->assertInstanceOf(Sidebar::class, $sidebars[$this->sidebarName1]);
            $this->assertInstanceOf(Sidebar::class, $sidebars[$this->sidebarName2]);
        });

        test('getSidebar($name) returns null when the searched sidebar not exists', function () {
            $this->assertNull($this->view->getSidebar(uniqid()));
        });

        test('getSidebar($name) returns the expected sidebar', function () {
            $this->assertInstanceOf(Sidebar::class, $this->view->getSidebar($this->sidebarName1));
        });

        test('renderSidebar($name) throwns an UnexistentSidebarException when searched not exists', function () {
            $sidebar = uniqid();

            $this->expectException(UnexistentSidebarException::class);
            $this->expectExceptionMessage("The sidebar '{$sidebar}' not exists.");

            $this->view->renderSidebar($sidebar);
        });

        test('renderSidebar($name) returns the sidebar view', function () {
            $result = uniqid();
            $sidebarName = uniqid('sidebar');

            $sidebar = $this->getMockBuilder(Sidebar::class)
                ->setMethods(['render'])
                ->getMock();
            $sidebar->expects($this->once())
                ->method('render')
                ->willReturn($result);

            $this->view->setSidebar($sidebarName, $sidebar);

            $this->assertEquals($result, $this->view->renderSidebar($sidebarName));
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

            $this->view->getSidebar($this->sidebarName1)->addChild($child1);
            $this->view->getSidebar($this->sidebarName2)->addChild($child2);

            $this->assertEquals(
                [$dependencyName1 => $dependency1, $dependencyName2 => $dependency2],
                $this->view->getAdditionalDependencies()
            );
        });
    });
});
