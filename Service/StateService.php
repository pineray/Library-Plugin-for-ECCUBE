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
    static $cache;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;

        $this->keyValueStore = $app['orm.em']->getRepository('Plugin\Lib\Entity\KeyValue');
        $this->keyValueStore->setCollection('state');
        $this->keyValueStore->setApplication($app);

        if (empty(static::$cache)) {
            static::$cache = $this->keyValueStore->getAll();
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function get($key, $default = NULL) {
        return (isset(static::$cache[$key])) ? static::$cache[$key] : $default;
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
        static::$cache[$key] = $value;
        $this->keyValueStore->set($key, $value);
    }

    /**
     * @param array $data
     */
    public function setMultiple(array $data) {
        foreach ($data as $key => $value) {
            static::$cache[$key] = $value;
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
            unset(static::$cache[$key]);
        }
        $this->keyValueStore->deleteMultiple($keys);
    }

    public function resetCache() {
        static::$cache = $this->keyValueStore->getAll();
    }
}