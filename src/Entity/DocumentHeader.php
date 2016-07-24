<?php
/**
 * PixForce
 *
 * @link      http://www.pixforce.com.br/
 * @copyright Copyright (c) 2016 PixForce (http://www.pixforce.com.br)
 * @license   Proprietary
 */

namespace Rogiel\StarMap\Entity;

use Rogiel\MPQ\Stream\Parser\BinaryStreamParser;

class DocumentHeader {

    const DEFAULT_LOCALE = 'enUS';

    private $name = array();
    private $shortDescription = array();
    private $longDescription = array();

    // -----------------------------------------------------------------------------------------------------------------

    public function __construct(BinaryStreamParser $parser) {
        $parser->readBytes(44);

        $numDeps = $parser->readByte();
        $parser->readBytes(3);
        while ($numDeps > 0) {
            while ($parser->readByte() !== 0);
            $numDeps--;
        }
        $numAttribs = $parser->readUInt32();
        $attribs = array();
        while ($numAttribs > 0) {
            $keyLen = $parser->readUInt16();
            $key = $parser->readBytes($keyLen);
            $locale = hex2bin(dechex($parser->readUInt32()));
            $valueLen = $parser->readUInt16();
            $value = $parser->readBytes($valueLen);
            $attribs[$key][$locale] = $value;
            $numAttribs--;
        }

        if(isset($attribs['DocInfo/Name'])) {
            $this->name = $attribs['DocInfo/Name'];
        }
        if(isset($attribs['DocInfo/DescShort'])) {
            $this->shortDescription = $attribs['DocInfo/DescShort'];
        }
        if(isset($attribs['DocInfo/DescLong'])) {
            $this->longDescription = $attribs['DocInfo/DescLong'];
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string|null
     */
    public function hasLocale($locale) {
        return $this->getName($locale) != NULL;
    }

    /**
     * @return array
     */
    public function getLocales() {
        return array_keys($this->name);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string|null
     */
    public function getName($locale = DocumentHeader::DEFAULT_LOCALE) {
        if(isset($this->name[$locale])) {
            return $this->name[$locale];
        }
        return NULL;
    }

    /**
     * @return string|null
     */
    public function getShortDescription($locale = DocumentHeader::DEFAULT_LOCALE) {
        if(isset($this->shortDescription[$locale])) {
            return $this->shortDescription[$locale];
        }
        return NULL;
    }

    /**
     * @return string|null
     */
    public function getLongDescription($locale = DocumentHeader::DEFAULT_LOCALE) {
        if(isset($this->longDescription[$locale])) {
            return $this->longDescription[$locale];
        }
        return NULL;
    }


}