<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/** Zend_Search_Lucene_Interface */
require_once 'Zend/Search/Lucene/Interface.php';

/** Zend_Search_Lucene_Document */
require_once 'Zend/Search/Lucene/Document.php';

/** Zend_Search_Lucene_Index_Term */
require_once 'Zend/Search/Lucene/Index/Term.php';

/** Zend_Search_Lucene_Analysis_Token */
require_once 'Zend/Search/Lucene/Analysis/Token.php';

/** Zend_Search_Lucene_Search_Similarity */
require_once 'Zend/Search/Lucene/Search/Similarity.php';

/** Zend_Search_Lucene_Search_QueryParser */
require_once 'Zend/Search/Lucene/Search/QueryParser.php';

/** Zend_Search_Lucene_Search_QueryHit */
require_once 'Zend/Search/Lucene/Search/QueryHit.php';

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_TempIndex implements Zend_Search_Lucene_Interface
{

    /**
     * document ID
     * @var integer
     */
    private $_docID = 0;

    /**
     * stored 'indexed' documents
     * @var array
     */
    private $_documents = array();

    /**
     * stored 'indexed' fields
     * @var array
     */
    private $_fields = array();

    /**
     * field for current simulated term stream
     * @var string
     */
    private $_currentField = '';

    /**
     * default search field
     * @var mixed
     */
    private $_defaultSearchField = null;

    /**
     * stored 'indexed' terms
     * (sorted by fieldname and text string)
     * @var array
     */
    private $_terms = array();

    /**
     * stored 'indexed' terms with documents 
     * (sorted by fieldname and text string)
     * @var array
     */
    private $_termDocs = array();

    /**
     * stored 'indexed' terms with positions in documents
     * @var array
     */
    private $_termPositions = array();

    /**
     * norm factors
     * @var array
     */
    private $_norms = array();

    /**
     * Result set limit
     *
     * 0 means no limit
     *
     * @var integer
     */
    private static $_resultSetLimit = 0;


    /**
     * Get current generation number
     *
     * Returns generation number
     * 0 means pre-2.1 index format
     * -1 means there are no segments files.
     *
     * @param Zend_Search_Lucene_Storage_Directory $directory
     * @return integer
     * @throws Zend_Search_Lucene_Exception
     */
    public static function getActualGeneration(Zend_Search_Lucene_Storage_Directory $directory)
    {
        return -1;
    }

    /**
     * Get segments file name
     *
     * @param integer $generation
     * @return string
     */
    public static function getSegmentFileName($generation)
    {
        return '';
    }

    /**
     * Get index format version
     *
     * @return integer
     */
    public function getFormatVersion()
    {
        return 0;
    }

    /**
     * Set index format version.
     * Index is converted to this format at the nearest upfdate time
     *
     * @param int $formatVersion
     * @throws Zend_Search_Lucene_Exception
     */
    public function setFormatVersion($formatVersion)
    {
    }

    /**
     * Returns the Zend_Search_Lucene_Storage_Directory instance for this index.
     *
     * @return Zend_Search_Lucene_Storage_Directory
     */
    public function getDirectory()
    {
        return null;
    }

    /**
     * Returns the total number of documents in this index (including deleted documents).
     *
     * @return integer
     */
    public function count()
    {
        return sizeof($this->_documents);
    }

    /**
     * Returns one greater than the largest possible document number.
     * This may be used to, e.g., determine how big to allocate a structure which will have
     * an element for every document number in an index.
     *
     * @return integer
     */
    public function maxDoc()
    {
        return $this->_docID;
    }

    /**
     * Returns the total number of non-deleted documents in this index.
     *
     * @return integer
     */
    public function numDocs()
    {
        return sizeof($this->_documents);
    }

    /**
     * Checks, that document is deleted
     *
     * @param integer $id
     * @return boolean
     * @throws Zend_Search_Lucene_Exception    Exception is thrown if $id is out of the range
     */
    public function isDeleted($id)
    {
        if (isset($this->_documents[$id])) return false;
        return true;
    }

    /**
     * Set default search field.
     *
     * Null means, that search is performed through all fields by default
     *
     * Default value is null
     *
     * @param string $fieldName
     */
    public static function setDefaultSearchField($fieldName)
    {
        $this->_defaultSearchField = $fieldName;
    }

    /**
     * Get default search field.
     *
     * Null means, that search is performed through all fields by default
     *
     * @return string
     */
    public static function getDefaultSearchField()
    {
        return $this->_defaultSearchField;
    }

    /**
     * Set result set limit.
     *
     * 0 (default) means no limit
     *
     * @param integer $limit
     */
    public static function setResultSetLimit($limit)
    {
        self::$_resultSetLimit = $limit;
    }

    /**
     * Set result set limit.
     *
     * 0 means no limit
     *
     * @return integer
     */
    public static function getResultSetLimit()
    {
        return self::$_resultSetLimit;
    }

    /**
     * Retrieve index maxBufferedDocs option
     *
     * maxBufferedDocs is a minimal number of documents required before
     * the buffered in-memory documents are written into a new Segment
     *
     * Default value is 10
     *
     * @return integer
     */
    public function getMaxBufferedDocs()
    {
        return 0;
    }

    /**
     * Set index maxBufferedDocs option
     *
     * maxBufferedDocs is a minimal number of documents required before
     * the buffered in-memory documents are written into a new Segment
     *
     * Default value is 10
     *
     * @param integer $maxBufferedDocs
     */
    public function setMaxBufferedDocs($maxBufferedDocs)
    {
        // has no function on temp index
    }

    /**
     * Retrieve index maxMergeDocs option
     *
     * maxMergeDocs is a largest number of documents ever merged by addDocument().
     * Small values (e.g., less than 10,000) are best for interactive indexing,
     * as this limits the length of pauses while indexing to a few seconds.
     * Larger values are best for batched indexing and speedier searches.
     *
     * Default value is PHP_INT_MAX
     *
     * @return integer
     */
    public function getMaxMergeDocs()
    {
        return 0;
    }

    /**
     * Set index maxMergeDocs option
     *
     * maxMergeDocs is a largest number of documents ever merged by addDocument().
     * Small values (e.g., less than 10,000) are best for interactive indexing,
     * as this limits the length of pauses while indexing to a few seconds.
     * Larger values are best for batched indexing and speedier searches.
     *
     * Default value is PHP_INT_MAX
     *
     * @param integer $maxMergeDocs
     */
    public function setMaxMergeDocs($maxMergeDocs)
    {
        // has no function on temp index
    }

    /**
     * Retrieve index mergeFactor option
     *
     * mergeFactor determines how often segment indices are merged by addDocument().
     * With smaller values, less RAM is used while indexing,
     * and searches on unoptimized indices are faster,
     * but indexing speed is slower.
     * With larger values, more RAM is used during indexing,
     * and while searches on unoptimized indices are slower,
     * indexing is faster.
     * Thus larger values (> 10) are best for batch index creation,
     * and smaller values (< 10) for indices that are interactively maintained.
     *
     * Default value is 10
     *
     * @return integer
     */
    public function getMergeFactor()
    {
        return 0;
    }

    /**
     * Set index mergeFactor option
     *
     * mergeFactor determines how often segment indices are merged by addDocument().
     * With smaller values, less RAM is used while indexing,
     * and searches on unoptimized indices are faster,
     * but indexing speed is slower.
     * With larger values, more RAM is used during indexing,
     * and while searches on unoptimized indices are slower,
     * indexing is faster.
     * Thus larger values (> 10) are best for batch index creation,
     * and smaller values (< 10) for indices that are interactively maintained.
     *
     * Default value is 10
     *
     * @param integer $maxMergeDocs
     */
    public function setMergeFactor($mergeFactor)
    {
        // has no function on temp index
    }

    /**
     * Performs a query against the index and returns an array
     * of Zend_Search_Lucene_Search_QueryHit objects.
     * Input is a string or Zend_Search_Lucene_Search_Query.
     *
     * 100% identical to Zend_Search_Lucene->find()
     *
     * @param mixed $query
     * @return array Zend_Search_Lucene_Search_QueryHit
     * @throws Zend_Search_Lucene_Exception
     */
    public function find($query)
    {
        if (is_string($query)) {
            $query = Zend_Search_Lucene_Search_QueryParser::parse($query);
        }

        if (!$query instanceof Zend_Search_Lucene_Search_Query) {
            require_once 'Zend/Search/Lucene/Exception.php';
            throw new Zend_Search_Lucene_Exception('Query must be a string or Zend_Search_Lucene_Search_Query object');
        }

        $this->commit();

        $hits   = array();
        $scores = array();
        $ids    = array();

        $query = $query->rewrite($this)->optimize($this);

        $query->execute($this);

        $topScore = 0;
        foreach ($query->matchedDocs() as $id => $num) {
            $docScore = $query->score($id, $this);
            if( $docScore != 0 ) {
                $hit = new Zend_Search_Lucene_Search_QueryHit($this);
                $hit->id = $id;
                $hit->score = $docScore;

                $hits[]   = $hit;
                $ids[]    = $id;
                $scores[] = $docScore;

                if ($docScore > $topScore) {
                    $topScore = $docScore;
                }
            }

            if (self::$_resultSetLimit != 0  &&  count($hits) >= self::$_resultSetLimit) {
                break;
            }
        }

        if (count($hits) == 0) {
            // skip sorting, which may cause a error on empty index
            return array();
        }

        if ($topScore > 1) {
            foreach ($hits as $hit) {
                $hit->score /= $topScore;
            }
        }

        if (func_num_args() == 1) {
            // sort by scores
            array_multisort($scores, SORT_DESC, SORT_NUMERIC,
                            $ids,    SORT_ASC,  SORT_NUMERIC,
                            $hits);
        } else {
            // sort by given field names

            $argList    = func_get_args();
            $fieldNames = $this->getFieldNames();
            $sortArgs   = array();

            // PHP 5.3 now expects all arguments to array_multisort be passed by
            // reference (if it's invoked through call_user_func_array());
            // since constants can't be passed by reference, create some placeholder variables.
            $sortReg    = SORT_REGULAR;
            $sortAsc    = SORT_ASC;
            $sortNum    = SORT_NUMERIC;

            require_once 'Zend/Search/Lucene/Exception.php';
            for ($count = 1; $count < count($argList); $count++) {
                $fieldName = $argList[$count];

                if (!is_string($fieldName)) {
                    throw new Zend_Search_Lucene_Exception('Field name must be a string.');
                }

                if (!in_array($fieldName, $fieldNames)) {
                    throw new Zend_Search_Lucene_Exception('Wrong field name.');
                }

                $valuesArray = array();
                foreach ($hits as $hit) {
                    try {
                        $value = $hit->getDocument()->getFieldValue($fieldName);
                    } catch (Zend_Search_Lucene_Exception $e) {
                        if (strpos($e->getMessage(), 'not found') === false) {
                            throw $e;
                        } else {
                            $value = null;
                        }
                    }

                    $valuesArray[] = $value;
                }

                $sortArgs[] = &$valuesArray;

                if ($count + 1 < count($argList)  &&  is_integer($argList[$count+1])) {
                    $count++;
                    $sortArgs[] = &$argList[$count];

                    if ($count + 1 < count($argList)  &&  is_integer($argList[$count+1])) {
                        $count++;
                        $sortArgs[] = &$argList[$count];
                    } else {
                        if ($argList[$count] == SORT_ASC  || $argList[$count] == SORT_DESC) {
                            $sortArgs[] = &$sortReg;
                        } else {
                            $sortArgs[] = &$sortAsc;
                        }
                    }
                } else {
                    $sortArgs[] = &$sortAsc;
                    $sortArgs[] = &$sortReg;
                }
            }

            // Sort by id's if values are equal
            $sortArgs[] = &$ids;
            $sortArgs[] = &$sortAsc;
            $sortArgs[] = &$sortNum;

            // Array to be sorted
            $sortArgs[] = &$hits;

            // Do sort
            call_user_func_array('array_multisort', $sortArgs);
        }

        return $hits;
    }

    /**
     * Returns a list of all unique field names that exist in this index.
     *
     * @param boolean $indexed
     * @return array
     */
    public function getFieldNames($indexed = false)
    {
        return array_keys($this->_fields);
    }

    /**
     * Returns a Zend_Search_Lucene_Document object for the document
     * number $id in this index.
     *
     * @param integer|Zend_Search_Lucene_Search_QueryHit $id
     * @return Zend_Search_Lucene_Document
     */
    public function getDocument($id)
    {
        return $this->_documents[$id];
    }

    /**
     * Returns true if index contain documents with specified term.
     *
     * Is used for query optimization.
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return boolean
     */
    public function hasTerm(Zend_Search_Lucene_Index_Term $term)
    {
        if (isset($this->_terms[$term->field][$term->text])) {
            return true;
        }
        return false;
    }

    /**
     * Returns IDs of all the documents containing term.
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @param Zend_Search_Lucene_Index_DocsFilter|null $docsFilter
     * @return array
     */
    public function termDocs(Zend_Search_Lucene_Index_Term $term, $docsFilter = null)
    {
        if ($docsFilter != null) {
            $allowed = array();
            foreach($docsFilter->segmentFilters as $seg) {
                $allowed = array_merge(array_keys($seg),$allowed);
            }
            $result = $this->termDocs($term);
            return array_intersect($result,$allowed);
        } else {
            if (isset($this->_termDocs[$term->field][$term->text])) {
                return array_keys($this->_termDocs[$term->field][$term->text]);
            } else {
                return array();
            }
        }
    }

    /**
     * Returns documents filter for all documents containing term.
     *
     * It performs the same operation as termDocs, but return result as
     * Zend_Search_Lucene_Index_DocsFilter object
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @param Zend_Search_Lucene_Index_DocsFilter|null $docsFilter
     * @return Zend_Search_Lucene_Index_DocsFilter
     */
    public function termDocsFilter(Zend_Search_Lucene_Index_Term $term, $docsFilter = null)
    {
        require_once 'Zend/Search/Lucene/Exception.php';
        throw new Zend_Search_Lucene_Exception('DocsFilter is unimplemented in TempIndex. This function is assumed to be buggy in Lucene.php (it returns an array instead of a DocsFilter object), and not used anywhere in ZEND code. Specification is unclear.');
    }

    /**
     * Returns an array of all term freqs.
     * Return array structure: array( docId => freq, ...)
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @param Zend_Search_Lucene_Index_DocsFilter|null $docsFilter
     * @return integer
     */
    public function termFreqs(Zend_Search_Lucene_Index_Term $term, $docsFilter = null)
    {
        if ($docsFilter != null) {
            $allowed = array();
            foreach($docsFilter->segmentFilters as $seg) {
                $allowed = array_merge($seg,$allowed);
            }
            $result = $this->termFreqs($term);
            return array_intersect_key($result,$allowed);
        } else {
            if (!isset($this->_termDocs[$term->field][$term->text])) {
                return array();
            } else {
                return $this->_termDocs[$term->field][$term->text];
            }
        }
    }

    /**
     * Returns an array of all term positions in the documents.
     * Return array structure: array( docId => array( pos1, pos2, ...), ...)
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @param Zend_Search_Lucene_Index_DocsFilter|null $docsFilter
     * @return array
     */
    public function termPositions(Zend_Search_Lucene_Index_Term $term, $docsFilter = null)
    {
        if ($docsFilter != null) {
            $allowed = array();
            foreach($docsFilter->segmentFilters as $seg) {
                $allowed = array_merge($seg,$allowed);
            }
            $result = $this->termPositions($term);
            return array_intersect_key($result,$allowed);
        } else {
            if (!isset($this->_termPositions[$term->field][$term->text])) {
                return array();
            } else {
                return $this->_termPositions[$term->field][$term->text];
            }
        }

    }

    /**
     * Returns the number of documents in this index containing the $term.
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return integer
     */
    public function docFreq(Zend_Search_Lucene_Index_Term $term)
    {
        return sizeof($this->_termDocs[$term->field][$term->text]);
    }

    /**
     * Retrive similarity used by index reader
     *
     * @return Zend_Search_Lucene_Search_Similarity
     */
    public function getSimilarity()
    {
        return Zend_Search_Lucene_Search_Similarity::getDefault();
    }

    /**
     * Returns a normalization factor for "field, document" pair.
     *
     * @param integer $id
     * @param string $fieldName
     * @return float
     */
    public function norm($id, $fieldName)
    {
        return $this->_norms[$fieldName][$id];
    }

    /**
     * Returns true if any documents have been deleted from this index.
     *
     * @return boolean
     */
    public function hasDeletions()
    {
        return false;
    }

    /**
     * Deletes a document from the index.
     * $id is an internal document id
     *
     * @param integer|Zend_Search_Lucene_Search_QueryHit $id
     * @throws Zend_Search_Lucene_Exception
     */
    public function delete($id)
    {
        unset($this->_documents[$id]);
    }

    /**
     * Adds a document to this index.
     *
     * @param Zend_Search_Lucene_Document $document
     */
    public function addDocument(Zend_Search_Lucene_Document $document)
    {
        $this->_documents[$this->_docID] = $document;

        // parse document
        $analyzer   = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
        $fieldNames = $document->getFieldnames();
        foreach ($fieldNames as $fieldName) {

            $field =  $document->getField($fieldName);

            // tokenize if requested
            if ($field->isTokenized) {
                $tokens = $analyzer->tokenize($field->getUtf8Value(), 'UTF-8');
            } else {
                $tokens = array( new Zend_Search_Lucene_Analysis_Token($field->getUtf8Value(),0,strlen(utf8_decode($field->getUtf8Value()))) );
            }

            // store tokens in "index"
            $position=-1;

            foreach( $tokens as $token ) {
                $text = $token->getTermText();
                $term = new Zend_Search_Lucene_Index_Term($text,$fieldName);

                $position += $token->getPositionIncrement();

                // build an ordered array (list) of terms for each field
                if (isset($this->_terms[$fieldName])) {

                    // if the term is not set already, sort it in
                    if (!isset($this->_terms[$fieldName][$text])) {
                        $new=array();
                        while (($current = array_shift($this->_terms[$fieldName])) && $text > $current->text) {
                            $new[$current->text] = $current;
                        }
                        $new[$text] = $term;
                        if ($current) $new[$current->text] = $current;
                        $this->_terms[$fieldName] = array_merge($new,$this->_terms[$fieldName]);
                    }
                } else {
                    // first terms in each field are just stored
                    $this->_terms[$fieldName][$text] = $term;
                }

                // store termPosition for this term
                $this->_termPositions[$fieldName][$text][$this->_docID][] = $position;
                
                // store or increase term freq for this document
                if (!isset($this->_termDocs[$fieldName][$text][$this->_docID])) {
                    $this->_termDocs[$fieldName][$text][$this->_docID] = 1;
                } else {
                    $this->_termDocs[$fieldName][$text][$this->_docID]++;
                }

            }

            // remember fieldname and document
            $this->_fields[$fieldName][$this->_docID] = 1;

            // calculate and store normalisation vector
            $this->_norms[$fieldName][$this->_docID] = $this->getSimilarity()->lengthNorm($fieldName,sizeof($tokens))*$document->boost*$field->boost;
        }

        // increase docID
        $this->_docID++;
    }

    /**
     * Commit changes resulting from delete() or undeleteAll() operations.
     */
    public function commit()
    {
        // cannot commit anything in memory
    }

    /**
     * Optimize index.
     *
     * Merges all segments into one
     */
    public function optimize()
    {
        // cannot optimize anything in memory
    }

    /**
     * Returns an array of all terms in this index.
     *
     * @return array
     */
    public function terms()
    {
        $result=array();
        foreach ($this->_terms as $fieldarray) {
            $result=array_merge($result,array_values($fieldarray));
        }
        return $result;
    }

    /**
     * Undeletes all documents currently marked as deleted in this index.
     */
    public function undeleteAll()
    {
        // objects in memory can not be undeleted
    }


    /**
     * Add reference to the index object
     *
     * @internal
     */
    public function addReference()
    {
        // has no function on temp index
    }

    /**
     * Remove reference from the index object
     *
     * When reference count becomes zero, index is closed and resources are cleaned up
     *
     * @internal
     */
    public function removeReference()
    {
        // has no function on temp index
    }

    /**
     * Reset terms stream.
     */
    public function resetTermsStream(){
        reset($this->_terms);
    }

    /**
     * Skip terms stream up to specified term prefix.
     *
     * Prefix contains fully specified field info and portion of searched term
     *
     * @param Zend_Search_Lucene_Index_Term $prefix
     */
    public function skipTo(Zend_Search_Lucene_Index_Term $prefix){
        $field = $prefix->field;
        $text  = $prefix->text;
        if (!$this->_terms[$field]) return;
        $this->_currentField = $field;
        reset($this->_terms[$field]);
        if (current($this->_terms[$field])->text < $text) {
            while (current($this->_terms[$field]) && current($this->_terms[$field])->text < $text) {
                next($this->_terms[$field]);
            };
        }
    }

    /**
     * Scans terms dictionary and returns next term
     *
     * @return Zend_Search_Lucene_Index_Term|null
     */
    public function nextTerm(){
        if ( !isset($this->_terms[$this->_currentField]) ) return null;
        return next($this->_terms[$this->_currentField]);
    }

    /**
     * Returns term in current position
     *
     * @return Zend_Search_Lucene_Index_Term|null
     */
    public function currentTerm(){
        if ( !isset($this->_terms[$this->_currentField]) ) return null;
        return current($this->_terms[$this->_currentField]);
    }

    /**
     * Close terms stream
     *
     * Should be used for resources clean up if stream is not read up to the end
     */
    public function closeTermsStream(){
        // has no function on temp index
    }

}