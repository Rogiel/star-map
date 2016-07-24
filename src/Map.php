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

namespace Rogiel\StarMap;

use Rogiel\MPQ\MPQFile;
use Rogiel\MPQ\Stream\Parser\BinaryStreamParser;
use Rogiel\StarMap\Entity\DocumentHeader;
use Rogiel\StarMap\Entity\MapInfo;
use Rogiel\StarMap\Entity\Minimap;
use Rogiel\StarMap\Exception\MapException;

class Map {

    /**
     * @var MPQFile
     */
    private $file;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * The processed MapInfo file
     *
     * @var MapInfo
     */
    private $mapInfo;

    /**
     * The processed DocumentHeader file
     *
     * @var DocumentHeader
     */
    private $documentHeader;

    /**
     * The processed Minimap file
     *
     * @var Minimap
     */
    private $minimap;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Map constructor.
     * @param $file string|MPQFile the map file name or MPQFile instance
     * @throws MapException if the file given is not a string or a mpq file
     */
    public function __construct($file) {
        if(is_string($file)) {
            $file = MPQFile::parseFile($file);
        }
        if(!$file instanceof MPQFile) {
            throw new MapException("Invalid map file given");
        }

        $this->file = $file;
        $this->file->parse();
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Gets the MapInfo parsed structure
     *
     * @return MapInfo
     * @throws MapException
     */
    public function getMapInfo() {
        if($this->mapInfo != NULL) {
            return $this->mapInfo;
        }

        $stream = $this->file->openStream('MapInfo');
        if($stream == NULL) {
            throw new MapException("MapInfo file not found on map MPQ file.");
        }

        $parser = new BinaryStreamParser($stream);
        $this->mapInfo = new MapInfo($parser);
        return $this->mapInfo;
    }

    /**
     * Gets the MapInfo parsed structure
     *
     * @return DocumentHeader
     * @throws MapException
     */
    public function getDocumentHeader($locale = 'enUS') {
        if($this->documentHeader != NULL) {
            return $this->documentHeader;
        }

        $stream = $this->file->openStream('DocumentHeader');
        if($stream == NULL) {
            throw new MapException("DocumentHeader file not found on map MPQ file.");
        }

        $parser = new BinaryStreamParser($stream);
        $this->documentHeader = new DocumentHeader($parser);
        return $this->documentHeader;
    }

    /**
     * @return Minimap
     * @throws MapException
     */
    public function getMinimap() {
        if($this->minimap != NULL) {
            return $this->minimap;
        }

        $stream = $this->file->openStream('Minimap.tga');
        if($stream == NULL) {
            throw new MapException("Minimap.tga file not found on map MPQ file.");
        }

        $this->minimap = new Minimap($stream);
        return $this->minimap;
    }

    // -----------------------------------------------------------------------------------------------------------------

}