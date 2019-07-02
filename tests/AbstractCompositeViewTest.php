<?php

namespace NubecuLabs\ComposedViews\Tests;

use NubecuLabs\ComposedViews\AbstractCompositeView;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('AbstractCompositeViewTest.php', function () {
    createMethod('getViewClass', function () {
        return AbstractCompositeView::class;
    });

    useMacro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php');
});
