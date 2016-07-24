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

use Rogiel\MPQ\Stream\Parser\BinaryStreamParser;
use Rogiel\StarMap\Exception\MapException;

class MapInfo {

    /**
     * The map info file format version
     *
     * @var integer
     */
    private $version;
    private $unknown1;
    private $unknown2;

    /**
     * The full map width
     *
     * @var integer
     */
    private $width;

    /**
     * The full map height
     *
     * @var integer
     */
    private $height;

    /**
     * Small map preview type: 0 = None, 1 = Minimap, 2 = Custom
     *
     * @var integer
     */
    private $smallPreviewType;

    /**
     * (Optional) Small map preview path; relative to root of map archive
     *
     * @var string
     */
    private $smallPreviewPath;

    /**
     * Large map preview type: 0 = None, 1 = Minimap, 2 = Custom
     *
     * @var integer
     */
    private $largePreviewType;

    /**
     * (Optional) Large map preview path; relative to root of map archive
     *
     * @var string
     */
    private $largePreviewPath;

    private $unknown3;
    private $unknown4;
    private $unknown5;
    private $unknown6;

    /**
     * The type of fog of war used on the map
     *
     * @var string
     */
    private $fogType;

    /**
     * The tile set used on the map
     *
     * @var string
     */
    private $tileSet;

    /**
     * The left bounds for the camera. This value is 7 less than the value shown in the editor.
     *
     * @var integer
     */
    private $cameraLeft;

    /**
     * The bottom bounds for the camera. This value is 4 less than the value shown in the editor.
     *
     * @var integer
     */
    private $cameraBottom;

    /**
     * The right bounds for the camera. This value is 7 more than the value shown in the editor.
     *
     * @var integer
     */
    private $cameraRight;

    /**
     * The top bounds for the camera. This value is 4 more than the value shown in the editor.
     *
     * @var integer
     */
    private $cameraTop;

    /**
     * The map base height (what is that?). This value is 4096*Base Height in the editor (giving a decimal value).
     *
     * @var integer
     */
    private $baseHeight;

// MIGHT NOT BE ACCURATE AFTER THIS
    /**
     * Load screen type: 0 = default, 1 = custom
     *
     * @var integer
     */
    private $loadScreenType;

    /**
     * (Optional) Load screen image path; relative to root of map archive
     *
     * @var string
     */
    private $loadScreenPath;

    /**
     * Unknown string, usually empty
     *
     * @var string
     */
    private $unknown7;

    /**
     * Load screen image scaling strategy: 0 = normal, 1 = aspect scaling, 2 = stretch the image.
     *
     * @var integer
     */
    private $loadScreenScaling;

    /**
     * The text position on the loading screen. One of:
     * 0xffffffff = (Default)
     * 0 = Top Left
     * 1 = Top
     * 2 = Top Right
     * 3 = Left
     * 4 = Center
     * 5 = Right
     * 6 = Bottom Left
     * 7 = Bottom
     * 8 = Bottom Right
     *
     * @var integer
     */
    private $textPosition;

    /**
     * Loading screen text position offset x
     *
     * @var integer
     */
    private $textPositionOffsetX;

    /**
     * Loading screen text position offset y
     *
     * @var integer
     */
    private $textPositionOffsetY;

    /**
     * Loading screen text size x
     *
     * @var integer
     */
    private $textPositionSizeX;

    /**
     * Loading screen text size y
     *
     * @var integer
     */
    private $textPositionSizeY;

    /**
     * A bit array of flags with the following options (possibly incomplete)
     *
     *  0x00000001 = Disable Replay Recording
     *  0x00000002 = Wait for Key (Loading Screen)
     *  0x00000004 = Disable Trigger Preloading
     *  0x00000008 = Enable Story Mode Preloading
     *  0x00000010 = Use Horizontal Field of View
     *
     * @var integer
     */
    private $dataFlags;

    private $unknown8;
    private $unknown9;
    private $unknown10;
    private $unknown11;

