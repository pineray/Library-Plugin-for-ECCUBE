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
 * Throw this exception to release the item allowing it to be processed again.
 */
class RequeueException extends \Exception
{
}
