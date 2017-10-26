<?php

/*
 * This file is part of the Lib
 *
 * Copyright (C) 2017 pineray
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Lib\Util;

use Eccube\Application;

class Nav
{
    /** @var \Eccube\Application */
    public $app;

    /** @var $app['config'] */
    private $config;

    /** @var array - Target item. */
    private $current;

    /** @var array - Parent of target item. */
    private $parent;

    /** @var array Traits from root to target item. */
    private $traits;

    /**
     * @param Application $app
     * @param string $search
     * @return Nav
     */
    public static function forge(\Eccube\Application $app, $search = '')
    {
        return new Nav($app, $search);
    }

    /**
     * Nav constructor.
     * @param Application $app
     * @param string $search
     */
    public function __construct(\Eccube\Application $app, $search = '')
    {
        $this->app = $app;

        $this->config = $this->app['config'];
        $this->current = null;
        $this->parent = null;
        $this->traits = [];
        if ($search !== '') {
            $this->find($search);
        }
    }

    /**
     * Find the target item.
     * @param string $search
     * @return Nav $this
     */
    public function find($search)
    {
        $searchRoutine = function (&$item, $search, $traits = [], $Nav) use(&$searchRoutine) {
            $traits[] = $item['id'];
            if ($search == $item['id']) {
                $Nav->current = &$item;
                return $traits;
            }
            elseif (isset($item['child'])) {
                foreach ($item['child'] as &$child) {
                    $return = $searchRoutine($child, $search, $traits, $Nav);
                    if ($return !== false) {
                        $length = count($return);
                        if ($return[$length - 2] == $item['id']) {
                            $Nav->parent = &$item;
                        }

                        return $return;
                    }
                }
            }
            return false;
        };

        $nav = &$this->config['nav'];

        $traits = [];
        foreach ($nav as $key => &$item) {
            $return = $searchRoutine($item, $search, $traits, $this);
            if ($return !== false) {
                $this->traits = $return;
                break;
            }
        }

        return $this;
    }

    /**
     * Insert new item to the end of elements in the target item.
     * @param array $item
     * @return Nav $this
     */
    public function append($item)
    {
        if ($this->current === null) {
            $nav = &$this->config['nav'];

            $nav[] = $item;
        }
        elseif (count($this->traits) < 3) {
            if (isset($this->current['child'])) {
                $this->current['child'][] = $item;
            }
            else {
                $this->current['child'] = [$item];
                $this->current['has_child'] = true;
            }
        }

        $this->app['config'] = $this->config;

        return $this;
    }

    /**
     * Insert new item to the beginning of elements in the target item.
     * @param array $item
     * @return Nav $this
     */
    public function prepend($item)
    {
        if ($this->current === null) {
            $nav = &$this->config['nav'];

            array_unshift($nav, $item);
        }
        elseif (count($this->traits) < 3) {
            if (isset($this->current['child'])) {
                array_unshift($this->current['child'], $item);
            }
            else {
                $this->current['child'] = [$item];
                $this->current['has_child'] = true;
            }
        }

        $this->app['config'] = $this->config;

        return $this;
    }

    /**
     * Insert new item before the target item.
     * @param array $item
     * @return Nav $this
     */
    public function before($item)
    {
        if ($this->current === null) {
            $nav = &$this->config['nav'];

            array_unshift($nav, $item);
        }
        elseif (count($this->traits) === 1) {
            $nav = &$this->config['nav'];
            $target_index = null;
            foreach ($nav as $key => $siblings) {
                if ($siblings['id'] === $this->current['id']) {
                    $target_index = $key;
                    break;
                }
            }
            if ($target_index !== null) {
                array_splice($nav, $target_index, 0, [$item]);
            }
            else {
                array_unshift($nav, $item);
            }
        }
        else {
            $target_index = null;
            foreach ($this->parent['child'] as $key => $siblings) {
                if ($siblings['id'] === $this->current['id']) {
                    $target_index = $key;
                    break;
                }
            }
            if ($target_index !== null) {
                array_splice($this->parent['child'], $target_index, 0, [$item]);
            }
            else {
                array_unshift($this->parent['child'], $item);
            }
        }

        $this->app['config'] = $this->config;

        return $this;
    }

    /**
     * Insert new item after the target item.
     * @param array $item
     * @return Nav $this
     */
    public function after($item)
    {
        if ($this->current === null) {
            $nav = &$this->config['nav'];

            array_unshift($nav, $item);
        }
        elseif (count($this->traits) === 1) {
            $nav = &$this->config['nav'];
            $target_index = null;
            foreach ($nav as $key => $siblings) {
                if ($siblings['id'] === $this->current['id']) {
                    $target_index = $key;
                    break;
                }
            }
            if ($target_index !== null) {
                array_splice($nav, $target_index + 1, 0, [$item]);
            }
            else {
                $nav[] = $item;
            }
        }
        else {
            $target_index = null;
            foreach ($this->parent['child'] as $key => $siblings) {
                if ($siblings['id'] === $this->current['id']) {
                    $target_index = $key;
                    break;
                }
            }
            if ($target_index !== null) {
                array_splice($this->parent['child'], $target_index + 1, 0, [$item]);
            }
            else {
                $this->parent['child'][] = $item;
            }
        }

        $this->app['config'] = $this->config;

        return $this;
    }

}