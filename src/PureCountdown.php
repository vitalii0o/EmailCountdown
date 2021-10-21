<?php

namespace EmailCountdown;

use GifCreator\GifCreator;

class PureCountdown extends AbstractCountdown
{
    /**
     * @param resource $frame
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return resource
     */
    protected function modifyFrame($frame, $days, $hours, $minutes, $seconds)
    {
        return $frame;
    }

    /**
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     */
    protected function getFormImage($days, $hours, $minutes, $seconds)
    {
        return;
    }
}
