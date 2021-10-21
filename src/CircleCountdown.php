<?php

namespace EmailCountdown;

class CircleCountdown extends AbstractCountdown
{
    /**
     * @var int
     */
    private $circleWidth = 100;

    /**
     * @var int
     */
    private $circleHeight = 100;

    /**
     * @var float
     */
    private $circleScale = 3.0;

    /**
     * Cache the circle image so don't have to draw it again
     * @var resource
     */
    private $circleImage = null;

    /**
     * @var int
     */
    private $lastDays = null;

    /**
     * @var int
     */
    private $lastHours = null;

    /**
     * @var int
     */
    private $lastMinutes = null;

    /**
     * @var array
     */
    private $circleBackgroundColorAll = null;

    /**
     * @var array
     */
    private $circleForegroundColorAll = null;

    /**
     * Get the circle image for the fake countdown
     *
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return resource
     */
    protected function getFormImage($days, $hours, $minutes, $seconds)
    {
        if (empty($this->circleImage)) {
            $circleImageWidth = $this->width * $this->circleScale;
            $circleImageHeight = $this->height * $this->circleScale;

            // create the circle image once
            $this->circleImage = imagecreatetruecolor($circleImageWidth, $circleImageHeight);

            // background
            $backgroundColor = imagecolorallocate(
                $this->circleImage,
                $this->backgroundColor['red'],
                $this->backgroundColor['green'],
                $this->backgroundColor['blue']
            );
            imagefilledrectangle(
                $this->circleImage,
                0, 0,
                $circleImageWidth, $circleImageHeight,
                $backgroundColor
            );

            imagesetthickness($this->circleImage, $this->circleScale * 2);

            $this->circleBackgroundColorAll = imagecolorallocate(
                $this->circleImage,
                $this->formBackgroundColor['red'],
                $this->formBackgroundColor['green'],
                $this->formBackgroundColor['blue']
            );
            $this->circleForegroundColorAll = imagecolorallocate(
                $this->circleImage,
                $this->formForegroundColor['red'],
                $this->formForegroundColor['green'],
                $this->formForegroundColor['blue']
            );
        }

        $zoomWidth = $this->circleWidth * $this->circleScale;
        $zoomHeight = $this->circleHeight * $this->circleScale;

        imagearc(
            $this->circleImage,
            ($zoomWidth / 2) + 900, $zoomHeight / 2,
            $zoomWidth - 20 * $this->circleScale,$zoomHeight - 20 * $this->circleScale,
            0, 359.99,
            $this->circleBackgroundColorAll
        );
        imagearc(
            $this->circleImage,
            ($zoomWidth / 2) + 900, $zoomHeight / 2,
            $zoomWidth - 20 * $this->circleScale,$zoomHeight - 20 * $this->circleScale,
            -90, -90 - (6 * $seconds),
            $this->circleForegroundColorAll
        );

        if (empty($this->lastMinutes) || $minutes != $this->lastMinutes) {
            imagearc(
                $this->circleImage,
                ($zoomWidth / 2) + 600, $zoomHeight / 2,
                $zoomWidth - 20 * $this->circleScale, $zoomHeight - 20 * $this->circleScale,
                0, 359.99,
                $this->circleBackgroundColorAll
            );
            imagearc(
                $this->circleImage,
                ($zoomWidth / 2) + 600, $zoomHeight / 2,
                $zoomWidth - 20 * $this->circleScale, $zoomHeight - 20 * $this->circleScale,
                -90, -90 - (6 * $minutes),
                $this->circleForegroundColorAll
            );
            $this->lastMinutes = $minutes;
        }

        if (empty($this->lastHours) || $hours != $this->lastHours) {
            imagearc(
                $this->circleImage,
                $zoomWidth / 2 + 300, $zoomHeight / 2,
                $zoomWidth - 20 * $this->circleScale, $zoomHeight - 20 * $this->circleScale,
                0, 359.99,
                $this->circleBackgroundColorAll
            );
            imagearc(
                $this->circleImage,
                $zoomWidth / 2 + 300, $zoomHeight / 2,
                $zoomWidth - 20 * $this->circleScale,$zoomHeight - 20 * $this->circleScale,
                -90, -90 - (15 * $hours),
                $this->circleForegroundColorAll
            );
            $this->lastHours = $hours;
        }

        if (empty($this->lastDays) || $days != $this->lastDays) {
            imagearc(
                $this->circleImage,
                $zoomWidth / 2, $zoomHeight / 2,
                $zoomWidth - 20 * $this->circleScale,$zoomHeight - 20 * $this->circleScale,
                0, 359.99,
                $this->circleBackgroundColorAll
            );
            imagearc(
                $this->circleImage,
                $zoomWidth / 2, $zoomHeight / 2,
                $zoomWidth - 20 * $this->circleScale,$zoomHeight - 20 * $this->circleScale,
                -90, -90 - (1 * $days),
                $this->circleForegroundColorAll
            );
            $this->lastDays = $days;
        }

        return $this->circleImage;
    }

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
        $circleImage = $this->getFormImage($days, $hours, $minutes, $seconds);
        imagecopyresampled(
            $frame, $circleImage,
            0, 0,
            0, 0,
            $this->width, $this->height,
            $this->width * $this->circleScale, $this->height * $this->circleScale
        );

        return $frame;
    }
}
