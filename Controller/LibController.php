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

class LibController
{

    /**
     * Lib画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        // add code...

        return $app->render('Lib/Resource/template/index.twig', array(
            // add parameter...
        ));
    }

}
