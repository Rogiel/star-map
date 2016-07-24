<?php
/**
 * Copyright (c) 2016, Rogiel Sulzbach
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
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
     * Gets the image resource
     *
     * @return resource
     */
    public function getImageResource() {
        return $this->resource;
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