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