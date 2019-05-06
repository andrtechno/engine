<?php

namespace panix\engine\components;

use yii\base\Component;
use yii\base\Exception;
use yii\web\Response;

class ImageHandler extends Component
{

    private $originalImage = null;
    private $image = null;
    private $format = 0;
    private $width = 0;
    private $height = 0;
    private $mimeType = '';
    private $fileName = '';
    public $transparencyColor = [0, 0, 0];
    public $jpegQuality = 100;
    public $alphaColor = [255, 255, 255];

    protected $newDimensions;
    /**
     * The maximum width an image can be after resizing (in pixels)
     *
     * @var int
     */
    protected $maxWidth;
    /**
     * The maximum height an image can be after resizing (in pixels)
     *
     * @var int
     */
    protected $maxHeight;

    const IMG_GIF = 1;
    const IMG_JPEG = 2;
    const IMG_PNG = 3;

    const CORNER_LEFT_TOP = 1;
    const CORNER_RIGHT_TOP = 2;
    const CORNER_LEFT_BOTTOM = 3;
    const CORNER_RIGHT_BOTTOM = 4;
    const CORNER_CENTER = 5;
    const CORNER_CENTER_TOP = 6;
    const CORNER_CENTER_BOTTOM = 7;
    const CORNER_LEFT_CENTER = 8;
    const CORNER_RIGHT_CENTER = 9;

    const FLIP_HORIZONTAL = 1;
    const FLIP_VERTICAL = 2;
    const FLIP_BOTH = 3;

    public $lib = 'Imagick';//test

