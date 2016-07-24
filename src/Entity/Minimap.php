<?php
/**
 * PixForce
 *
 * @link      http://www.pixforce.com.br/
 * @copyright Copyright (c) 2016 PixForce (http://www.pixforce.com.br)
 * @license   Proprietary
 */

namespace Rogiel\StarMap\Entity;

use Rogiel\MPQ\Stream\Block\BlockStream;
use Rogiel\MPQ\Stream\Parser\BinaryStreamParser;
use Rogiel\MPQ\Stream\Stream;
use Rogiel\StarMap\Exception\MapException;

class Minimap {

    /**
     * @var resource
     */
    private $resource;

    // -----------------------------------------------------------------------------------------------------------------

    public function __construct(Stream $stream) {
        $buffer = '';
        while (($read = $stream->readBytes(10240))) {
            $buffer .= $read;
        }

        $this->resource = self::createImageResourceFromTGA($buffer);
        $buffer = NULL;
    }

    function __destruct() {
        imagedestroy($this->resource);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param string $filename
     * @param int    $compressionLevel
     * @param int    $filters see imagepng documentation for details on this field
     *
     * @return bool
     */
    public function toPNG($filename, $compressionLevel = 0, $filters = 0) {
        return imagepng($this->resource, $filename, $compressionLevel, $filters);
    }

    /**
     * @param string $filename
     * @param int    $quality
     *
     * @return bool
     *
     */
    public function toJPG($filename, $quality = 75) {
        return imagejpeg($this->resource, $filename, $quality);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param     $data
     * @param int $return_array
     *
     * @return array|resource
     */
    private static function createImageResourceFromTGA($data, $return_array = 0) {
        $pointer = 18;
        $x = 0;
        $y = 0;
        $w = base_convert(bin2hex(strrev(substr($data, 12, 2))), 16, 10);
        $h = base_convert(bin2hex(strrev(substr($data, 14, 2))), 16, 10);
        $img = imagecreatetruecolor($w, $h);

        while ($pointer < strlen($data)) {
            imagesetpixel($img, $x, $y, base_convert(bin2hex(strrev(substr($data, $pointer, 3))), 16, 10));
            $x++;

            if ($x == $w) {
                $y++;
                $x = 0;
            }

            $pointer += 3;
        }

        if ($return_array)
            return array($img, $w, $h);
        else
            return $img;
    }


}