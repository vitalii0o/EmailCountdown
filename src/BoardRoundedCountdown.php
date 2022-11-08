<?php

namespace EmailCountdown;

class BoardRoundedCountdown extends AbstractCountdown
{
    /**
     * @var int
     */
    private $squareWidth = 32;

    /**
     * @var int
     */
    private $squareHeight = 46;

    /**
     * @var int
     */
    private $radiusCorner = 5;

    /**
     * @var array
     */
    private $formBackgroundColorAll = null;

    /**
     * @var array
     */
    private $formForegroundColorAll = null;

    /**
     * @var array
     */
    protected $textData = [
        'days'    => [
            'textSize'       => 32,
            'textPositionX'  => 77,
            'textPositionY'  => 64,
            'label'          => 'TAGE',
            'labelSize'      => 7,
            'labelPositionX' => 82,
            'labelPositionY' => 85
        ],
        'hours'   => [
            'textSize'       => 30,
            'textPositionX'  => 158,
            'textPositionY'  => 64,
            'label'          => 'STUNDEN',
            'labelSize'      => 7,
            'labelPositionX' => 164,
            'labelPositionY' => 85
        ],
        'minutes' => [
            'textSize'       => 30,
            'textPositionX'  => 238,
            'textPositionY'  => 64,
            'label'          => 'MINUTEN',
            'labelSize'      => 7,
            'labelPositionX' => 244,
            'labelPositionY' => 85
        ],
        'seconds' => [
            'textSize'       => 30,
            'textPositionX'  => 320,
            'textPositionY'  => 64,
            'label'          => 'SEKUNDEN',
            'labelSize'      => 7,
            'labelPositionX' => 325,
            'labelPositionY' => 85
        ]
    ];

    /**
     * @var array
     */
    protected $textLineColor = [
        'red'   => 60,
        'green' => 60,
        'blue'  => 60
    ];

    protected $lineThickness = 2;

