<?php

namespace EmailCountdown;

abstract class AbstractCountdown
{
    const BACKGROUND_TYPE_DEFAULT = 'default';
    const BACKGROUND_TYPE_TRANSPARENT = 'transparent';

    /**
     * @var string
     */
    protected $backgroundType = self::BACKGROUND_TYPE_DEFAULT;

    /**
     * @var int
     */
    protected $scale = 1;

    /**
     * @var int we create only 60 frames/seconds to create the fake counter
     */
    protected $maxFrames = 60;

    /**
     * @var int currently fixed, if changed to dynamic size, don't forget to change the positioning below!
     */
    protected $width = 400;

    /**
     * @var int
     */
    protected $height = 100;

    /**
     * @var string
     */
    protected $fontFile = __DIR__ . '/../fonts/ARIAL.TTF';

    /**
     * @var int 100 ticks in gif -> 1 second in real time
     */
    protected $gifTicks = 100;

    /**
     * @var int
     */
    protected $gifLoops = 10;

    /**
     * @var \DateTime
     */
    protected $destinationTime = null;

    /**
     * @var array
     */
    protected $backgroundColor = [
        'red'   => 255,
        'green' => 255,
        'blue'  => 255
    ];

    /**
     * @var array
     */
    protected $textColor = [
        'red'   => 80,
        'green' => 80,
        'blue'  => 80
    ];

    /**
     * @var array
     */
    protected $formBackgroundColor = [
        'red'   => 255,
        'green' => 204,
        'blue'  => 204
    ];

    /**
     * @var array
     */
    protected $formForegroundColor = [
        'red'   => 255,
        'green' => 0,
        'blue'  => 0
    ];

    /**
     * @var resource
     */
    protected $formImage = null;

    /**
     * @var array
     */
    protected $textData = [
        'days'    => [
            'textSize'       => 30,
            'textPositionX'  => 50,
            'textPositionY'  => 62,
            'label'          => 'TAGE',
            'labelSize'      => 7,
            'labelPositionX' => 50,
            'labelPositionY' => 75
        ],
        'hours'   => [
            'textSize'       => 30,
            'textPositionX'  => 152,
            'textPositionY'  => 62,
            'label'          => 'STUNDEN',
            'labelSize'      => 7,
            'labelPositionX' => 152,
            'labelPositionY' => 75
        ],
        'minutes' => [
            'textSize'       => 30,
            'textPositionX'  => 252,
            'textPositionY'  => 62,
            'label'          => 'MINUTEN',
            'labelSize'      => 7,
            'labelPositionX' => 252,
            'labelPositionY' => 75
        ],
        'seconds' => [
            'textSize'       => 30,
            'textPositionX'  => 352,
            'textPositionY'  => 62,
            'label'          => 'SEKUNDEN',
            'labelSize'      => 7,
            'labelPositionX' => 352,
            'labelPositionY' => 75
        ]
    ];

    /**
     * @var array
     */
    protected $textLabelColor = [
        'red'   => 60,
        'green' => 60,
        'blue'  => 60
    ];

    /**
     * @var bool
     */
    protected $showTextLabel = true;

    /**
     * @param \DateTime $destinationTime
     */
    public function __construct($destinationTime = null)
    {
        $this->setDestinationTime($destinationTime);
    }

    protected abstract function getFormImage($days, $hours, $minutes, $seconds);
    protected abstract function modifyFrame($frame, $days, $hours, $minutes, $seconds);

    /**
     * @param string $formBackgroundColor
     * @return $this
     */
    public function setFormBackgroundColor($formBackgroundColor)
    {
        if (!empty($formBackgroundColor) && preg_match('/[0-9a-fA-F]{6}/', $formBackgroundColor) == 1) {
            $this->formBackgroundColor = self::convertHexToRGB($formBackgroundColor);
        }

        return $this;
    }

    /**
     * @param string $formForegroundColor
     * @return $this
     */
    public function setFormForegroundColor($formForegroundColor)
    {
        if (!empty($formForegroundColor) && preg_match('/[0-9a-fA-F]{6}/', $formForegroundColor) == 1) {
            $this->formForegroundColor = self::convertHexToRGB($formForegroundColor);
        }

        return $this;
    }

