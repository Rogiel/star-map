<?php
/**
 * PixForce
 *
 * @link      http://www.pixforce.com.br/
 * @copyright Copyright (c) 2016 PixForce (http://www.pixforce.com.br)
 * @license   Proprietary
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