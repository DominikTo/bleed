<?php

namespace bleed\Payload;

/**
 * This class takes care of the Heartbeat payload.
 *
 * @copyright Copyright (C) 2014 Dominik Tobschall. All rights reserved.
 * @author Dominik Tobschall (http://tobschall.de/)
 */
class Heartbeat extends Payload
{
    /**
     * By Jared Stafford (http://www.jspenguin.org)
     */
    protected $payload = <<<PAYLOAD
18 03 02 00 03
01 40 00
PAYLOAD;

}
