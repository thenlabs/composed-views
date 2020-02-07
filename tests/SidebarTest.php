<?php

namespace ThenLabs\ComposedViews\Tests;

use ThenLabs\ComposedViews\AbstractView;
use ThenLabs\ComposedViews\AbstractCompositeView;
use ThenLabs\ComposedViews\Sidebar;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

testCase('SidebarTest.php', function () {
    setUp(function () {
        $this->sidebar = new Sidebar;
    });

    test('is a composite view', function () {
        $this->assertInstanceOf(AbstractCompositeView::class, $this->sidebar);
    });

    test('the view of the sidebar is equal to the view of the all childs', function () {
        $result1 = '<div>'.uniqid().'</div>';
        $child1 = $this->createMock(AbstractView::class);
        $child1->method('getId')->willReturn('child1');
        $child1->method('render')->willReturn($result1);

        $result2 = '<div>'.uniqid().'</div>';
        $child2 = $this->createMock(AbstractView::class);
        $child2->method('getId')->willReturn('child2');
        $child2->method('render')->willReturn($result2);

        $this->sidebar->addChilds($child1, $child2);

        $this->assertEquals($result1.$result2, $this->sidebar->render());
    });
});
