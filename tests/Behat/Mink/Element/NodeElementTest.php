<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Selector\SelectorsHandler;

require_once 'ElementTest.php';

/**
 * @group unittest
 */
class NodeElementTest extends ElementTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;

    /**
     * @var SelectorsHandler
     */
    private $selectorsHandler;

    protected function setUp()
    {
        $this->driver = $this->getMock('Behat\Mink\Driver\DriverInterface');
        $this->selectorsHandler = new SelectorsHandler();
    }

    public function testGetXpath()
    {
        $node = new NodeElement('some custom xpath', $this->driver, $this->selectorsHandler);

        $this->assertEquals('some custom xpath', $node->getXpath());
        $this->assertNotEquals('not some custom xpath', $node->getXpath());
    }

    public function testGetText()
    {
        $node = new NodeElement('text_tag', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('getText')
            ->with('text_tag')
            ->will($this->returnValue('val1'));

        $this->assertEquals('val1', $node->getText());
    }

    public function testHasAttribute()
    {
        $node = new NodeElement('input_tag', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->with('input_tag', 'href')
            ->will($this->onConsecutiveCalls(null, 'http://...'));

        $this->assertFalse($node->hasAttribute('href'));
        $this->assertTrue($node->hasAttribute('href'));
    }

    public function testGetAttribute()
    {
        $node = new NodeElement('input_tag', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('getAttribute')
            ->with('input_tag', 'href')
            ->will($this->returnValue('http://...'));

        $this->assertEquals('http://...', $node->getAttribute('href'));
    }

    public function testHasClass()
    {
        $node = new NodeElement('input_tag', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->exactly(6))
            ->method('getAttribute')
            ->with('input_tag', 'class')
            ->will($this->returnValue('class1 class2'));

        $this->assertTrue($node->hasClass('class1'));
        $this->assertTrue($node->hasClass('class2'));
        $this->assertFalse($node->hasClass('class3'));
    }

    public function testGetValue()
    {
        $node = new NodeElement('input_tag', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('getValue')
            ->with('input_tag')
            ->will($this->returnValue('val1'));

        $this->assertEquals('val1', $node->getValue());
    }

    public function testSetValue()
    {
        $node = new NodeElement('input_tag', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('setValue')
            ->with('input_tag', 'new_val');

        $node->setValue('new_val');
    }

    public function testClick()
    {
        $node = new NodeElement('link_or_button', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('click')
            ->with('link_or_button');

        $node->click();
    }

    public function testRightClick()
    {
        $node = new NodeElement('elem', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('rightClick')
            ->with('elem');

        $node->rightClick();
    }

    public function testDoubleClick()
    {
        $node = new NodeElement('elem', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('doubleClick')
            ->with('elem');

        $node->doubleClick();
    }

    public function testCheck()
    {
        $node = new NodeElement('checkbox_or_radio', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('check')
            ->with('checkbox_or_radio');

        $node->check();
    }

    public function testUncheck()
    {
        $node = new NodeElement('checkbox_or_radio', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('uncheck')
            ->with('checkbox_or_radio');

        $node->uncheck();
    }

    public function testSelectOption()
    {
        $node = new NodeElement('select', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('selectOption')
            ->with('select', 'item1');

        $node->selectOption('item1');
    }

    public function testGetTagName()
    {
        $node = new NodeElement('html//h3', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('getTagName')
            ->with('html//h3')
            ->will($this->returnValue('h3'));

        $this->assertEquals('h3', $node->getTagName());
    }

    public function testIsVisible()
    {
        $node = new NodeElement('some_xpath', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->exactly(2))
            ->method('isVisible')
            ->with('some_xpath')
            ->will($this->onConsecutiveCalls(true, false));

        $this->assertTrue($node->isVisible());
        $this->assertFalse($node->isVisible());
    }

    public function testIsChecked()
    {
        $node = new NodeElement('some_xpath', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->with('some_xpath')
            ->will($this->onConsecutiveCalls(true, false));

        $this->assertTrue($node->isChecked());
        $this->assertFalse($node->isChecked());
    }

    public function testIsSelected()
    {
        $node = new NodeElement('some_xpath', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->exactly(2))
            ->method('isSelected')
            ->with('some_xpath')
            ->will($this->onConsecutiveCalls(true, false));

        $this->assertTrue($node->isSelected());
        $this->assertFalse($node->isSelected());
    }

    public function testFocus()
    {
        $node = new NodeElement('some-element', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('focus')
            ->with('some-element');

        $node->focus();
    }

    public function testBlur()
    {
        $node = new NodeElement('some-element', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('blur')
            ->with('some-element');

        $node->blur();
    }

    public function testMouseOver()
    {
        $node = new NodeElement('some-element', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('mouseOver')
            ->with('some-element');

        $node->mouseOver();
    }

    public function dragTo()
    {
        $node = new NodeElement('some_tag1', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('triggerEvent')
            ->with('some_tag1', 'some_tag3');

        $node->dragTo(new NodeElement('some_tag2', $this->driver, $this->selectorsHandler));
    }

    public function testSubmitForm()
    {
        $node = new NodeElement('some_xpath', $this->driver, $this->selectorsHandler);

        $this->driver
            ->expects($this->once())
            ->method('submitForm')
            ->with('some_xpath');

        $node->submit();
    }
}
