<?php

namespace Tollwerk\TwLucenesearch\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  © 2020 Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>, tollwerk® GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once 'Zend/Search/Lucene/Document.php';

/**
 * Extended Zend lucene document
 *
 * @package tw_lucenesearch
 * @copyright Copyright © 2020 Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>, tollwerk® GmbH (http://tollwerk.de)
 * @author Dipl.-Ing. Joschi Kuphal <joschi@tollwerk.de>
 */
class Document extends \Zend_Search_Lucene_Document
{

    /************************************************************************************************
     * PUBLIC METHODS
     ***********************************************************************************************/

    /**
     * Return the document ID
     *
     * @return string                    Document ID
     */
    public function getId()
    {
        return $this->getFieldValue('id');
    }

    /**
     * Return the document ID (UTF-8)
     *
     * @return string                    Document ID (UTF-8)
     */
    public function getIdUtf8()
    {
        return $this->getFieldUtf8Value('id');
    }

    /**
     * Return the unique document ID
     *
     * @return string                    Unique document ID
     */
    public function getUid()
    {
        return $this->getFieldValue('uid');
    }

    /**
     * Return the unique document ID (UTF-8)
     *
     * @return string                    Unique document ID (UTF-8)
     */
    public function getUidUtf8()
    {
        return $this->getFieldUtf8Value('uid');
    }

    /**
     * Return the document title
     *
     * @return string                    Document title
     */
    public function getTitle()
    {
        return $this->getFieldValue('title');
    }

    /**
     * Return the document title (UTF-8)
     *
     * @return string                    Document title (UTF-8)
     */
    public function getTitleUtf8()
    {
        return $this->getFieldUtf8Value('title');
    }

    /**
     * Return the document text
     *
     * @return string                    Document text
     */
    public function getBodytext()
    {
        return $this->getFieldValue('bodytext');
    }

    /**
     * Return the document text (UTF-8)
     *
     * @return string                    Document text (UTF-8)
     */
    public function getBodytextUtf8()
    {
        return $this->getFieldUtf8Value('bodytext');
    }

    /**
     * Return the document text with highlighted search terms
     *
     * @return string                    Document text with highlighted search terms
     */
    public function getHighlightedBodytext()
    {
        require_once 'Zend/Search/Lucene/Search/QueryParser.php';
        return \Zend_Search_Lucene_Search_QueryParser::parse('jagd')->highlightMatches($this->getBodytext());
    }

    /**
     * Return the document language
     *
     * @return string                    Document language
     */
    public function getLanguage()
    {
        return $this->getFieldValue('language');
    }

    /**
     * Return the document language (UTF-8)
     *
     * @return string                    Document language (UTF-8)
     */
    public function getLanguageUtf8()
    {
        return $this->getFieldUtf8Value('language');
    }

    /**
     * Return a flag for the document language
     *
     * Fixes the language key "en" to the British flag key "en"
     *
     * @return string Document language flag
     */
    public function getFlag()
    {
        $flag = $this->getLanguage();
        return ($flag == 'en') ? 'gb' : $flag;
    }

    /**
     * Return the document timestamp
     *
     * @return string                    Document timestamp
     */
    public function getTimestamp()
    {
        return $this->getFieldValue('timestamp');
    }

    /**
     * Return the document timestamp (UTF-8)
     *
     * @return string                    Document timestamp (UTF-8)
     */
    public function getTimestampUtf8()
    {
        return $this->getFieldUtf8Value('timestamp');
    }

    /**
     * Return the document abstract
     *
     * @return string                    Return the document abstract
     */
    public function getAbstract()
    {
        return $this->getFieldValue('abstract');
    }

    /**
     * Return the document abstract (UTF-8)
     *
     * @return string                    Return the document abstract (UTF-8)
     */
    public function getAbstractUtf8()
    {
        return $this->getFieldUtf8Value('abstract');
    }

    /**
     * Return the document keywords
     *
     * @return string                    Document keywords
     */
    public function getKeywords()
    {
        return $this->getFieldValue('keywords');
    }

    /**
     * Return the document keywords (UTF-8)
     *
     * @return string                    Document keywords (UTF-8)
     */
    public function getKeywordsUtf8()
    {
        return $this->getFieldUtf8Value('keywords');
    }

    /**
     * Return the document reference
     *
     * @return string                    Document reference
     */
    public function getReference()
    {
        return $this->getFieldValue('reference');
    }

    /**
     * Return the document reference (UTF-8)
     *
     * @return string                    Document reference (UTF-8)
     */
    public function getReferenceUtf8()
    {
        return $this->getFieldUtf8Value('reference');
    }

    /**
     * Return the unserialized document reference parameters
     *
     * @return array                    Document reference parameters
     */
    public function getReferenceParameters()
    {
        return unserialize($this->getFieldValue('reference'));
    }

    /**
     * Return the document rootline
     *
     * @return string                    Document rootline
     */
    public function getRootline()
    {
        return $this->getFieldValue('rootline');
    }

    /**
     * Return the document rootline (UTF-8)
     *
     * @return string                    Document rootline (UTF-8)
     */
    public function getRootlineUtf8()
    {
        return $this->getFieldUtf8Value('rootline');
    }

    /**
     * Return the page ID of a search result
     *
     * @return int                        Page ID
     */
    public function getPageUid()
    {
        $referenceParams = $this->getReferenceParameters();
        return array_key_exists('id', $referenceParams) ? intval($referenceParams['id']) : 0;
    }

    /**
     * Return the additional URL parameters for building a page link
     *
     * @return string                    Additional URL parameters
     */
    public function getPageAdditionalParams()
    {
        $referenceParams = $this->getReferenceParameters();
        unset($referenceParams['id']);
        if (empty($referenceParams['type'])) {
            unset($referenceParams['type']);
        }
        return $referenceParams;
    }

    /************************************************************************************************
     * STATIC METHODS
     ***********************************************************************************************/

    /**
     * Cast a standard Zend lucene document as extended instance
     *
     * @param \Zend_Search_Lucene_Document $document Standard Zend lucene document
     * @return \Tollwerk\TwLucenesearch\Domain\Model\Document        Extended lucene document
     */
    public static function cast(\Zend_Search_Lucene_Document $document)
    {
        $extDocument = new self();
        foreach (get_object_vars($document) as $key => $value) {
            $extDocument->$key = $value;
        }
        return $extDocument;
    }
}
