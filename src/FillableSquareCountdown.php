<?php

namespace EmailCountdown;

class FillableSquareCountdown extends AbstractCountdown
{
    /**
     * @var int
     */
    private $squareWidth = 80;

    /**
     * @var int
     */
    private $squareHeight = 60;

    /**
     * @var int
     */
    private $circleRadius = 60;

    /**
     * @var float
     */
    private $squareScale = 3.0;

    /**
     * @var array
     */
    private $squareBackgroundColorAll = null;

    /**
     * @var array
     */
    private $squareForegroundColorAll = null;

    /**
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return resource
     */
    protected function getFormImage($days, $hours, $minutes, $seconds)
    {
        $squareImageWidth = $this->width * $this->squareScale;
        $squareImageHeight = $this->height * $this->squareScale;
        if (empty($this->formImage)) {

            // create the square image once
            $this->formImage = imagecreatetruecolor($squareImageWidth, $squareImageHeight);

            // background
            $backgroundColor = imagecolorallocate(
                $this->formImage,
                $this->backgroundColor['red'],
                $this->backgroundColor['green'],
                $this->backgroundColor['blue']
            );
            imagefilledrectangle(
                $this->formImage,
                0, 0,
                $squareImageWidth, $squareImageHeight,
                $backgroundColor
            );
            imagesetthickness($this->formImage, $this->squareScale * 2);

            $this->squareBackgroundColorAll = imagecolorallocate(
                $this->formImage,
                $this->formBackgroundColor['red'],
                $this->formBackgroundColor['green'],
                $this->formBackgroundColor['blue']
            );
            $this->squareForegroundColorAll = imagecolorallocate(
                $this->formImage,
                $this->formForegroundColor['red'],
                $this->formForegroundColor['green'],
                $this->formForegroundColor['blue']
            );
        }

        $zoomWidth = $this->squareWidth * $this->squareScale;
        $zoomHeight = $this->squareHeight * $this->squareScale;
        $circleRadius = $this->circleRadius * $this->squareScale;

        $y1 = ($squareImageHeight - $zoomHeight) / 2;
        $y2 = $y1 + $zoomHeight;
        $margin = 5 * $this->squareScale;

        imagefilledellipse(
            $this->formImage,
            $zoomWidth / 2 - $margin, $squareImageHeight / 2,
            $circleRadius, $circleRadius,
            $this->squareForegroundColorAll
        );
        imagefilledrectangle(
            $this->formImage,
            $zoomWidth / 2 - $margin, $y1,
            $squareImageWidth - $zoomWidth / 2 + $margin, $y2,
            $this->squareForegroundColorAll
        );
        imagefilledellipse(
            $this->formImage,
            $squareImageWidth - $zoomWidth / 2 + $margin, $squareImageHeight / 2,
            $circleRadius, $circleRadius,
            $this->squareForegroundColorAll
        );

        return $this->formImage;
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
        $squareImage = $this->getFormImage($days, $hours, $minutes, $seconds);
        imagecopyresampled(
            $frame, $squareImage,
            0, 0,
            0, 0,
            $this->width, $this->height,
            $this->width * $this->squareScale, $this->height * $this->squareScale
        );

        return $frame;
    }
}
