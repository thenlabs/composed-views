<?php

namespace NubecuLabs\ComposedViews\Tests;

use NubecuLabs\ComposedViews\AbstractCompositeView;
use NubecuLabs\Components\ComponentInterface;
use NubecuLabs\Components\ComponentTrait;
use NubecuLabs\Components\CompositeComponentInterface;
use NubecuLabs\Components\Exception\InvalidChildException;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('AbstractCompositeViewTest.php', function () {
    createMethod('getViewClass', function () {
        return AbstractCompositeView::class;
    });

    useMacro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php');

    test('is instance of NubecuLabs\Components\CompositeComponentInterface', function () {
        $view = $this->createMock($this->getViewClass());

        $this->assertInstanceOf(CompositeComponentInterface::class, $view);
    });

    testCase('throws an NubecuLabs\Components\Exception\InvalidChildException when attempt insert a child that is not a view', function () {
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
});
