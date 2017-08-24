<?php

/*
 * This file is part of the Lib
 *
 * Copyright (C) 2017 pineray
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Lib\Queue;

/**
 * Defines an interface for a QueueWorker.
 *
 * @see \Plugin\Lib\Service\CronService
 * @see \Plugin\Lib\Repository\QueueRepository
 */
interface QueueWorkerInterface
{

    /**
     * Works on a single queue item.
     *
     * @param mixed $data
     *   The data that was passed when the item was queued.
     *
     * @throws \Plugin\Lib\Queue\RequeueException
     *   Processing is not yet finished. This will allow another process to claim
     *   the item immediately.
     * @throws \Exception
     *   A QueueWorker may throw an exception to indicate there was a problem.
     *   The cron process will log the exception, and leave the item in the queue
     *   to be processed again later.
     * @throws \Plugin\Lib\Queue\SuspendQueueException
     *   More specifically, a SuspendQueueException should be thrown when a
     *   QueueWorker is aware that the problem will affect all subsequent
     *   workers of its queue. For example, a callback that makes HTTP requests
     *   may find that the remote server is not responding. The cron process will
     *   behave as with a normal Exception, and in addition will not attempt to
     *   process further items from the current item's queue during the current
     *   cron run.
     *
     * @see \Plugin\Lib\Service\CronService::processQueues()
     */
    public function processItem($data);

}