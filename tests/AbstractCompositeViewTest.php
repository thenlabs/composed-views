<?php

namespace NubecuLabs\ComposedViews\Tests;

use NubecuLabs\ComposedViews\AbstractCompositeView;
use NubecuLabs\Components\CompositeComponentInterface;

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
});
