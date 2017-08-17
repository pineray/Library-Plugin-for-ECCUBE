<?php

/*
 * This file is part of the Lib
 *
 * Copyright (C) 2017 pineray
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Lib\ServiceProvider;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Plugin\Lib\Form\Type\LibConfigType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class LibServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // プラグイン用設定画面
        $app->match('/'.$app['config']['admin_route'].'/plugin/Lib/config', 'Plugin\Lib\Controller\ConfigController::index')->bind('plugin_Lib_config');

        // 独自コントローラ
        $app->match('/plugin/lib/hello', 'Plugin\Lib\Controller\LibController::index')->bind('plugin_Lib_hello');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new LibConfigType();

            return $types;
        }));

        // Repository
        $app['plugin.lib.repository.KeyValue'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\Lib\Entity\KeyValue');
        });

        // Service
        $app['plugin.lib.service.state'] = $app->share(function () use ($app) {
            return new \Plugin\Lib\Service\StateService($app);
        });

        // ログファイル設定
        $app['monolog.logger.lib'] = $app->share(function ($app) {

            $logger = new $app['monolog.logger.class']('lib');

            $filename = $app['config']['root_dir'].'/app/log/lib.log';
            $RotateHandler = new RotatingFileHandler($filename, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                'lib_{date}',
                'Y-m-d'
            );

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::ERROR),
                    0,
                    true,
                    true,
                    Logger::INFO
                )
            );

            return $logger;
        });

    }

    public function boot(BaseApplication $app)
    {
    }

}
