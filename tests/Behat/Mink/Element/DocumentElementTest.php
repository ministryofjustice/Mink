<?php

namespace Test\Behat\Mink\Element;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Selector\SelectorsHandler;

/**
 * @group unittest
 */
class DocumentElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $elementFactory;

    /**
     * @var SelectorsHandler
     */
    private $selectorsHandler;

    /**
     * Page.
     *
     * @var DocumentElement
     */
    private $document;

    protected function setUp()
    {
        $this->driver = $this->getMock('Behat\Mink\Driver\DriverInterface');
        $this->elementFactory = $this->getMockBuilder('Behat\Mink\Element\ElementFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectorsHandler = new SelectorsHandler();
        $this->document = new DocumentElement($this->driver, $this->selectorsHandler, $this->elementFactory);
    }

    public function testFindAll()
    {
        $this->driver
            ->expects($this->exactly(3))
            ->method('find')
            ->with('//html/h3[a]')
            ->will($this->onConsecutiveCalls(array(2, 3, 4), array(1, 2), array()));
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(5))
            ->method('createNodeElement')
            ->will($this->returnValue($node));

        $this->assertEquals(3, count($this->document->findAll('xpath', $xpath = 'h3[a]')));

        $selector = $this->getMockBuilder('Behat\Mink\Selector\SelectorInterface')->getMock();
        $selector
            ->expects($this->once())
            ->method('translateToXPath')
            ->with($css = 'h3 > a')
            ->will($this->returnValue($xpath));

        $this->selectorsHandler->registerSelector('css', $selector);
        $this->assertEquals(2, count($this->document->findAll('css', $css)));
        $this->assertCount(0, $this->document->findAll('xpath', $xpath));
    }

    public function testFind()
    {
        $this->driver
            ->expects($this->exactly(3))
            ->method('find')
            ->with('//html/h3[a]')
            ->will($this->onConsecutiveCalls(array(2, 3, 4), array(1, 2), array()));
        $node1 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node4 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(5))
            ->method('createNodeElement')
            ->will($this->onConsecutiveCalls($node2, $node3, $node4, $node1, $node2));

        $this->assertSame($node2, $this->document->find('xpath', $xpath = 'h3[a]'));

        $selector = $this->getMockBuilder('Behat\Mink\Selector\SelectorInterface')->getMock();
        $selector
            ->expects($this->once())
            ->method('translateToXPath')
            ->with($css = 'h3 > a')
            ->will($this->returnValue($xpath));

        $this->selectorsHandler->registerSelector('css', $selector);
        $this->assertSame($node1, $this->document->find('css', $css));

        $this->assertNull($this->document->find('xpath', $xpath));
    }

    public function testFindField()
    {
        $xpath = <<<XPATH
//html/.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('field1', 'field2', 'field3'), array()));

        $node1 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(3))
            ->method('createNodeElement')
            ->will($this->onConsecutiveCalls($node1, $node2, $node3));

        $this->assertSame($node1, $this->document->findField('some field'));
        $this->assertNull($this->document->findField('some field'));
    }

    public function testFindLink()
    {
        $xpath = <<<XPATH
//html/.//a[./@href][(((./@id = 'some link' or contains(normalize-space(string(.)), 'some link')) or contains(./@title, 'some link') or contains(./@rel, 'some link')) or .//img[contains(./@alt, 'some link')])] | .//*[./@role = 'link'][((./@id = 'some link' or contains(./@value, 'some link')) or contains(./@title, 'some link') or contains(normalize-space(string(.)), 'some link'))]
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('link1', 'link2', 'link3'), array()));

        $node1 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(3))
            ->method('createNodeElement')
            ->will($this->onConsecutiveCalls($node1, $node2, $node3));

        $this->assertSame($node1, $this->document->findLink('some link'));
        $this->assertNull($this->document->findLink('some link'));
    }

    public function testFindButton()
    {
        $xpath = <<<XPATH
//html/.//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button' or ./@type = 'reset'][(((./@id = 'some button' or ./@name = 'some button') or contains(./@value, 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')] | .//button[((((./@id = 'some button' or ./@name = 'some button') or contains(./@value, 'some button')) or contains(normalize-space(string(.)), 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')] | .//*[./@role = 'button'][(((./@id = 'some button' or ./@name = 'some button') or contains(./@value, 'some button')) or contains(./@title, 'some button') or contains(normalize-space(string(.)), 'some button'))]
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('button1', 'button2', 'button3'), array()));

        $node1 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(3))
            ->method('createNodeElement')
            ->will($this->onConsecutiveCalls($node1, $node2, $node3));

        $this->assertEquals($node1, $this->document->findButton('some button'));
        $this->assertNull($this->document->findButton('some button'));
    }

    public function testFindById()
    {
        $xpath = <<<XPATH
//html/.//*[@id= 'some-item-2' ]
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('id2', 'id3'), array()));
        $node2 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node3 = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(2))
            ->method('createNodeElement')
            ->will($this->onConsecutiveCalls($node2, $node3));

        $this->assertSame($node2, $this->document->findById('some-item-2'));
        $this->assertEquals(null, $this->document->findById('some-item-2'));
    }

    public function testHasSelector()
    {
        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with('//html/some xpath')
            ->will($this->onConsecutiveCalls(array('id2', 'id3'), array()));
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(2))
            ->method('createNodeElement')
            ->will($this->returnValue($node));

        $this->assertTrue($this->document->has('xpath', 'some xpath'));
        $this->assertFalse($this->document->has('xpath', 'some xpath'));
    }

    public function testHasContent()
    {
        $xpath = <<<XPATH
//html/./descendant-or-self::*[contains(normalize-space(.), 'some content')]
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('item1', 'item2'), array()));
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(2))
            ->method('createNodeElement')
            ->will($this->returnValue($node));

        $this->assertTrue($this->document->hasContent('some content'));
        $this->assertFalse($this->document->hasContent('some content'));
    }

    public function testHasLink()
    {
        $xpath = <<<XPATH
//html/.//a[./@href][(((./@id = 'some link' or contains(normalize-space(string(.)), 'some link')) or contains(./@title, 'some link') or contains(./@rel, 'some link')) or .//img[contains(./@alt, 'some link')])] | .//*[./@role = 'link'][((./@id = 'some link' or contains(./@value, 'some link')) or contains(./@title, 'some link') or contains(normalize-space(string(.)), 'some link'))]
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('link1', 'link2', 'link3'), array()));
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(3))
            ->method('createNodeElement')
            ->will($this->returnValue($node));

        $this->assertTrue($this->document->hasLink('some link'));
        $this->assertFalse($this->document->hasLink('some link'));
    }

    public function testHasButton()
    {
        $xpath = <<<XPATH
//html/.//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button' or ./@type = 'reset'][(((./@id = 'some button' or ./@name = 'some button') or contains(./@value, 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')] | .//button[((((./@id = 'some button' or ./@name = 'some button') or contains(./@value, 'some button')) or contains(normalize-space(string(.)), 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')] | .//*[./@role = 'button'][(((./@id = 'some button' or ./@name = 'some button') or contains(./@value, 'some button')) or contains(./@title, 'some button') or contains(normalize-space(string(.)), 'some button'))]
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('button1', 'button2', 'button3'), array()));
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(3))
            ->method('createNodeElement')
            ->will($this->returnValue($node));

        $this->assertTrue($this->document->hasButton('some button'));
        $this->assertFalse($this->document->hasButton('some button'));
    }

    public function testHasField()
    {
        $xpath = <<<XPATH
//html/.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('field1', 'field2', 'field3'), array()));
        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementFactory->expects($this->exactly(3))
            ->method('createNodeElement')
            ->will($this->returnValue($node));

        $this->assertTrue($this->document->hasField('some field'));
        $this->assertFalse($this->document->hasField('some field'));
    }

    public function testHasCheckedField()
    {
        $xpath = <<<XPATH
//html/.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some checkbox' or ./@name = 'some checkbox') or ./@id = //label[contains(normalize-space(string(.)), 'some checkbox')]/@for) or ./@placeholder = 'some checkbox')] | .//label[contains(normalize-space(string(.)), 'some checkbox')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;
        $checkbox = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $checkbox
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->will($this->onConsecutiveCalls(true, false));

        $this->driver
            ->expects($this->exactly(3))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array($xpath), array(), array($xpath)));

        $this->elementFactory->expects($this->exactly(2))
            ->method('createNodeElement')
            ->with($xpath, $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($checkbox));

        $this->assertTrue($this->document->hasCheckedField('some checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some checkbox'));
        $this->assertFalse($this->document->hasCheckedField('some checkbox'));
    }

    public function testHasUncheckedField()
    {
        $xpath = <<<XPATH
//html/.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some checkbox' or ./@name = 'some checkbox') or ./@id = //label[contains(normalize-space(string(.)), 'some checkbox')]/@for) or ./@placeholder = 'some checkbox')] | .//label[contains(normalize-space(string(.)), 'some checkbox')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;
        $checkbox = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $checkbox
            ->expects($this->exactly(2))
            ->method('isChecked')
            ->will($this->onConsecutiveCalls(true, false));

        $this->driver
            ->expects($this->exactly(3))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array($xpath), array(), array($xpath)));

        $this->elementFactory->expects($this->exactly(2))
            ->method('createNodeElement')
            ->with($xpath, $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($checkbox));

        $this->assertFalse($this->document->hasUncheckedField('some checkbox'));
        $this->assertFalse($this->document->hasUncheckedField('some checkbox'));
        $this->assertTrue($this->document->hasUncheckedField('some checkbox'));
    }

    public function testHasSelect()
    {
        $xpath = <<<XPATH
//html/.//select[(((./@id = 'some select field' or ./@name = 'some select field') or ./@id = //label[contains(normalize-space(string(.)), 'some select field')]/@for) or ./@placeholder = 'some select field')] | .//label[contains(normalize-space(string(.)), 'some select field')]//.//select
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('select'), array()));

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFactory->expects($this->once())
            ->method('createNodeElement')
            ->with('select', $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($node));

        $this->assertTrue($this->document->hasSelect('some select field'));
        $this->assertFalse($this->document->hasSelect('some select field'));
    }

    public function testHasTable()
    {
        $xpath = <<<XPATH
//html/.//table[(./@id = 'some table' or contains(.//caption, 'some table'))]
XPATH;

        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->with($xpath)
            ->will($this->onConsecutiveCalls(array('table'), array()));

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elementFactory->expects($this->once())
            ->method('createNodeElement')
            ->with('table', $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($node));

        $this->assertTrue($this->document->hasTable('some table'));
        $this->assertFalse($this->document->hasTable('some table'));
    }

    public function testClickLink()
    {
        $xpath = <<<XPATH
//html/.//a[./@href][(((./@id = 'some link' or contains(normalize-space(string(.)), 'some link')) or contains(./@title, 'some link')) or .//img[contains(./@alt, 'some link')])]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('click');
        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array('link'), array()));
        $this->elementFactory->expects($this->once())
            ->method('createNodeElement')
            ->with('link', $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($node));

        $this->document->clickLink('some link');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->clickLink('some link');
    }

    public function testClickButton()
    {
        $xpath = <<<XPATH
//html/.//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][((./@id = 'some button' or contains(./@value, 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')] | .//button[(((./@id = 'some button' or contains(./@value, 'some button')) or contains(normalize-space(string(.)), 'some button')) or contains(./@title, 'some button'))] | .//input[./@type = 'image'][contains(./@alt, 'some button')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('press');
        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array('button'), array()));
        $this->elementFactory->expects($this->once())
            ->method('createNodeElement')
            ->with('button', $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($node));

        $this->document->pressButton('some button');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->pressButton('some button');
    }

    public function testFillField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('setValue')
            ->with('some val');
        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array('field'), array()));
        $this->elementFactory->expects($this->once())
            ->method('createNodeElement')
            ->with('field', $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($node));

        $this->document->fillField('some field', 'some val');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->fillField('some field', 'some val');
    }

    public function testCheckField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('check');
        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array('field'), array()));
        $this->elementFactory->expects($this->once())
            ->method('createNodeElement')
            ->with('field', $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($node));

        $this->document->checkField('some field');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->checkField('some field');
    }

    public function testUncheckField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('uncheck');
        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array('field'), array()));
        $this->elementFactory->expects($this->once())
            ->method('createNodeElement')
            ->with('field', $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($node));

        $this->document->uncheckField('some field');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->uncheckField('some field');
    }

    public function testSelectField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('selectOption')
            ->with('option2');
        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array('field'), array()));
        $this->elementFactory->expects($this->once())
            ->method('createNodeElement')
            ->with('field', $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($node));

        $this->document->selectFieldOption('some field', 'option2');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->selectFieldOption('some field', 'option2');
    }

    public function testAttachFileToField()
    {
        $xpath = <<<XPATH
.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@id = 'some field' or ./@name = 'some field') or ./@id = //label[contains(normalize-space(string(.)), 'some field')]/@for) or ./@placeholder = 'some field')] | .//label[contains(normalize-space(string(.)), 'some field')]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]
XPATH;

        $node = $this->getMockBuilder('Behat\Mink\Element\NodeElement')
            ->disableOriginalConstructor()
            ->getMock();
        $node
            ->expects($this->once())
            ->method('attachFile')
            ->with('/path/to/file');
        $this->driver
            ->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls(array('field'), array()));
        $this->elementFactory->expects($this->once())
            ->method('createNodeElement')
            ->with('field', $this->driver, $this->selectorsHandler)
            ->will($this->returnValue($node));

        $this->document->attachFileToField('some field', '/path/to/file');
        $this->setExpectedException('Behat\Mink\Exception\ElementNotFoundException');
        $this->document->attachFileToField('some field', '/path/to/file');
    }

    public function testGetContent()
    {
        $this->driver
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($ret = 'page content'));

        $this->assertEquals($ret, $this->document->getContent());
    }

    public function testGetText()
    {
        $this->driver
            ->expects($this->once())
            ->method('getText')
            ->with('//html')
            ->will($this->returnValue('val1'));

        $this->assertEquals('val1', $this->document->getText());
    }
}
