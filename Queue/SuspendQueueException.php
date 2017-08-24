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
 * Exception class to throw to indicate that a cron queue should be skipped.
 */
class SuspendQueueException extends \Exception
{
}