    public function getImage()
    {
        return $this->image;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function __destruct()
    {
        $this->freeImage();
    }

    private function freeImage()
    {
        if (is_resource($this->image)) {
            imagedestroy($this->image);
        }

        if ($this->originalImage !== null) {
            if (is_resource($this->originalImage['image'])) {
                imagedestroy($this->originalImage['image']);
            }
            $this->originalImage = null;
        }
    }

    private function checkLoaded()
    {
        if (!is_resource($this->image)) {
            throw new Exception('Load image first');
        }
    }

    private function loadImage($file)
    {
        $result = array();


        if ($imageInfo = @getimagesize($file)) {
            $result['width'] = $imageInfo[0];
            $result['height'] = $imageInfo[1];

            $result['mimeType'] = $imageInfo['mime'];

            switch ($result['format'] = $imageInfo[2]) {
                case self::IMG_GIF:
                    if ($result['image'] = imagecreatefromgif($file)) {
                        return $result;
                    } else {
                        throw new Exception('Invalid image gif format');
                    }
                    break;
                case self::IMG_JPEG:
                    if ($result['image'] = imagecreatefromjpeg($file)) {
                        return $result;
                    } else {
                        throw new Exception('Invalid image jpeg format');
                    }
                    break;
                case self::IMG_PNG:
                    if ($result['image'] = imagecreatefrompng($file)) {
                        return $result;
                    } else {
                        throw new Exception('Invalid image png format');
                    }
                    break;
                default:
                    throw new Exception('Not supported image format');
            }
        } else {
            throw new Exception('Invalid image file');
        }
    }

    protected function initImage($image = false)
    {
        if ($image === false) {
            $image = $this->originalImage;
        }

        $this->width = $image['width'];
        $this->height = $image['height'];
        $this->mimeType = $image['mimeType'];
        $this->format = $image['format'];

        //Image
        if (is_resource($this->image))
            imagedestroy($this->image);

        $this->image = imagecreatetruecolor($this->width, $this->height);
        $this->preserveTransparency($this->image);
        imagecopy($this->image, $image['image'], 0, 0, 0, 0, $this->width, $this->height);
    }

    public function load($file)
    {
        $this->freeImage();

        if (($this->originalImage = $this->loadImage($file))) {
            $this->initImage();
            $this->fileName = $file;


            return $this;
        } else {
            return false;
        }
    }

    public function reload()
    {
        $this->checkLoaded();
        $this->initImage();

        return $this;
    }

    public function upload($toSave)
    {
        if (!move_uploaded_file($this->fileName, $toSave)) {
            throw new Exception('Invalid move_uploaded_file');
        }
    }

    private function preserveTransparency($newImage)
    {
        switch ($this->format) {
            case self::IMG_GIF:
                $color = imagecolorallocate(
                    $newImage, $this->transparencyColor[0], $this->transparencyColor[1], $this->transparencyColor[2]
                );

                imagecolortransparent($newImage, $color);
                imagetruecolortopalette($newImage, false, 256);
                break;
            case self::IMG_PNG:
                imagealphablending($newImage, false);

                $color = imagecolorallocatealpha(
                    $newImage, $this->transparencyColor[0], $this->transparencyColor[1], $this->transparencyColor[2], 0
                );

                imagefill($newImage, 0, 0, $color);
                imagesavealpha($newImage, true);
                break;
        }
    }

    /**
     * Resize an image to be no larger than $maxWidth or $maxHeight
     *
     * If either param is set to zero, then that dimension will not be considered as a part of the resize.
     * Additionally, if $this->options['resizeUp'] is set to true (false by default), then this function will
     * also scale the image up to the maximum dimensions provided.
     *
     * @param int $toWidth The maximum width of the image in pixels
     * @param int $toHeight The maximum height of the image in pixels
     * @return $this
     */
    public function resize($toWidth = 0, $toHeight = 0)
    {
        $this->checkLoaded();


       /* if($this->lib == 'Imagick'){
            $image = new \Imagick($this->image);

            $image->setImageCompressionQuality($this->jpegQuality);

            if ($toWidth && $toHeight) {
                if ($toWidth && $toHeight) {
                    $image->cropThumbnailImage($toWidth, $toHeight);
                } elseif ($toHeight) {
                    $image->thumbnailImage(0, $toHeight);
                } elseif ($toWidth) {
                    $image->thumbnailImage($toWidth, 0);
                } else {
                    throw new \Exception('Something wrong with this->module->parseSize($sizeString)');
                }
            }

            $image->writeImage($this->image);
        }*/



        $this->maxHeight = (intval($toHeight) > $this->height) ? $this->height : $toHeight;
         $this->maxWidth = (intval($toWidth) > $this->width) ? $this->width : $toWidth;
        //$this->maxHeight = intval($toHeight);
       // $this->maxWidth = intval($toWidth);

        // get the new dimensions...
        $this->calcImageSize($this->width, $this->height);

        // create the working image
        if (function_exists('imagecreatetruecolor')) {
            $newImage = imagecreatetruecolor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        } else {
            $newImage = imagecreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
        }

        $this->preserveTransparency($newImage);
        //$this->preserveAlpha();
        imagecopyresampled(
            $newImage,
            $this->image,
            0,
            0,
            0,
            0,
            $this->newDimensions['newWidth'],
            $this->newDimensions['newHeight'],
            $this->width,
            $this->height
        );

        imagedestroy($this->image);

        $this->image = $newImage;
        $this->width = $this->newDimensions['newWidth'];
        $this->height = $this->newDimensions['newHeight'];

        return $this;
    }

    /**
     * Adaptively Resizes the Image and Crops Using a Quadrant
     *
     * This function attempts to get the image to as close to the provided dimensions as possible, and then crops the
     * remaining overflow using the quadrant to get the image to be the size specified.
     *
     * The quadrants available are Top, Bottom, Center, Left, and Right:
     *
     *
     * +---+---+---+
     * |   | T |   |
     * +---+---+---+
     * | L | C | R |
     * +---+---+---+
     * |   | B |   |
     * +---+---+---+
     *
     * Note that if your image is Landscape and you choose either of the Top or Bottom quadrants (which won't
     * make sence since only the Left and Right would be available, then the Center quadrant will be used
     * to crop. This would have exactly the same result as using adaptiveResize().
     * The same goes if your image is portrait and you choose either the Left or Right quadrants.
     *
     * @param int $width
     * @param int $height
     * @param string $quadrant T, B, C, L, R
     * @return GdThumb
     */
    public function adaptiveResizeQuadrant($width, $height, $quadrant = 'C')
    {
        // make sure our arguments are valid
        if (!is_numeric($width) || $width == 0) {
            throw new InvalidArgumentException('$width must be numeric and greater than zero');
        }

        if (!is_numeric($height) || $height == 0) {
            throw new InvalidArgumentException('$height must be numeric and greater than zero');
        }

        // make sure we're not exceeding our image size if we're not supposed to
        // if ($this->options['resizeUp'] === false)
        // {
        $this->maxHeight = (intval($height) > $this->height) ? $this->height : $height;
        $this->maxWidth = (intval($width) > $this->width) ? $this->width : $width;
        //} else {
        //    $this->maxHeight	= intval($height);
        //    $this->maxWidth		= intval($width);
        // }

        $this->calcImageSizeStrict($this->width, $this->height);

        // resize the image to be close to our desired dimensions
        $this->resize($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);

        // reset the max dimensions...
        //if ($this->options['resizeUp'] === false)
        //{
        $this->maxHeight = (intval($height) > $this->height) ? $this->height : $height;
        $this->maxWidth = (intval($width) > $this->width) ? $this->width : $width;
        //}else{
        //    $this->maxHeight	= intval($height);
        //   $this->maxWidth		= intval($width);
        // }

        // create the working image
        if (function_exists('imagecreatetruecolor')) {
            $newImage = imagecreatetruecolor($this->maxWidth, $this->maxHeight);
        } else {
            $newImage = imagecreate($this->maxWidth, $this->maxHeight);
        }

        $this->preserveAlpha();

        $cropWidth = $this->maxWidth;
        $cropHeight = $this->maxHeight;
        $cropX = 0;
        $cropY = 0;

        // Crop the rest of the image using the quadrant

        if ($this->width > $this->maxWidth) {
            // Image is landscape
            switch ($quadrant) {
                case 'L':
                    $cropX = 0;
                    break;

                case 'R':
                    $cropX = intval(($this->width - $this->maxWidth));
                    break;

                case 'C':
                default:
                    $cropX = intval(($this->width - $this->maxWidth) / 2);
                    break;
            }


        } elseif ($this->height > $this->maxHeight) {
            // Image is portrait
            switch ($quadrant) {
                case 'T':
                    $cropY = 0;
                    break;

                case 'B':
                    $cropY = intval(($this->height - $this->maxHeight));
                    break;

                case 'C':
                default:
                    $cropY = intval(($this->height - $this->maxHeight) / 2);
                    break;
            }

        }

        imagecopyresampled
        (
            $newImage,
            $this->image,
            0,
            0,
            $cropX,
            $cropY,
            $cropWidth,
            $cropHeight,
            $cropWidth,
            $cropHeight
        );

        // update all the variables and resources to be correct
        $this->image = $newImage;
        $this->width = $this->maxWidth;
        $this->height = $this->maxHeight;

        return $this;
    }

    /**
     * Adaptively Resizes the Image
     *
     * This function attempts to get the image to as close to the provided dimensions as possible, and then crops the
     * remaining overflow (from the center) to get the image to be the size specified
     *
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function adaptiveResize($width, $height)
    {
        // make sure our arguments are valid
        if ((!is_numeric($width) || $width == 0) && (!is_numeric($height) || $height == 0)) {
            throw new InvalidArgumentException('$width and $height must be numeric and greater than zero');
        }

        if (!is_numeric($width) || $width == 0) {
            $width = ($height * $this->width) / $this->height;
        }

        if (!is_numeric($height) || $height == 0) {
            $height = ($width * $this->height) / $this->width;
        }

        // make sure we're not exceeding our image size if we're not supposed to
        if (false) {
            $this->maxHeight = (intval($height) > $this->height) ? $this->height : $height;
            $this->maxWidth = (intval($width) > $this->width) ? $this->width : $width;
        } else {
            $this->maxHeight = intval($height);
            $this->maxWidth = intval($width);
        }

        $this->calcImageSizeStrict($this->width, $this->height);

        // resize the image to be close to our desired dimensions
        $this->resize($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);

        // reset the max dimensions...
        // if ($this->options['resizeUp'] === false)
        // {
        //$this->maxHeight = (intval($height) > $this->height) ? $this->height : $height;
        //$this->maxWidth = (intval($width) > $this->width) ? $this->width : $width;
        // }
        // else
        // {
        $this->maxHeight = intval($height);
        $this->maxWidth = intval($width);
        // }

        // create the working image
        if (function_exists('imagecreatetruecolor')) {
            $newImage = imagecreatetruecolor($this->maxWidth, $this->maxHeight);
        } else {
            $newImage = imagecreate($this->maxWidth, $this->maxHeight);
        }

        $this->preserveAlpha();

        $cropWidth = $this->maxWidth;
        $cropHeight = $this->maxHeight;
        $cropX = 0;
        $cropY = 0;

        // now, figure out how to crop the rest of the image...
        if ($this->width > $this->maxWidth) {
            $cropX = intval(($this->width - $this->maxWidth) / 2);
        } elseif ($this->height > $this->maxHeight) {
            $cropY = intval(($this->height - $this->maxHeight) / 2);
        }

        imagecopyresampled
        (
            $newImage,
            $this->image,
            0,
            0,
            $cropX,
            $cropY,
            $cropWidth,
            $cropHeight,
            $cropWidth,
            $cropHeight
        );

        // update all the variables and resources to be correct
        $this->image = $newImage;
        $this->width = $this->maxWidth;
        $this->height = $this->maxHeight;

        return $this;
    }

    public function thumb($toWidth, $toHeight, $proportional = true)
    {
        $this->checkLoaded();

        if ($toWidth !== false)
            $toWidth = min($toWidth, $this->width);

        if ($toHeight !== false)
            $toHeight = min($toHeight, $this->height);


        $this->resize($toWidth, $toHeight, $proportional);

        return $this;
    }

    public function watermark($watermarkFile, $offsetX, $offsetY, $corner = self::CORNER_RIGHT_BOTTOM, $zoom = false)
    {

        $this->checkLoaded();

        if ($wImg = $this->loadImage($watermarkFile)) {

            if (in_array($wImg['mimeType'], array('image/png', 'image/gif'))) {
                $posX = 0;
                $posY = 0;

                $watermarkWidth = $wImg['width'];
                $watermarkHeight = $wImg['height'];

                if ($zoom !== false) {
                    $dimension = round(max($this->width, $this->height) * $zoom);

                    $watermarkHeight = $dimension;
                    $watermarkWidth = round($watermarkHeight / $wImg['height'] * $wImg['width']);

                    if ($watermarkWidth > $dimension) {
                        $watermarkWidth = $dimension;
                        $watermarkHeight = round($watermarkWidth / $wImg['width'] * $wImg['height']);
                    }
                }

                switch ($corner) {
                    case self::CORNER_LEFT_TOP:
                        $posX = $offsetX;
                        $posY = $offsetY;
                        break;
                    case self::CORNER_RIGHT_TOP:
                        $posX = $this->width - $watermarkWidth - $offsetX;
                        $posY = $offsetY;
                        break;
                    case self::CORNER_LEFT_BOTTOM:
                        $posX = $offsetX;
                        $posY = $this->height - $watermarkHeight - $offsetY;
                        break;
                    case self::CORNER_RIGHT_BOTTOM:
                        $posX = $this->width - $watermarkWidth - $offsetX;
                        $posY = $this->height - $watermarkHeight - $offsetY;
                        break;
                    case self::CORNER_CENTER:
                        $posX = floor(($this->width - $watermarkWidth) / 2);
                        $posY = floor(($this->height - $watermarkHeight) / 2);
                        break;
                    case self::CORNER_CENTER_TOP:
                        $posX = floor(($this->width - $watermarkWidth) / 2);
                        $posY = $offsetY;
                        break;
                    case self::CORNER_CENTER_BOTTOM:
                        $posX = floor(($this->width - $watermarkWidth) / 2);
                        $posY = $this->height - $watermarkHeight - $offsetY;
                        break;
                    case self::CORNER_LEFT_CENTER:
                        $posX = $offsetX;
                        $posY = floor(($this->height - $watermarkHeight) / 2);
                        break;
                    case self::CORNER_RIGHT_CENTER:
                        $posX = $this->width - $watermarkWidth - $offsetX;
                        $posY = floor(($this->height - $watermarkHeight) / 2);
                        break;
                    default:
                        throw new Exception('Invalid $corner value');
                }
                // $this->preserveTransparency($wImg['image']);
                // $this->preserveAlpha();
                imagecopyresampled(
                    $this->image, $wImg['image'], $posX, $posY, 0, 0, $watermarkWidth, $watermarkHeight, $wImg['width'], $wImg['height']
                );

                imagedestroy($wImg['image']);

                return $this;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function flip($mode)
    {
        $this->checkLoaded();

        $srcX = 0;
        $srcY = 0;
        $srcWidth = $this->width;
        $srcHeight = $this->height;

        switch ($mode) {
            case self::FLIP_HORIZONTAL:
                $srcX = $this->width - 1;
                $srcWidth = -$this->width;
                break;
            case self::FLIP_VERTICAL:
                $srcY = $this->height - 1;
                $srcHeight = -$this->height;
                break;
            case self::FLIP_BOTH:
                $srcX = $this->width - 1;
                $srcY = $this->height - 1;
                $srcWidth = -$this->width;
                $srcHeight = -$this->height;
                break;
            default:
                throw new Exception('Invalid $mode value');
        }

        $newImage = imagecreatetruecolor($this->width, $this->height);
        $this->preserveTransparency($newImage);

        imagecopyresampled($newImage, $this->image, 0, 0, $srcX, $srcY, $this->width, $this->height, $srcWidth, $srcHeight);

        imagedestroy($this->image);

        $this->image = $newImage;


        return $this;
    }

    public function rotate($degrees)
    {
        $this->checkLoaded();

        $degrees = (int)$degrees;
        $this->image = imagerotate($this->image, $degrees, 0);

        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);

        return $this;
    }

    public function crop($width, $height, $startX = false, $startY = false)
    {
        $this->checkLoaded();

        $width = (int)$width;
        $height = (int)$height;

//Centered crop
        $startX = $startX === false ? floor(($this->width - $width) / 2) : intval($startX);
        $startY = $startY === false ? floor(($this->height - $height) / 2) : intval($startY);

//Check dimensions
        $startX = max(0, min($this->width, $startX));
        $startY = max(0, min($this->height, $startY));
        $width = min($width, $this->width - $startX);
        $height = min($height, $this->height - $startY);


        $newImage = imagecreatetruecolor($width, $height);

        $this->preserveTransparency($newImage);

        imagecopyresampled($newImage, $this->image, 0, 0, $startX, $startY, $width, $height, $width, $height);

        imagedestroy($this->image);

        $this->image = $newImage;
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function text($text, $fontFile, $size = 12, $color = array(0, 0, 0), $corner = self::CORNER_LEFT_TOP, $offsetX = 0, $offsetY = 0, $angle = 0, $alpha = 0)
    {
        $this->checkLoaded();

        $bBox = imagettfbbox($size, $angle, $fontFile, $text);
        $textHeight = $bBox[1] - $bBox[7];
        $textWidth = $bBox[2] - $bBox[0];


        switch ($corner) {
            case self::CORNER_LEFT_TOP:
                $posX = $offsetX;
                $posY = $offsetY;
                break;
            case self::CORNER_RIGHT_TOP:
                $posX = $this->width - $textWidth - $offsetX;
                $posY = $offsetY;
                break;
            case self::CORNER_LEFT_BOTTOM:
                $posX = $offsetX;
                $posY = $this->height - $textHeight - $offsetY;
                break;
            case self::CORNER_RIGHT_BOTTOM:
                $posX = $this->width - $textWidth - $offsetX;
                $posY = $this->height - $textHeight - $offsetY;
                break;
            case self::CORNER_CENTER:
                $posX = floor(($this->width - $textWidth) / 2);
                $posY = floor(($this->height - $textHeight) / 2);
                break;
            case self::CORNER_CENTER_TOP:
                $posX = floor(($this->width - $textWidth) / 2);
                $posY = $offsetY;
                break;
            case self::CORNER_CENTER_BOTTOM:
                $posX = floor(($this->width - $textWidth) / 2);
                $posY = $this->height - $textHeight - $offsetY;
                break;
            case self::CORNER_LEFT_CENTER:
                $posX = $offsetX;
                $posY = floor(($this->height - $textHeight) / 2);
                break;
            case self::CORNER_RIGHT_CENTER:
                $posX = $this->width - $textWidth - $offsetX;
                $posY = floor(($this->height - $textHeight) / 2);
                break;
            default:
                throw new Exception('Invalid $corner value');
        }

        if ($alpha > 0) {
            $color = imagecolorallocatealpha($this->image, $color[0], $color[1], $color[2], $alpha);
        } else {
            $color = imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
        }

        imagettftext($this->image, $size, $angle, $posX, $posY + $textHeight, $color, $fontFile, $text);

        return $this;
    }

    public function adaptiveThumb($width, $height)
    {
        $this->checkLoaded();

        $width = intval($width);
        $height = intval($height);

        $widthProportion = $width / $this->width;
        $heightProportion = $height / $this->height;

        if ($widthProportion > $heightProportion) {
            $newWidth = $width;
            $newHeight = round($newWidth / $this->width * $this->height);
        } else {
            $newHeight = $height;
            $newWidth = round($newHeight / $this->height * $this->width);
        }

        $this->resize($newWidth, $newHeight);

        $this->crop($width, $height);

        return $this;
    }

    public function resizeCanvas($toWidth, $toHeight, $backgroundColor = array(255, 255, 255))
    {
        $this->checkLoaded();

        $newWidth = min($toWidth, $this->width);
        $newHeight = min($toHeight, $this->height);

        $widthProportion = $newWidth / $this->width;
        $heightProportion = $newHeight / $this->height;

        if ($widthProportion < $heightProportion) {
            $newHeight = round($widthProportion * $this->height);
        } else {
            $newWidth = round($heightProportion * $this->width);
        }

        $posX = floor(($toWidth - $newWidth) / 2);
        $posY = floor(($toHeight - $newHeight) / 2);


        $newImage = imagecreatetruecolor($toWidth, $toHeight);

        $backgroundColor = imagecolorallocate($newImage, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
        imagefill($newImage, 0, 0, $backgroundColor);

        imagecopyresampled($newImage, $this->image, $posX, $posY, 0, 0, $newWidth, $newHeight, $this->width, $this->height);

        imagedestroy($this->image);

        $this->image = $newImage;
        $this->width = $toWidth;
        $this->height = $toHeight;

        return $this;
    }

    public function grayscale()
    {
        $newImage = imagecreatetruecolor($this->width, $this->height);

        imagecopy($newImage, $this->image, 0, 0, 0, 0, $this->width, $this->height);
        imagecopymergegray($newImage, $newImage, 0, 0, 0, 0, $this->width, $this->height, 0);

        imagedestroy($this->image);

        $this->image = $newImage;

        return $this;
    }

    public function show($inFormat = false)
    {
        $this->checkLoaded();

        if (!$inFormat) {
            $inFormat = $this->format;
        }

        switch ($inFormat) {
            case self::IMG_GIF:
                header('Content-type: image/gif');
                imagegif($this->image);
                break;
            case self::IMG_JPEG:
                header('Content-type: image/jpeg');
                imagejpeg($this->image, null, $this->jpegQuality);
                break;
            case self::IMG_PNG:
                header('Content-type: image/png');
                imagepng($this->image);
                break;
            default:
                throw new Exception('Invalid image format for putput');
        }

        return $this;
    }

    public function save($file = false, $toFormat = false, $touch = false)
    {
        if (empty($file)) {
            $file = $this->fileName;
        }

        $this->checkLoaded();

        if (!$toFormat) {
            $toFormat = $this->format;
        }

        switch ($toFormat) {
            case self::IMG_GIF:
                if (!imagegif($this->image, $file)) {
                    throw new Exception('Can\'t save gif file');
                }
                break;
            case self::IMG_JPEG:
                if (!imagejpeg($this->image, $file, $this->jpegQuality)) {
                    throw new Exception('Can\'t save jpeg file');
                }
                break;
            case self::IMG_PNG:
                if (!imagepng($this->image, $file)) {
                    throw new Exception('Can\'t save png file');
                }
                break;
            default:
                throw new Exception('Invalid image format for save');
        }

        if ($touch && $file != $this->fileName) {
            touch($file, filemtime($this->fileName));
        }

        return $this;
    }

    /**
     * Calculates new image dimensions, not allowing the width and height to be less than either the max width or height
     *
     * @param int $width
     * @param int $height
     */
    protected function calcImageSizeStrict($width, $height)
    {
        // first, we need to determine what the longest resize dimension is..
        if ($this->maxWidth >= $this->maxHeight) {
            // and determine the longest original dimension
            if ($width > $height) {
                $newDimensions = $this->calcHeight($width, $height);

                if ($newDimensions['newWidth'] < $this->maxWidth) {
                    $newDimensions = $this->calcWidth($width, $height);
                }
            } elseif ($height >= $width) {
                $newDimensions = $this->calcWidth($width, $height);

                if ($newDimensions['newHeight'] < $this->maxHeight) {
                    $newDimensions = $this->calcHeight($width, $height);
                }
            }
        } elseif ($this->maxHeight > $this->maxWidth) {
            if ($width >= $height) {
                $newDimensions = $this->calcWidth($width, $height);

                if ($newDimensions['newHeight'] < $this->maxHeight) {
                    $newDimensions = $this->calcHeight($width, $height);
                }
            } elseif ($height > $width) {
                $newDimensions = $this->calcHeight($width, $height);

                if ($newDimensions['newWidth'] < $this->maxWidth) {
                    $newDimensions = $this->calcWidth($width, $height);
                }
            }
        }

        $this->newDimensions = $newDimensions;
    }


    /**
     * Calculates a new width and height for the image based on $this->maxWidth and the provided dimensions
     *
     * @return array
     * @param int $width
     * @param int $height
     */
    protected function calcHeight($width, $height)
    {
        $newHeightPercentage = (100 * $this->maxHeight) / $height;
        $newWidth = ($width * $newHeightPercentage) / 100;

        return array
        (
            'newWidth' => ceil($newWidth),
            'newHeight' => ceil($this->maxHeight)
        );
    }


    /**
     * Calculates a new width and height for the image based on $this->maxWidth and the provided dimensions
     *
     * @return array
     * @param int $width
     * @param int $height
     */
    protected function calcWidth($width, $height)
    {
        $newWidthPercentage = (100 * $this->maxWidth) / $width;
        $newHeight = ($height * $newWidthPercentage) / 100;

        return array
        (
            'newWidth' => intval($this->maxWidth),
            'newHeight' => intval($newHeight)
        );
    }

    /**
     * Calculates the new image dimensions
     *
     * These calculations are based on both the provided dimensions and $this->maxWidth and $this->maxHeight
     *
     * @param int $width
     * @param int $height
     */
    protected function calcImageSize($width, $height)
    {
        $newSize = array
        (
            'newWidth' => $width,
            'newHeight' => $height
        );

        if ($this->maxWidth > 0) {
            $newSize = $this->calcWidth($width, $height);

            if ($this->maxHeight > 0 && $newSize['newHeight'] > $this->maxHeight) {
                $newSize = $this->calcHeight($newSize['newWidth'], $newSize['newHeight']);
            }
        }

        if ($this->maxHeight > 0) {
            $newSize = $this->calcHeight($width, $height);

            if ($this->maxWidth > 0 && $newSize['newWidth'] > $this->maxWidth) {
                $newSize = $this->calcWidth($newSize['newWidth'], $newSize['newHeight']);
            }
        }

        $this->newDimensions = $newSize;
    }

    /**
     * Preserves the alpha or transparency for PNG and GIF files
     *
     * Alpha / transparency will not be preserved if the appropriate options are set to false.
     * Also, the GIF transparency is pretty skunky (the results aren't awesome), but it works like a
     * champ... that's the nature of GIFs tho, so no huge surprise.
     *
     * This functionality was originally suggested by commenter Aimi (no links / site provided) - Thanks! :)
     *
     */
    protected function preserveAlpha()
    {
        if ($this->format == 'PNG' && true) {
            imagealphablending($this->image, false);

            $colorTransparent = imagecolorallocatealpha
            (
                $this->image,
                $this->alphaColor[0],
                $this->alphaColor[1],
                $this->alphaColor[2],
                0
            );

            imagefill($this->image, 0, 0, $colorTransparent);
            imagesavealpha($this->image, true);
        }
        // preserve transparency in GIFs... this is usually pretty rough tho
        if ($this->format == 'GIF' && true) {
            $colorTransparent = imagecolorallocate
            (
                $this->image,
                $this->transparencyColor[0],
                $this->transparencyColor[1],
                $this->transparencyColor[2]
            );

            imagecolortransparent($this->image, $colorTransparent);
            imagetruecolortopalette($this->image, true, 256);
        }
    }
}