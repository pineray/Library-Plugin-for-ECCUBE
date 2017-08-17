<?php

/*
 * This file is part of the Lib
 *
 * Copyright (C) 2017 pineray
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Lib\Service;

class StateService
{
    /** @var \Eccube\Application */
    public $app;

    /** @var \Plugin\Lib\Repository\KeyValueRepository */
    protected $keyValueStore;

    /** @var array */
    protected $cache;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;

        $this->keyValueStore = $app['orm.em']->getRepository('Plugin\Lib\Entity\KeyValue');
        $this->keyValueStore->setCollection('state');
        $this->keyValueStore->setApplication($app);

        if (empty($this->cache)) {
            $this->cache = $this->keyValueStore->getAll();
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function get($key, $default = NULL) {
        return (isset($this->cache[$key])) ? $this->cache[$key] : $default;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function getMultiple(array $keys) {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->get($key);
        }
        return $values;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->cache[$key] = $value;
        $this->keyValueStore->set($key, $value);
    }

    /**
     * @param array $data
     */
    public function setMultiple(array $data) {
        foreach ($data as $key => $value) {
            $this->cache[$key] = $value;
        }
        $this->keyValueStore->setMultiple($data);
    }

    /**
     * @param string $key
     */
    public function delete($key) {
        $this->deleteMultiple([$key]);
    }

    /**
     * @param array $keys
     */
    public function deleteMultiple(array $keys) {
        foreach ($keys as $key) {
            unset($this->cache[$key]);
        }
        $this->keyValueStore->deleteMultiple($keys);
    }

    public function resetCache() {
        $this->cache = $this->keyValueStore->getAll();
    }
}