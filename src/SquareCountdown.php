<?php

namespace EmailCountdown;

class SquareCountdown extends AbstractCountdown
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
     * @var float
     */
    private $squareScale = 3.0;

    /**
     * @var array
     */
    private $formBackgroundColorAll = null;

    /**
     * @var array
     */
    private $formForegroundColorAll = null;

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

            $this->formBackgroundColorAll = imagecolorallocate(
                $this->formImage,
                $this->formBackgroundColor['red'],
                $this->formBackgroundColor['green'],
                $this->formBackgroundColor['blue']
            );
            $this->formForegroundColorAll = imagecolorallocate(
                $this->formImage,
                $this->formForegroundColor['red'],
                $this->formForegroundColor['green'],
                $this->formForegroundColor['blue']
            );
        }

        $zoomWidth = $this->squareWidth * $this->squareScale;
        $zoomHeight = $this->squareHeight * $this->squareScale;

        $y1 = ($squareImageHeight - $zoomHeight) / 2;
        $y2 = $y1 + $zoomHeight;
        $xRepeater = $squareImageWidth / 4;
        $x1 = ($xRepeater - $zoomWidth) / 2;

        imagefilledrectangle(
            $this->formImage,
            $x1, $y1,
            $x1 + $zoomWidth, $y2,
            $this->formForegroundColorAll
        );
        imagefilledrectangle(
            $this->formImage,
            $x1 + $xRepeater, $y1,
            $x1 + $xRepeater + $zoomWidth, $y2,
            $this->formForegroundColorAll
        );
        imagefilledrectangle(
            $this->formImage,
            $x1 + $xRepeater * 2, $y1,
            $x1 + $xRepeater * 2 + $zoomWidth, $y2,
            $this->formForegroundColorAll
        );
        imagefilledrectangle(
            $this->formImage,
            $x1 + $xRepeater * 3, $y1,
            $x1 + $xRepeater * 3 + $zoomWidth, $y2,
            $this->formForegroundColorAll
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
