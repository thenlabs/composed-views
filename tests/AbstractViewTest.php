<?php

namespace NubecuLabs\ComposedViews\Tests;

use NubecuLabs\ComposedViews\AbstractView;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('AbstractViewTest.php', function () {
    createMethod('getViewClass', function () {
        return AbstractView::class;
    });

    useMacro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php');
});