    /**
     * set text and position for labels and texts
     *
     * @param string $part
     * @param string $label
     * @param int|null $textSize
     * @param int|null $textPositionX
     * @param int|null $textPositionY
     * @param int|null $labelSize
     * @param int|null $labelPositionX
     * @param int|null $labelPositionY
     * @return $this
     */
    public function setTextData(
        string $part,
        string $label = null,
        int $textSize = null,
        int $textPositionX = null,
        int $textPositionY = null,
        int $labelSize = null,
        int $labelPositionX = null,
        int $labelPositionY = null
    ) {
        if (array_key_exists($part, $this->textData)) {
            $this->textData[$part]['label'] = $label ?? $this->textData[$part]['label'];
            $this->textData[$part]['textSize'] = $textSize ?? $this->textData[$part]['textSize'];
            $this->textData[$part]['textPositionX'] = $textPositionX ?? $this->textData[$part]['textPositionX'];
            $this->textData[$part]['textPositionY'] = $textPositionY ?? $this->textData[$part]['textPositionY'];
            $this->textData[$part]['labelSize'] = $labelSize ?? $this->textData[$part]['labelSize'];
            $this->textData[$part]['labelPositionX'] = $labelPositionX ?? $this->textData[$part]['labelPositionX'];
            $this->textData[$part]['labelPositionY'] = $labelPositionY ?? $this->textData[$part]['labelPositionY'];
        }
        return $this;
    }

    /**
     * Set the destination time for the fake countdown
     *
     * @param \DateTime $destinationTime
     * @return $this
     * @throws \Exception
     */
    public function setDestinationTime($destinationTime)
    {
        if ($destinationTime instanceof \DateTime) {
            $this->destinationTime = $destinationTime;
        } else {
            $datetime = \DateTime::createFromFormat('dmYHi', $destinationTime);
            if (empty($destinationTime) || $datetime === false) {
                $this->destinationTime = new \DateTime();
                $this->destinationTime->modify('+1 day');
            } else {
                $this->destinationTime = $datetime;
            }
        }
        return $this;
    }

    /**
     * Set the color for the text labels
     *
     * @param string $textColor must be in hex code i.e. 00ff00
     * @return $this
     */
    public function setTextColor($textColor)
    {
        if (!empty($textColor) && preg_match('/[0-9a-fA-F]{6}/', $textColor) == 1) {
            $this->textColor = self::convertHexToRGB($textColor);
        }
        return $this;
    }

    /**
     * Set the background color
     *
     * @param string $backgroundColor must be in hex code i.e. ff0000
     * @return $this
     */
    public function setBackgroundColor($backgroundColor)
    {
        if (!empty($backgroundColor) && preg_match('/[0-9a-fA-F]{6}/', $backgroundColor) == 1) {
            $this->backgroundColor = self::convertHexToRGB($backgroundColor);
        }
        return $this;
    }

    /**
     * Convert hex color code to array
     *
     * @param string $color
     * @return array
     */
    protected static function convertHexToRGB($color)
    {
        $int = hexdec($color);
        return [
            'red'   => 0xFF & ($int >> 0x10),
            'green' => 0xFF & ($int >> 0x8),
            'blue'  => 0xFF & $int
        ];
    }