    /**
     * @param string $textLineColor
     * @return $this
     */
    public function setTextLineColor($textLineColor)
    {
        if (!empty($textLineColor) && preg_match('/[0-9a-fA-F]{6}/', $textLineColor) == 1) {
            $this->textLineColor = self::convertHexToRGB($textLineColor);
        }

        return $this;
    }

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
     * Adding texts for each frame
     *
     * @param resource $frame
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return resource
     */
    protected function addText($frame, $days, $hours, $minutes, $seconds)
    {
        $textColor = imagecolorallocate($frame, $this->textColor['red'], $this->textColor['green'], $this->textColor['blue']);

        // Calculate center of bounding box, so text is centered
        $daysBBox = imagettfbbox($this->textData['days']['textSize'] * $this->scale,0, $this->fontFile, sprintf('%02d', $days));
        $daysPositionX = ($this->textData['days']['textPositionX'] * $this->scale) - ($daysBBox[4] / 2);

        $hoursBBox = imagettfbbox($this->textData['hours']['textSize'] * $this->scale, 0, $this->fontFile, sprintf('%02d', $hours));
        $hoursPositionX = ($this->textData['hours']['textPositionX'] * $this->scale) - ($hoursBBox[4] / 2);

        $minutesBBox = imagettfbbox($this->textData['minutes']['textSize'] * $this->scale, 0, $this->fontFile, sprintf('%02d', $minutes));
        $minutesPositionX = ($this->textData['minutes']['textPositionX'] * $this->scale) - ($minutesBBox[4] / 2);

        $secondsBBox = imagettfbbox($this->textData['seconds']['textSize'] * $this->scale, 0, $this->fontFile, sprintf('%02d', $seconds));
        $secondsPositionX = ($this->textData['seconds']['textPositionX'] * $this->scale) - ($secondsBBox[4] / 2);

        $days = sprintf('%02d', $days);
        $formatted = implode(' ', str_split($days));
        imagettftext(
            $frame,
            $this->textData['days']['textSize'] * $this->scale,
            0,
            $daysPositionX, $this->textData['days']['textPositionY'] * $this->scale,
            $textColor,
            $this->fontFile,
            $formatted
        );
        $hours = sprintf('%02d', $hours);
        $formatted = implode(' ', str_split($hours));
        imagettftext(
            $frame,
            $this->textData['hours']['textSize'] * $this->scale,
            0,
            $hoursPositionX, $this->textData['hours']['textPositionY'] * $this->scale,
            $textColor,
            $this->fontFile,
            $formatted
        );
        $minutes = sprintf('%02d', $minutes);
        $formatted = implode(' ', str_split($minutes));
        imagettftext(
            $frame,
            $this->textData['minutes']['textSize'] * $this->scale,
            0,
            $minutesPositionX, $this->textData['minutes']['textPositionY'] * $this->scale,
            $textColor,
            $this->fontFile,
            $formatted
        );
        $seconds = sprintf('%02d', $seconds);
        $formatted = implode(' ', str_split($seconds));
        imagettftext(
            $frame,
            $this->textData['seconds']['textSize'] * $this->scale,
            0,
            $secondsPositionX, $this->textData['seconds']['textPositionY'] * $this->scale,
            $textColor,
            $this->fontFile,
            $formatted
        );

        if ($this->showTextLabel) {
            $textLabelColor = imagecolorallocate($frame, $this->textLabelColor['red'], $this->textLabelColor['green'], $this->textLabelColor['blue']);

            $daysLabelBBox = imagettfbbox($this->textData['days']['labelSize'] * $this->scale, 0, $this->fontFile, $this->textData['days']['label']);
            $daysLabelPositionX = $this->textData['days']['labelPositionX'] * $this->scale - ($daysLabelBBox[4] / 2);

            $hoursLabelBBox = imagettfbbox($this->textData['hours']['labelSize'] * $this->scale, 0, $this->fontFile, $this->textData['hours']['label']);
            $hoursLabelPositionX = $this->textData['hours']['labelPositionX'] * $this->scale - ($hoursLabelBBox[4] / 2);

            $minutesLabelBBox = imagettfbbox($this->textData['minutes']['labelSize'] * $this->scale, 0, $this->fontFile, $this->textData['minutes']['label']);
            $minutesLabelPositionX = $this->textData['minutes']['labelPositionX'] * $this->scale - ($minutesLabelBBox[4] / 2);

            $secondsLabelBBox = imagettfbbox($this->textData['seconds']['labelSize'] * $this->scale, 0, $this->fontFile, $this->textData['seconds']['label']);
            $secondsLabelPositionX = $this->textData['seconds']['labelPositionX'] * $this->scale - ($secondsLabelBBox[4] / 2);

            imagettftext(
                $frame,
                $this->textData['days']['labelSize'] * $this->scale,
                0,
                $daysLabelPositionX, $this->textData['days']['labelPositionY'] * $this->scale,
                $textLabelColor,
                $this->fontFile,
                $this->textData['days']['label']
            );
            imagettftext(
                $frame,
                $this->textData['hours']['labelSize'] * $this->scale,
                0,
                $hoursLabelPositionX, $this->textData['hours']['labelPositionY'] * $this->scale,
                $textLabelColor,
                $this->fontFile,
                $this->textData['hours']['label']
            );
            imagettftext(
                $frame,
                $this->textData['minutes']['labelSize'] * $this->scale,
                0,
                $minutesLabelPositionX, $this->textData['minutes']['labelPositionY'] * $this->scale,
                $textLabelColor,
                $this->fontFile,
                $this->textData['minutes']['label']
            );
            imagettftext(
                $frame,
                $this->textData['seconds']['labelSize'] * $this->scale,
                0,
                $secondsLabelPositionX, $this->textData['seconds']['labelPositionY'] * $this->scale,
                $textLabelColor,
                $this->fontFile,
                $this->textData['seconds']['label']
            );
        }

        return $frame;
    }

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
        if (empty($this->formImage)) {

            // create the square image once
            $this->formImage = imagecreatetruecolor($formImageWidth, $formImageHeight);

            // background
            if ($this->backgroundType === self::BACKGROUND_TYPE_TRANSPARENT) {
                $backgroundColor = imagecolorallocatealpha(
                    $this->formImage,
                    $this->backgroundColor['red'],
                    $this->backgroundColor['green'],
                    $this->backgroundColor['blue'],
                    127
                );
                imagealphablending($this->formImage, false);
                imagesavealpha($this->formImage, true);
            } else {
                $backgroundColor = imagecolorallocate(
                    $this->formImage,
                    $this->backgroundColor['red'],
                    $this->backgroundColor['green'],
                    $this->backgroundColor['blue']
                );
            }
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

        $lineColor = imagecolorallocate(
            $this->formImage,
            $this->textLineColor['red'],
            $this->textLineColor['green'],
            $this->textLineColor['blue']
        );
        imagesetthickness($this->formImage, $this->lineThickness);

        $zoomWidth = $this->squareWidth * $this->scale;
        $zoomHeight = $this->squareHeight * $this->scale;

        $paddingLeft = 45 * $this->scale;
        $paddingBetweenNumbers = 2 * $this->scale;
        $paddingBetweenItems = 15 * $this->scale;
        $radiusCorner = $this->radiusCorner * $this->scale;

        $y1 = ($formImageHeight - $zoomHeight) / 2;
        $y2 = $y1 + $zoomHeight;
        $xRepeater = $formImageWidth / 10;
        $x1 = (($xRepeater - $zoomWidth) / 2) + $paddingLeft;
        $yLine = $formImageHeight / 2 - 2 * $this->scale;

        $xPointer = $x1;// + 50;

        // Days
        $this->imageRectangleWithRoundedCorners(
            $this->formImage,
            $xPointer, $y1,
            $xPointer + $zoomWidth, $y2,
            $radiusCorner,
            $this->formForegroundColorAll
        );
        imageline(
            $this->formImage,
            $xPointer, $yLine,
            $xPointer + $zoomWidth, $yLine,
            $lineColor
        );
        $xPointer += $zoomWidth + $paddingBetweenNumbers;
        $this->imageRectangleWithRoundedCorners(
            $this->formImage,
            $xPointer, $y1,
            $xPointer + $zoomWidth, $y2,
            $radiusCorner,
            $this->formForegroundColorAll
        );
        imageline(
            $this->formImage,
            $xPointer, $yLine,
            $xPointer + $zoomWidth, $yLine,
            $lineColor
        );

        // Hours
        $xPointer += + $zoomWidth + $paddingBetweenItems;
        $this->imageRectangleWithRoundedCorners(
            $this->formImage,
            $xPointer, $y1,
            $xPointer + $zoomWidth, $y2,
            $radiusCorner,
            $this->formForegroundColorAll
        );
        imageline(
            $this->formImage,
            $xPointer, $yLine,
            $xPointer + $zoomWidth, $yLine,
            $lineColor
        );
        $xPointer += $zoomWidth + $paddingBetweenNumbers;
        $this->imageRectangleWithRoundedCorners(
            $this->formImage,
            $xPointer, $y1,
            $xPointer + $zoomWidth, $y2,
            $radiusCorner,
            $this->formForegroundColorAll
        );
        imageline(
            $this->formImage,
            $xPointer, $yLine,
            $xPointer + $zoomWidth, $yLine,
            $lineColor
        );

        // Minutes
        $xPointer += + $zoomWidth + $paddingBetweenItems;
        $this->imageRectangleWithRoundedCorners(
            $this->formImage,
            $xPointer, $y1,
            $xPointer + $zoomWidth, $y2,
            $radiusCorner,
            $this->formForegroundColorAll
        );
        imageline(
            $this->formImage,
            $xPointer, $yLine,
            $xPointer + $zoomWidth, $yLine,
            $lineColor
        );
        $xPointer += $zoomWidth + $paddingBetweenNumbers;
        $this->imageRectangleWithRoundedCorners(
            $this->formImage,
            $xPointer, $y1,
            $xPointer + $zoomWidth, $y2,
            $radiusCorner,
            $this->formForegroundColorAll
        );
        imageline(
            $this->formImage,
            $xPointer, $yLine,
            $xPointer + $zoomWidth, $yLine,
            $lineColor
        );

        // Seconds
        $xPointer += + $zoomWidth + $paddingBetweenItems;
        $this->imageRectangleWithRoundedCorners(
            $this->formImage,
            $xPointer, $y1,
            $xPointer + $zoomWidth, $y2,
            $radiusCorner,
            $this->formForegroundColorAll
        );
        imageline(
            $this->formImage,
            $xPointer, $yLine,
            $xPointer + $zoomWidth, $yLine,
            $lineColor
        );
        $xPointer += $zoomWidth + $paddingBetweenNumbers;
        $this->imageRectangleWithRoundedCorners(
            $this->formImage,
            $xPointer, $y1,
            $xPointer + $zoomWidth, $y2,
            $radiusCorner,
            $this->formForegroundColorAll
        );
        imageline(
            $this->formImage,
            $xPointer, $yLine,
            $xPointer + $zoomWidth, $yLine,
            $lineColor
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

    protected function imageRectangleWithRoundedCorners(&$im, $x1, $y1, $x2, $y2, $radius, $color)
    {
// draw rectangle without corners
        imagefilledrectangle($im, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($im, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
// draw circled corners
        imagefilledellipse($im, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($im, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($im, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($im, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }
}
