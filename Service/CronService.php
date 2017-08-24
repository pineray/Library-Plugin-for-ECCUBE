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

use Eccube\Application;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Plugin\Lib\Queue\RequeueException;
use Plugin\Lib\Queue\SuspendQueueException;

class CronService
{
    /** @var \Eccube\Application */
    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Run cron.
     *
     * @return bool
     */
    public function run()
    {
        @ignore_user_abort(TRUE);

        if (function_exists('set_time_limit')) {
            $current = ini_get('max_execution_time');

            // Do not set time limit if it is currently unlimited.
            if ($current != 0) {
                @set_time_limit(240);
            }
        }

        $return = FALSE;

        if (!$this->lock()) {
            // Cron is still running normally.
            log_warning('Attempting to re-run cron while it is already running.');
        }
        else {
            $event = new EventArgs();
            $this->app['eccube.event.dispatcher']->dispatch('plugin.lib.cron.run', $event);

            // Record cron time.
            $this->app['plugin.lib.service.state']->set('plugin.lib.cron_last', new \DateTime());
            log_info('Cron run completed.');

            // Release cron lock.
            $this->release();

            // Return TRUE so other functions can check if it did run successfully
            $return = TRUE;
        }

        $this->processQueues();

        return $return;
    }

    /**
     * Try to acquire cron lock.
     *
     * @return bool
     */
    public function lock()
    {
        $now = microtime(TRUE);
        $lock = $this->app['plugin.lib.service.state']->get('plugin.lib.cron_lock', null);

        if ($lock !== null && $lock > $now) {
            return false;
        }
        else {
            $this->app['plugin.lib.service.state']->set('plugin.lib.cron_lock', $now + 900.0);
            return true;
        }
    }

    /**
     * Release cron lock.
     */
    public function release()
    {
        $this->app['plugin.lib.service.state']->delete('plugin.lib.cron_lock');
    }

    /**
     * Processes cron queues.
     */
    protected function processQueues()
    {
        $types = $this->app['plugin.lib.repository.Queue']->getTypes();
        foreach ($types as $type) {
            $end = time() + (!empty($type['max_time']) ? $type['max_time'] : 15);
            while (time() < $end && ($item = $this->app['plugin.lib.repository.Queue']->claimItem($type['name']))) {
                try {
                    $this->app[$item->getName()]->processItem($item->getData());
                    $this->app['plugin.lib.repository.Queue']->deleteItem($item);
                }
                catch (RequeueException $e) {
                    // The worker requested the task be immediately requeued.
                    $this->app['plugin.lib.repository.Queue']->releaseItem($item);
                }
                catch (SuspendQueueException $e) {
                    // If the worker indicates there is a problem with the whole queue,
                    // release the item and skip to the next queue.
                    $this->app['plugin.lib.repository.Queue']->releaseItem($item);

                    log_error('The cron queue is suspended.', array($e->getMessage()));

                    // Skip to the next queue.
                    continue;
                }
                catch (\Exception $e) {
                    // In case of any other kind of exception, log it and leave the item
                    // in the queue to be processed again later.
                    log_error('There was a problem when the cron queue is processing.', array($e->getMessage()));
                }
            }
        }
    }
}