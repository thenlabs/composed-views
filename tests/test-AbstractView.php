<?php

namespace ThenLabs\ComposedViews\Tests;

use ThenLabs\ComposedViews\AbstractView;

setTestCaseClass(TestCase::class);

testCase('test-AbstractView.php', function () {
    method('getViewClass', function () {
        return AbstractView::class;
    });

    useMacro('commons for AbstractViewTest.php and AbstractCompositeViewTest.php');
});
