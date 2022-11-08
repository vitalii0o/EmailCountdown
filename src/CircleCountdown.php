<?php

namespace EmailCountdown;

class CircleCountdown extends AbstractCountdown
{
    /**
     * @var int
     */
    private $circleDiameter = 100;

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
     * @var int
     */
    private $lineThickness = 2;

    /**
     * @param int $lineThickness
     * @return $this
     */
    public function setLineThickness($lineThickness)
    {
        $this->lineThickness = $lineThickness;

        return $this;
    }

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
            $formImageWidth = $this->width * $this->scale;
            $formImageHeight = $this->height * $this->scale;

            // create the circle image once
            $this->circleImage = imagecreatetruecolor($formImageWidth, $formImageHeight);

            // background
            if ($this->backgroundType === self::BACKGROUND_TYPE_TRANSPARENT) {
                $backgroundColor = imagecolorallocatealpha(
                    $this->circleImage,
                    $this->backgroundColor['red'],
                    $this->backgroundColor['green'],
                    $this->backgroundColor['blue'],
                    127
                );
                imagealphablending($this->circleImage, false);
                imagesavealpha($this->circleImage, true);
            } else {
                $backgroundColor = imagecolorallocate(
                    $this->circleImage,
                    $this->backgroundColor['red'],
                    $this->backgroundColor['green'],
                    $this->backgroundColor['blue']
                );
            }

            imagefilledrectangle(
                $this->circleImage,
                0, 0,
                $formImageWidth, $formImageHeight,
                $backgroundColor
            );

            imagesetthickness($this->circleImage, $this->scale * $this->lineThickness);

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

        $circleDiameter = $this->circleDiameter * $this->scale;
        $circlePadding = 20 * $this->scale;
        $distanceBetweenItems = $this->circleDiameter * $this->scale;

        imagearc(
            $this->circleImage,
            ($circleDiameter / 2) + (3 * $distanceBetweenItems), $circleDiameter / 2,
            $circleDiameter - $circlePadding,$circleDiameter - $circlePadding,
            0, 359.99,
            $this->circleBackgroundColorAll
        );
        imagearc(
            $this->circleImage,
            ($circleDiameter / 2) + (3 * $distanceBetweenItems), $circleDiameter / 2,
            $circleDiameter - $circlePadding,$circleDiameter - $circlePadding,
            -90, -90 - (6 * $seconds),
            $this->circleForegroundColorAll
        );

        if (empty($this->lastMinutes) || $minutes != $this->lastMinutes) {
            imagearc(
                $this->circleImage,
                ($circleDiameter / 2) + (2 * $distanceBetweenItems), $circleDiameter / 2,
                $circleDiameter - $circlePadding, $circleDiameter - $circlePadding,
                0, 359.99,
                $this->circleBackgroundColorAll
            );
            imagearc(
                $this->circleImage,
                ($circleDiameter / 2) + (2 * $distanceBetweenItems), $circleDiameter / 2,
                $circleDiameter - $circlePadding, $circleDiameter - $circlePadding,
                -90, -90 - (6 * $minutes),
                $this->circleForegroundColorAll
            );
            $this->lastMinutes = $minutes;
        }

        if (empty($this->lastHours) || $hours != $this->lastHours) {
            imagearc(
                $this->circleImage,
                $circleDiameter / 2 + ($distanceBetweenItems), $circleDiameter / 2,
                $circleDiameter - $circlePadding, $circleDiameter - $circlePadding,
                0, 359.99,
                $this->circleBackgroundColorAll
            );
            imagearc(
                $this->circleImage,
                $circleDiameter / 2 + ($distanceBetweenItems), $circleDiameter / 2,
                $circleDiameter - $circlePadding,$circleDiameter - $circlePadding,
                -90, -90 - (15 * $hours),
                $this->circleForegroundColorAll
            );
            $this->lastHours = $hours;
        }

        if (empty($this->lastDays) || $days != $this->lastDays) {
            imagearc(
                $this->circleImage,
                $circleDiameter / 2, $circleDiameter / 2,
                $circleDiameter - $circlePadding,$circleDiameter - $circlePadding,
                0, 359.99,
                $this->circleBackgroundColorAll
            );
            imagearc(
                $this->circleImage,
                $circleDiameter / 2, $circleDiameter / 2,
                $circleDiameter - $circlePadding,$circleDiameter - $circlePadding,
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
            $this->width * $this->scale, $this->height * $this->scale,
            $this->width * $this->scale, $this->height * $this->scale
        );

        return $frame;
    }
}