    /**
     * Create a new gif frame image with gd-library
     *
     * @return resource
     */
    protected function createFrame()
    {
        // create frame
        $frame = imagecreatetruecolor($this->width * $this->scale, $this->height * $this->scale);

        // background color again
        if ($this->backgroundType === self::BACKGROUND_TYPE_TRANSPARENT) {
            $backgroundColor = imagecolorallocatealpha(
                $frame,
                $this->backgroundColor['red'],
                $this->backgroundColor['green'],
                $this->backgroundColor['blue'],
                127
            );
            imagealphablending($frame, false);
            imagesavealpha($frame, true);
        } else {
            $backgroundColor = imagecolorallocate(
                $frame,
                $this->backgroundColor['red'],
                $this->backgroundColor['green'],
                $this->backgroundColor['blue']
            );
        }

        imagefilledrectangle(
            $frame,
            0, 0,
            $this->width * $this->scale, $this->height * $this->scale,
            $backgroundColor
        );

        return $frame;
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

        imagettftext(
            $frame,
            $this->textData['days']['textSize'] * $this->scale,
            0,
            $daysPositionX, $this->textData['days']['textPositionY'] * $this->scale,
            $textColor,
            $this->fontFile,
            sprintf('%02d', $days)
        );
        imagettftext(
            $frame,
            $this->textData['hours']['textSize'] * $this->scale,
            0,
            $hoursPositionX, $this->textData['hours']['textPositionY'] * $this->scale,
            $textColor,
            $this->fontFile,
            sprintf('%02d', $hours)
        );
        imagettftext(
            $frame,
            $this->textData['minutes']['textSize'] * $this->scale,
            0,
            $minutesPositionX, $this->textData['minutes']['textPositionY'] * $this->scale,
            $textColor,
            $this->fontFile,
            sprintf('%02d', $minutes)
        );
        imagettftext(
            $frame,
            $this->textData['seconds']['textSize'] * $this->scale,
            0,
            $secondsPositionX, $this->textData['seconds']['textPositionY'] * $this->scale,
            $textColor,
            $this->fontFile,
            sprintf('%02d', $seconds)
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
     * Build a new frame (one image in the countdown)
     *
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return resource
     */
    protected function buildFrame($days, $hours, $minutes, $seconds)
    {
        $mainFrame = $this->createFrame();
        $modifyFrame = $this->modifyFrame($mainFrame, $days, $hours, $minutes, $seconds);
        return $this->addText($modifyFrame, $days, $hours, $minutes, $seconds);
    }

    /**
     * Get the fake countdown as gif
     *
     * @return string
     * @throws \Exception
     */
    public function getGIFAnimation()
    {
        $frames = [];
        $current_time = new \DateTime();

        for ($i = 0; $i < $this->maxFrames; $i++) {
            if ($current_time > $this->destinationTime) {
                $seconds = $minutes = $hours = $days = 0;
            } else {
                $current_time->modify('+1 second');
                $time_left = $current_time->diff($this->destinationTime, true);
                $seconds = $time_left->s;
                $minutes = $time_left->i;
                $hours = $time_left->h;
                $days = $time_left->days;
            }

            // $curTime = microtime(true);

            $frames[] = $this->buildFrame($days, $hours, $minutes, $seconds);

            // $timeConsumed = round(microtime(true) - $curTime,3)*1000;
            // error_log(__METHOD__.': '.($i+1).', took '.$timeConsumed.' ms');

            if ($seconds == 0 && $minutes == 0 && $hours == 0 && $days == 0) {
                // we don't need any more frames if already at zero time left
                break;
            }
        }

        // use GIFCreator to create the gif animation
        $animation = new GifCreator();

        $animation->create($frames, array_fill(0, count($frames), $this->gifTicks), $this->gifLoops);
        return $animation->getGIF();
    }

    /**
     * Use a different true type font file
     *
     * @param string $fontFile
     * @return $this
     */
    public function setFontFile(string $fontFile)
    {
        if (file_exists($fontFile)) {
            $this->fontFile = $fontFile;
        }
        return $this;
    }

    /**
     * Hide/show text labels
     *
     * @param bool $showTextLabel
     * @return $this
     */
    public function setShowTextLabel(bool $showTextLabel)
    {
        $this->showTextLabel = $showTextLabel;

        return $this;
    }

    /**
     * Set max frames
     *
     * @param int $maxFrames
     * @return $this
     */
    public function setMaxFrames(int $maxFrames)
    {
        $this->maxFrames = $maxFrames;
        return $this;
    }

    /**
     * @param float $scale
     * @return $this
     */
    public function setScale(float $scale)
    {
        $this->scale = $scale;
        return $this;
    }


    /**
     * @param string $textLabelColor
     * @return $this
     */
    public function setTextLabelColor($textLabelColor)
    {
        if (!empty($textLabelColor) && preg_match('/[0-9a-fA-F]{6}/', $textLabelColor) == 1) {
            $this->textLabelColor = self::convertHexToRGB($textLabelColor);
        }

        return $this;
    }

    /**
     * @param string $backgroundType
     * @return $this
     */
    public function setBackgroundType(string $backgroundType)
    {
        $this->backgroundType = $backgroundType;
        return $this;
    }
}
