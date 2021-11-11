<?php

namespace EmailCountdown;

class SquareCountdown extends AbstractCountdown
{
    /**
     * @var int
     */
    protected $squareWidth = 80;

    /**
     * @var int
     */
    protected $squareHeight = 60;

    /**
     * @var array
     */
    protected $formBackgroundColorAll = null;

    /**
     * @var array
     */
    protected $formForegroundColorAll = null;

    /**
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return resource
     */
    protected function getFormImage($days, $hours, $minutes, $seconds)
    {
        $formImageWidth = $this->width * $this->scale;
        $formImageHeight = $this->height * $this->scale;
        $squareWidth = $this->squareWidth * $this->scale;
        $squareHeight = $this->squareHeight * $this->scale;

        if (empty($this->formImage)) {

            // create the square image once
            $this->formImage = imagecreatetruecolor($formImageWidth, $formImageHeight);

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
                $formImageWidth, $formImageHeight,
                $backgroundColor
            );

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

        $y1 = ($formImageHeight - $squareHeight) / 2;
        $y2 = $y1 + $squareHeight;
        $xRepeater = $formImageWidth / 4;
        $x1 = ($xRepeater - $squareWidth) / 2;

        imagefilledrectangle(
            $this->formImage,
            $x1, $y1,
            $x1 + $squareWidth, $y2,
            $this->formForegroundColorAll
        );
        imagefilledrectangle(
            $this->formImage,
            $x1 + $xRepeater, $y1,
            $x1 + $xRepeater + $squareWidth, $y2,
            $this->formForegroundColorAll
        );
        imagefilledrectangle(
            $this->formImage,
            $x1 + $xRepeater * 2, $y1,
            $x1 + $xRepeater * 2 + $squareWidth, $y2,
            $this->formForegroundColorAll
        );
        imagefilledrectangle(
            $this->formImage,
            $x1 + $xRepeater * 3, $y1,
            $x1 + $xRepeater * 3 + $squareWidth, $y2,
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
            $this->width * $this->scale, $this->height * $this->scale,
            $this->width * $this->scale, $this->height * $this->scale
        );

        return $frame;
    }
}
