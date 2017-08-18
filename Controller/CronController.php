<?php

/*
 * This file is part of the Lib
 *
 * Copyright (C) 2017 pineray
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Lib\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CronController
{

    /**
     * Run Cron once.
     *
     * @param Application $app
     * @param Request $request
     * @param string $cron_key
     * @return Response
     */
    public function run(Application $app, Request $request, $cron_key)
    {
        if ($cron_key != $app['plugin.lib.service.state']->get('plugin.lib.cron_key')) {
            log_info('Cron could not run because an invalid key was used.');
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $app['plugin.lib.service.cron']->run();

        return new Response('', Response::HTTP_OK);
    }

    /**
     * Run cron manually.
     *
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function runManually(Application $app, Request $request)
    {
        if ($app['plugin.lib.service.cron']->run()) {
            $app->addSuccess('plugin.lib.cron.success', 'admin');
        }
        else {
            $app->addError('plugin.lib.cron.failed', 'admin');
        }

        return $app->redirect($app->url('plugin_Lib_config'));
    }
}
