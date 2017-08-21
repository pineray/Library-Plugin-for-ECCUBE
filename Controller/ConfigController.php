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

class ConfigController
{

    /**
     * Lib用設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        $form = $app['form.factory']->createBuilder('lib_config')->getForm();

        $form->get('cron_interval')->setData($app['plugin.lib.service.state']->get('plugin.lib.cron_interval', 0));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $app['plugin.lib.service.state']->set('plugin.lib.cron_interval', $data['cron_interval']);

            $app->addSuccess('plugin.lib.config.done', 'admin');
        }

        return $app->render('Lib/Resource/template/admin/config.twig', array(
            'form' => $form->createView(),
            'cron_key' => $app['plugin.lib.service.state']->get('plugin.lib.cron_key'),
            'cron_last' => $app['plugin.lib.service.state']->get('plugin.lib.cron_last', null),
        ));
    }

}