    public function __construct(BinaryStreamParser $parser) {
        $magic = $parser->readBytes(4);
        if($magic !== 'IpaM') {
            throw new MapException('Invalid MapInfo magic header');
        }

        $this->version = $parser->readUInt32();
        if ($this->version >= 0x18) {
            $this->unknown1 = $parser->readUInt32();
            $this->unknown2 = $parser->readUInt32();
        }

        $this->width = $parser->readUInt32();
        $this->height = $parser->readUInt32();

        $this->smallPreviewType = $parser->readUInt32();
        if ($this->smallPreviewType == 2) {
            $this->smallPreviewPath = $parser->readCString();
        }

        $this->largePreviewType = $parser->readUInt32();
        if ($this->largePreviewType == 2) {
            $this->largePreviewPath = $parser->readCString();
        }

        if ($this->version >= 0x1f) {
            $this->unknown3 = $parser->readCString();
        }

        if ($this->version >= 0x26) {
            $this->unknown4 = $parser->readCString();
        }

        if ($this->version >= 0x1f) {
            $this->unknown5 = $parser->readUInt32();
        }

        $this->unknown6 = $parser->readUInt32();
        $this->fogType = $parser->readCString();
        $this->tileSet = $parser->readCString();
        $this->cameraLeft = $parser->readUInt32();
        $this->cameraBottom = $parser->readUInt32();
        $this->cameraRight = $parser->readUInt32();
        $this->cameraTop = $parser->readUInt32();
        $this->baseHeight = $parser->readUInt32() / 4096;

        // -------------------------------------------------------------------------------------------------------------

        $this->loadScreenType = $parser->readUInt32();
        $this->loadScreenPath = $parser->readCString();
        $this->unknown7 = $parser->readBytes($parser->readUInt16());
        $this->loadScreenScaling = $parser->readUInt32();
        $this->textPosition = $parser->readUInt32();
        $this->textPositionOffsetX = $parser->readUInt32();
        $this->textPositionOffsetY = $parser->readUInt32();
        $this->textPositionSizeX = $parser->readUInt32();
        $this->textPositionSizeY = $parser->readUInt32();
        $this->dataFlags = $parser->readUInt32();
        $this->unknown8 = $parser->readUInt32();

        if ($this->version >= 0x19) {
            $this->unknown9 = $parser->readBytes(8);
        }
        if ($this->version >= 0x1f) {
            $this->unknown10 = $parser->readBytes(9);
        }
        if ($this->version >= 0x20) {
            $this->unknown11 = $parser->readBytes(4);
        }

        // there are more fields, but the implementation of them have been ommited
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return int
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getUnknown1() {
        return $this->unknown1;
    }

    /**
     * @return mixed
     */
    public function getUnknown2() {
        return $this->unknown2;
    }

    /**
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getSmallPreviewType() {
        return $this->smallPreviewType;
    }

    /**
     * @return string
     */
    public function getSmallPreviewPath() {
        return $this->smallPreviewPath;
    }

    /**
     * @return int
     */
    public function getLargePreviewType() {
        return $this->largePreviewType;
    }

    /**
     * @return string
     */
    public function getLargePreviewPath() {
        return $this->largePreviewPath;
    }

    /**
     * @return string
     */
    public function getUnknown3() {
        return $this->unknown3;
    }

    /**
     * @return string
     */
    public function getUnknown4() {
        return $this->unknown4;
    }

    /**
     * @return mixed
     */
    public function getUnknown5() {
        return $this->unknown5;
    }

    /**
     * @return mixed
     */
    public function getUnknown6() {
        return $this->unknown6;
    }

    /**
     * @return string
     */
    public function getFogType() {
        return $this->fogType;
    }

    /**
     * @return string
     */
    public function getTileSet() {
        return $this->tileSet;
    }

    /**
     * @return int
     */
    public function getCameraLeft() {
        return $this->cameraLeft;
    }

    /**
     * @return int
     */
    public function getCameraBottom() {
        return $this->cameraBottom;
    }

    /**
     * @return int
     */
    public function getCameraRight() {
        return $this->cameraRight;
    }

    /**
     * @return int
     */
    public function getCameraTop() {
        return $this->cameraTop;
    }

    /**
     * @return int
     */
    public function getBaseHeight() {
        return $this->baseHeight;
    }

    /**
     * @return int
     */
    public function getLoadScreenType() {
        return $this->loadScreenType;
    }

    /**
     * @return string
     */
    public function getLoadScreenPath() {
        return $this->loadScreenPath;
    }

    /**
     * @return string
     */
    public function getUnknown7() {
        return $this->unknown7;
    }

    /**
     * @return int
     */
    public function getLoadScreenScaling() {
        return $this->loadScreenScaling;
    }

    /**
     * @return int
     */
    public function getTextPosition() {
        return $this->textPosition;
    }

    /**
     * @return int
     */
    public function getTextPositionOffsetX() {
        return $this->textPositionOffsetX;
    }

    /**
     * @return int
     */
    public function getTextPositionOffsetY() {
        return $this->textPositionOffsetY;
    }

    /**
     * @return int
     */
    public function getTextPositionSizeX() {
        return $this->textPositionSizeX;
    }

    /**
     * @return int
     */
    public function getTextPositionSizeY() {
        return $this->textPositionSizeY;
    }

    /**
     * @return int
     */
    public function getDataFlags() {
        return $this->dataFlags;
    }

    /**
     * @return mixed
     */
    public function getUnknown8() {
        return $this->unknown8;
    }

    /**
     * @return string
     */
    public function getUnknown9() {
        return $this->unknown9;
    }

    /**
     * @return string
     */
    public function getUnknown10() {
        return $this->unknown10;
    }

    /**
     * @return string
     */
    public function getUnknown11() {
        return $this->unknown11;
    }

}