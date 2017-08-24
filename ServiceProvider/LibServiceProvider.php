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
        // cron手動実行
        $app->match('/'.$app['config']['admin_route'].'/plugin/Lib/cron-run', 'Plugin\Lib\Controller\CronController::runManually')->bind('plugin_Lib_cron_manually');

        // 独自コントローラ
        $app->match('/lib/cron/{cron_key}', 'Plugin\Lib\Controller\CronController::run')->bind('plugin_Lib_cron')->assert('cron_key', '[A-Za-z0-9_-]+');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new LibConfigType();

            return $types;
        }));

        // Repository
        $app['plugin.lib.repository.KeyValue'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\Lib\Entity\KeyValue');
        });
        $app['plugin.lib.repository.Queue'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\Lib\Entity\Queue');
        });

        // Service
        $app['plugin.lib.service.state'] = $app->share(function () use ($app) {
            return new \Plugin\Lib\Service\StateService($app);
        });
        $app['plugin.lib.service.cron'] = $app->share(function () use ($app) {
            return new \Plugin\Lib\Service\CronService($app);
        });

        // メッセージ登録
        $message_file = __DIR__.'/../Resource/locale/message.'.$app['locale'].'.yml';
        $app['translator']->addResource('yaml', $message_file, $app['locale']);

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
