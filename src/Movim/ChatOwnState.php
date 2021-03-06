<?php

namespace Movim;

use Movim\Widget\Wrapper;
use React\EventLoop\Timer\Timer;

use Moxl\Xec\Action\Message\Composing;
use Moxl\Xec\Action\Message\Paused;

/**
 * This class handle all the outgoing chatstates
 */
class ChatOwnState
{
    protected static $instance;
    private $_to = null;
    private $_muc = false;
    private $_timer;
    private $_timeout = 5;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function composing(string $to, bool $muc = false)
    {
        global $loop;

        if ($this->_to !== $to) {
            $mc = new Composing;

            if ($muc) {
                $mc->setMuc();
            }

            $mc->setTo($to)->request();

            if ($this->_to !== null) {
                $this->paused($this->_to, $this->_muc);
            }

            $this->_to = $to;
            $this->_muc = $muc;
        }

        if ($this->_timer) {
            $loop->cancelTimer($this->_timer);
        }

        $this->_timer = $loop->addTimer($this->_timeout, function () use ($to, $muc) {
            $this->paused($to, $muc);
        });
    }

    private function paused(string $to, bool $muc = false)
    {
        global $loop;

        $mp = new Paused;

        $this->_to = null;
        $this->_muc = false;

        $loop->cancelTimer($this->_timer);

        if ($muc) {
            $mp->setMuc();
        }

        $mp->setTo($to)->request();
    }
}