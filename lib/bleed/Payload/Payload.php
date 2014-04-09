<?php

namespace bleed\Payload;

/**
 * This class takes serves as a base class for Payloads.
 *
 * @copyright Copyright (C) 2014 Dominik Tobschall. All rights reserved.
 * @author Dominik Tobschall (http://tobschall.de/)
 */
abstract class Payload
{
    protected $payload;

    public function __construct()
    {
        $this->payload = $this->toBin($this->payload);
    }

    public function getPayload()
    {
        return $this->payload;
    }

    private function toBin($hex) {
        $hex = str_replace([' ', PHP_EOL], false, $hex);
        return pack("H*" , $hex);
    }

}
