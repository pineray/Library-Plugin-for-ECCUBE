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
    public static function addItem(&$app, $new)
    {
        $config = $app['config'];
        $nav = $config['nav'];

        foreach ($nav as $key => &$item) {
            if ($new['parent'] == $item['id']) {
                $item['child'][] = $new;
            }
        }

        $config['nav'] = $nav;
        $app['config'] = $config;
    }
}