<?php

use Refactor\MessagesByOrderStatusGenerator;

class Notification
{
    /**
     * @param Order $order
     *
     * @return array
     */
    public static function getMessagesByOrderStatus(Order $order)
    {
        $generator = new MessagesByOrderStatusGenerator(Logger::getInstance());
        return $generator->generate($order);
    }
}