<?php

/*
 * This file is part of the Lib
 *
 * Copyright (C) 2017 pineray
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Lib;

use Eccube\Application;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class LibEvent
{
    /** @var \Eccube\Application $app */
    private $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    public function onFrontResponse(FilterResponseEvent $event)
    {
        $cron_interval = $this->app['plugin.lib.service.state']->get('plugin.lib.cron_interval', 0);
        if ($cron_interval > 0) {
            $cron_last = $this->app['plugin.lib.service.state']->get('plugin.lib.cron_last', null);

            if ($cron_last === null) {
                $this->app['plugin.lib.service.cron']->run();
            }
            else {
                $last_timestamp = $cron_last->getTimestamp();
                $now = new \DateTime();
                $now_timestamp = $now->getTimestamp();

                if ($now_timestamp >= $last_timestamp + $cron_interval) {
                    $this->app['plugin.lib.service.cron']->run();
                }
            }
        }
    }
}