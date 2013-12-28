<?php

namespace Behat\Mink\Element;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Selector\SelectorsHandler;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base element.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class Element implements ElementInterface
{
    /**
     * Driver.
     *
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var SelectorsHandler
     */
    private $selectorsHandler;

    /**
     * Initialize element.
     *
     * @param DriverInterface  $driver
     * @param SelectorsHandler $selectorsHandler
     */
    public function __construct(DriverInterface $driver, SelectorsHandler $selectorsHandler)
    {
        $this->driver = $driver;
        $this->selectorsHandler = $selectorsHandler;
    }

    /**
     * Returns element's driver.
     *
     * @return DriverInterface
     */
    protected function getDriver()
    {
        return $this->driver;
    }

    /**
     * Checks whether element with specified selector exists.
     *
     * @param string $selector selector engine name
     * @param string $locator  selector locator
     *
     * @return Boolean
     */
    public function has($selector, $locator)
    {
        return null !== $this->find($selector, $locator);
    }

    /**
     * Finds first element with specified selector.
     *
     * @param string $selector selector engine name
     * @param string $locator  selector locator
     *
     * @return NodeElement|null
     */
    public function find($selector, $locator)
    {
        $items = $this->findAll($selector, $locator);

        return count($items) ? current($items) : null;
    }

    /**
     * Finds all elements with specified selector.
     *
     * @param string $selector selector engine name
     * @param string $locator  selector locator
     *
     * @return NodeElement[]
     */
    public function findAll($selector, $locator)
    {
        $xpath = $this->selectorsHandler->selectorToXpath($selector, $locator);

        // add parent xpath before element selector
        if (0 === strpos($xpath, '/')) {
            $xpath = $this->getXpath().$xpath;
        } else {
            $xpath = $this->getXpath().'/'.$xpath;
        }

        return $this->getDriver()->find($xpath);
    }

    /**
     * Returns element text (inside tag).
     *
     * @return string|null
     */
    public function getText()
    {
        return $this->getDriver()->getText($this->getXpath());
    }

    /**
     * Returns element html.
     *
     * @return string|null
     */
    public function getHtml()
    {
        return $this->getDriver()->getHtml($this->getXpath());
    }
}
