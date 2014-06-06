<?php
/*
 * AmazonProductRequest 
 * ====================
 * A lightweight and easily extendable class for making REST requests to
 * Amazon's Product Advertising API.
 * 
 * PHP Version  >= 5.4.0
 * Requires cURL
 *
 * Copyright (C) 2014 Yaakov Freedman
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @package AmazonProductRequest
 * @license http://www.gnu.org/licenses/gpl.txt GPL      
 * @author Yaakov Freedman <yaakovfreedman@yahoo.com>
 */
class AmazonProductRequest
{
    /* Local path of CA Root Certificates file needed for using the
     * SSL option. Available at http://curl.haxx.se/ca/cacert.pem
     * @type string
     * @access private
     */
    private $CERTPATH = 'cacert.pem';
    
    /*Constants for data validation. */
     
    /* List of valid locations.
     * @type array
     * @access private
     */
    private $LOCATIONS = array ('ca','com','cn', 'co.jp', 'co.uk','fr',
                                'it','cn');
    
    /* List of locations.
     * @type array
     * @access private
     */
    private $RESPONSEFORMATS = array ('string','simplexml', 'array');
    
    /* Configuration variables used for the request.
     * @type array
     * @access private
     */
    private $config = array();
    
    /* Amazon query parameters.
     * array keys in $params are used when sending queries to Amazon, and 
     * therefore must be in PascalCase.
     * @type array
     * @access private
     */ 
    private $params = array();
    
    /* Expressions used to construct the power search parameter that can be used
     * for the itemSearch operation. These are only useful when SearchIndex is 
     * set to 'Books'.
     * @type array
     * @access private
     */
    private $powerStrings = array();
    
    /* Message that will be populated and returned as an exception should the
     * execution of a request fail.
     * @type string
     * @access private
     */
    private $errorMsg;
    
    /* Constructor
     * @param string $keyId
     * @param string $tag
     * @param string $secretKey
     * @param string $version
     * @param string $location optional
     * @access public
     */
    public function __construct($keyId, $tag, $secretKey, $version, 
                                $location = 'com')
    {                
        $this->setConfigSecretKey($secretKey);
        $this->setConfigLocation($location);
        $this->config['ssl'] = false;
        $this->config['delay'] = false;
        $this->config['responseFormat'] = 'simplexml';
           
        $this->setAWSAccessKeyId($keyId);
        $this->setAssociateTag($tag);
        $this->setVersion($version);   
    }
    
    /******************
     * config getters * 
     ******************/
    
    /* Delay Configuration getter.
     * @return mixed boolean|null
     * @access public
     */
    public function getConfigDelay()
    {
        return isset($this->config['delay']) ? $this->config['delay'] : null;
    }
    
    /* Location Configuration getter.
     * @return mixed string|null
     * @access public
     */
    public function getConfigLocation()
    {
        return isset($this->config['location'])
        ? $this->config['location'] : null;
    }
    
    /* Response Format Configuration getter.
     * @return mixed string|null
     * @access public
     */
    public function getConfigResponseFormat()
    {
        return isset($this->config['responseFormat']) 
        ? $this->config['responseFormat'] : null;
    }
    
    /* Secret Key Configuration getter.
     * @return mixed string|null
     * @access public
     */
    public function getConfigSecretKey()
    {
        return isset($this->config['secretKey']) 
        ? $this->config['secretKey'] : null;
    }
    
    /* SSL Configuration getter.
     * @return mixed boolean|null
     * @access public
     */ 
    public function getConfigSsl()
    {
        return isset($this->config['ssl']) ? $this->config['ssl'] : null;
    }
    
    /******************
     * params getters * 
     ******************/
    
    /* Associate Tag getter.
     * @return mixed string|null
     * @access public
     */
    public function getAssociateTag()
    {
        return isset($this->params['AssociateTag'])
        ? $this->params['AssociateTag'] : null;
    }
    
    /* Author Parameter getter.
     * @return mixed string|null
     * @access public
     */    
    public function getAuthor()
    {
        return isset($this->params['Author']) ? $this->params['Author'] : null;
    }
    
    /* Availability Parameter getter.
     * @return boolean unset is interpreted as false and is the default
     * @access public
     */
    public function getAvailability()
    {
        return isset($this->params['Availability']) ? true : false;
    }
    
    /* AWS AccessKey ID Parameter getter.
     * @return mixed string|null
     * @access public
     */
    public function getAWSAccessKeyId()
    {
        return isset($this->params['AWSAccessKeyId'])
        ? $this->params['AWSAccessKeyId'] : null;
    }
    
    /* Brand Parameter getter.
     * @return mixed string|null
     * @access public
     */
    public function getBrand()
    {
        return isset($this->params['Brand']) ? $this->params['Brand'] : null;
    }
    
    /* Browse Node Parameter getter.
     * @return mixed string|null
     * @access public
     */
    public function getBrowseNode()
    {
        return isset($this->params['BrowseNode'])
        ? $this->params['BrowseNode'] : null;
    }
    
    /* Condition Parameter getter.
     * @return mixed string|null
     * @access public
     */
    public function getCondition()
    {
        return isset($this->params['Condition'])
        ? $this->params['Condition'] : null;
    }
    
    /* Id Type Parameter getter.
     * @return mixed string|null
     * @access public
     */
    public function getIdType()
    {
        return isset($this->params['IdType']) ? $this->params['IdType'] : null;
    }
    
    /* Item Page Parameter getter.
     * @return mixed integer|null
     * @access public
     */
    public function getItemPage()
    {
        return isset($this->params['ItemPage']) 
        ? $this->params['ItemPage'] : null;
    }
    
    /* Maximum Price Parameter getter.
     * @return mixed integer|null
     * @access public
     */
    public function getMaxPrice()
    {
        return isset($this->params['MaximumPrice']) 
        ? $this->params['MaximumPrice'] : null;
    }
    
    /* Minimum Price Parameter getter.
     * @return mixed integer|null
     * @access public
     */
    public function getMinPrice()
    {
        return isset($this->params['MinimumPrice']) 
        ? $this->params['MinimumPrice'] : null;
    }
    
    /* Merchant Id Parameter getter.
     * @return mixed string|null
     * @access public
     */    
    public function getMerchantId()
    {
        return isset($this->params['MerchantId']) 
        ? $this->params['MerchantId'] : null;
    }
    
    /* Publisher Parameter getter.
     * @return mixed string|null
     * @access public
     */    
    public function getPublisher()
    {
        return isset($this->params['Publisher']) 
        ? $this->params['Publisher'] : null;
    }
    
    /* Related Item Page Parameter getter.
     * @return mixed integer|null
     * @access public
     */    
    public function getRelatedItemPage()
    {
        return isset($this->params['RelatedItemPage']) 
        ? $this->params['RelatedItemPage'] : null;
    }
    
    /* Relationship Type Parameter getter.
     * @return mixed string|null
     * @access public
     */    
    public function getRelationshipType()
    {
        return isset($this->params['RelationshipType']) 
        ? $this->params['RelationshipType'] : null;
    }
    
    /* Response Group Parameter getter.
     * @return mixed string|null
     * @access public
     */    
    public function getResponseGroup()
    {
        return isset($this->params['ResponseGroup']) 
        ? $this->params['ResponseGroup'] : null;
    }
    
    /* Search Index Parameter getter.
     * @return mixed string|null
     * @access public
     */    
    public function getSearchIndex()
    {
        return isset($this->params['SearchIndex']) 
        ? $this->params['SearchIndex'] : null;
    }

    /* Sort Parameter getter.
     * @return mixed string|null
     * @access public
     */    
    public function getSort()
    {
        return isset($this->params['Sort']) ? $this->params['Sort'] : null;
    }

    /* Title Parameter getter.
     * @return mixed string|null
     * @access public
     */    
    public function getTitle()
    {
        return isset($this->params['Title']) ? $this->params['Title'] : null;
    }
    
    /* Version Parameter getter.
     * @return mixed string|null
     * @access public
     */    
    public function getVersion()
    {
        return isset($this->params['Version']) 
        ? $this->params['Version'] : null;
    }    

    /************************
     * powerStrings getters * 
     ************************/

    /* After Year Power Search String getter.
     * @return mixed integer|null
     */    
    public function getBookAfterYear()
    {
        return isset($this->powerStrings['afterYear']) 
        ? $this->powerStrings['afterYear']['value'] : null;
    }
    
    /* Before Year Power Search String getter.
     * @return mixed integer|null
     * @access public
     */    
    public function getBookBeforeYear()
    {
        return isset($this->powerStrings['beforeYear']) ? 
        $this->powerStrings['beforeYear']['value'] : null;
    }
    
    /* During Year Power Search String getter.
     * @return mixed integer|null
     * @access public
     */    
    public function getBookDuringYear()
    {
        return isset($this->powerStrings['duringYear']) ? 
        $this->powerStrings['duringYear']['value'] : null;
    }
    
    /* Language Power Search String getter.
     * @return mixed string|null
     * @access public
     */    
    public function getBookLanguage()
    {
        return isset($this->powerStrings['language']) ? 
        $this->powerStrings['language']['value'] : null;
    }
    
    /* Subject Power Search String getter.
     * @return mixed string|null
     * @access public
     */    
    public function getBookSubject()
    {
        return isset($this->powerStrings['subject']) ? 
        $this->powerStrings['subject']['value'] : null;
    }

     /****************** 
      * config setters *
      ******************/
     
    /* Delay Configuration setter.
     * @param boolean $delay
     * @return AmazonProductRequest
     * @access public
     */
    public function setConfigDelay($delay)
    {
        if (is_bool($delay) === false)
        {
            throw new Exception('Delay configuration must be true or false.');
        }
        
        $this->config['delay'] = $delay;
        
        return $this;
    }
    
    /* Location Configuration setter.
     * @param string $location non-case sensitive contained in $this->LOCATIONS
     * @return AmazonProductRequest
     * @access public
     */
    public function setConfigLocation($location)
    {
        $location = strtolower($location);
        
        if (in_array($location, $this->LOCATIONS) === false)
        {
            throw new Exception('Invalid location.');
        }
        
        $this->config['location'] = $location;
        
        return $this;
    }
    
    /* Response Format Configuration setter.
     * @param string $format non-case sensitive contained in 
     *     $this->RESPONSEFORMATS
     * @return AmazonProductRequest
     * @access public
     */
    public function setConfigResponseFormat($format)
    {
        $responseFormat = strtolower($format);
        if (in_array($format, $this->RESPONSEFORMATS) === false)
        {
            throw new Exception('Invalid response format.');
        }
        
        $this->config['responseFormat'] = $format;
        return $this;
    }
    
    /* Secret Key Configuration setter.
     * @param string $secretKey
     * @return AmazonProductRequest
     * @access public
     */
    public function setConfigSecretKey($secretKey)
    {    
        if (is_string($secretKey) === false)
        {
            throw new Exception ('Invalid Secret Key.');
        }
        
        $this->config['secretKey'] = $secretKey;
        
        return $this;
    }
    
    /* SSL Configuration setter.
     * @param boolean $ssl
     * @return AmazonProductRequest
     * @access public
     */
    public function setConfigSsl($ssl)
    {
        if (is_bool($ssl) === false)
        {
            throw new Exception('SSL configuration must be true or false.');
        }
        
        if ($ssl === true && 
            file_exists($this->CERTPATH) === false)
        {
            throw new Exception('SSL Root CA Cert file is not present.');
        }
        
        $this->config['ssl'] = $ssl;
        
        return $this;
    }
    
    /****************** 
     * params setters *
     ******************/
    
    /* Associate Tag Parameter setter.
     * @param boolean $tag
     * @return AmazonProductRequest
     * @access public
     */
    public function setAssociateTag($tag)
    {    
        if (is_string($tag) === false)
        {
            throw new Exception ('Invalid Associate Tag parameter.');
        }
        
        $this->params['AssociateTag'] = $tag;
        
        return $this;
    }
    
    /* Author parameter setter.
     * @param string $author
     * @return AmazonProductRequest
     * @access public
     */    
    public function setAuthor($author)
    {
        if (is_string($author) === false)
        {
            throw new Exception('Invalid Author parameter.');
        }
        
        $this->params['Author'] = $author;
        
        return $this;
    }
    
    /* Availability Parameter setter.
     * When set to true, only available items are returned.
     * When set to false, this filter is disabled.
     * @param boolean $availability
     * @return AmazonProductRequest
     * @access public
     */
    public function setAvailability($availability)
    {
        if ($availability === true)
        {
            $this->params['Availability'] = 'Available';
        }
        elseif ($availability === false)
        {
            if (isset($this->params['Availability']))
            {
                unset($this->params['Availability']);
            }
        }
        else
        {
            throw new Exception('Availability must be true or false.');
        }
        
        return $this;
    }
    
    /* AWS Access Key Id Parameter setter.
     * @param string $keyId
     * @return AmazonProductRequest
     * @access public
     */
    public function setAWSAccessKeyId($keyId)
    {        
        if (is_string($keyId) === false)
        {
            throw new Exception ('Invalid AWS Access Key parameter.');
        }
        $this->params['AWSAccessKeyId'] = $keyId;
        
        return $this;
    }
    
    /* Brand Parameter setter.
     * @param string $brand
     * @return AmazonProductRequest
     * @access public
     */
    public function setBrand($brand)
    {
        if (is_string($brand) === false)
        {
            throw new Exception('Invalid Brand parameter.');
        }
        
        $this->params['Brand'] = $brand;
        
        return $this;
    }
    
    /* Browse Node Parameter setter.
     * @param string $browseNode valid Amazon Browse Node integer string
     * @return AmazonProductRequest
     * @access public
     */
    public function setBrowseNode($browseNode)
    {
        if (is_string($browseNode) === false)
        {
            throw new Exception('Invalid Browse Node parameter.');
        }
        
        $this->params['BrowseNode'] = $browseNode;
        
        return $this;
    }
    
    /* Condition Parameter setter.
     * @param string $condition
     * @return AmazonProductRequest
     * @access public
     */
    public function setCondition($condition)
    {
        if (is_string($condition) === false)
        {
            throw new Exception('Invalid Condition parameter.');
        }
        
        $this->params['Condition'] = $condition;
        
        return $this;
    }
    
    /* Id Type Parameter setter.
     * @param string $idType
     * @return AmazonProductRequest
     * @access public
     */
    public function setIdType($idType)
    {
        if (is_string($idType) === false)
        {
            throw new Exception('Invalid Id Type parameter.');
        }
        
        $this->params['IdType'] = $idType;
        
        return $this;
    }
    
    /* Item Page Parameter setter.
     * @param integer $page between 1 and 10
     * @return AmazonProductRequest
     * @access public
     */
    public function setItemPage($page)
    {
        /* Amazon only returns pages 1 through 10. */
        if (is_int($page) === false || 
            $page < 1 || 
            $page > 10)
        {   
           throw new Exception('Invalid Item Page parameter,' . 
                               ' must be a number between 1 and 10.');
        }
        
        $this->params['ItemPage'] = $page;
        
        return $this;
    }
    
    /* Maximum Price Parameter setter.
     * @param integer $maxPrice in lowest denomination, 
     *     for U.S. this means cents, minimum value is 1 
     * @return AmazonProductRequest
     * @access public
     */
    public function setMaxPrice($maxPrice)
    {
        if (is_int($maxPrice) === false || 
            $maxPrice < 1)
        {
            throw new Exception('Invalid Maximum Price parameter.');
        }
        
        $this->params['MaximumPrice'] = $maxPrice;
        
        return $this;
    }
    
    /* Minimum Price Parameter setter.
     * @param integer $minPrice in lowest denomination, 
     *     for U.S. this means cents, minimum value is 1 
     * @return AmazonProductRequest
     * @access public
     */
    public function setMinPrice($minPrice)
    {
        if (is_int($minPrice) === false || 
            $minPrice < 1)
        {
            throw new Exception('Invalid Minimum Price parameter.');
        }
        
        $this->params['MinimumPrice'] = $minPrice;
        
        return $this;
    }
    
    /* Merchant Id Parameter setter.
     * @param string $id
     * @return AmazonProductRequest
     * @access public
     */
    public function setMerchantId($id)
    {    
        if (is_string($id) === false)
        {
            throw new Exception ('Invalid Merchant Id parameter.');
        }
        
        $this->params['MerchantId'] = $id;
        
        return $this;
    }
    
    /* Publisher Parameter setter.
     * @param string $publisher
     * @return AmazonProductRequest
     * @access public
     */
    public function setPublisher($publisher)
    {
        if (is_string($publisher) === false)
        {
            throw new Exception('Invalid Publisher parameter.');
        }
        
        $this->params['Publisher'] = $publisher;
        
        return $this;
    }
    
    /* Related Item Page Parameter setter.
     * @param integer $page minimum value is 1
     * @return AmazonProductRequest
     * @access public
     */
    public function setRelatedItemPage($page)
    {
        if (is_int($page) === false || 
            $page < 1)
        {   
            throw new Exception('Invalid Related Item Page parameter.');
        }
        
        $this->params['RelatedItemPage'] = $page;
        
        return $this;
    }
    
    /* Relationship Type Parameter setter.
     * @param string $relationshipType
     * @return AmazonProductRequest
     * @access public
     */
    public function setRelationshipType($relationshipType)
    {
        if(is_string($relationshipType) === false)
        {
            throw new Exception('Invalid Relationship Type parameter.');
        }
        
        $this->params['RelationshipType'] = $relationshipType;
        
        return $this;
    }
    
    /* Response Group Parameter setter.
     * @param string $responseGroup comma seperated
     * @return AmazonProductRequest
     * @access public
     */
    public function setResponseGroup($responseGroup)
    {
        if (is_string($responseGroup) === false)
        {
            throw new Exception('Invalid Response Group parameter.');
        }
        
        $this->params['ResponseGroup'] = $responseGroup;
        
        return $this;
    }
    
    /* Search Index Parameter setter.
     * @param string $searchIndex
     * @return AmazonProductRequest
     * @access public
     */    
    public function setSearchIndex($searchIndex)
    {
        if (is_string($searchIndex) === false)
        {
            throw new Exception('Invalid Search Index parameter.');
        }
        
        $this->params['SearchIndex'] = $searchIndex;
        
        return $this;
    }
    
    /* Sort Parameter setter.
     * @param string $sort
     * @return AmazonProductRequest
     * @access public
     */    
    public function setSort($sort)
    {
        if (is_string($sort) === false)
        {
            throw new Exception ('Invalid Sort parameter.');
        }
        
        $this->params['Sort'] = $sort;
        
        return $this;
    }
    
    /* Title Parameter setter.
     * @param string $title
     * @return AmazonProductRequest
     * @access public
     */    
    public function setTitle($title)
    {
        if (is_string($title) === false)
        {
            throw new Exception('Invalid Title parameter.');
        }
        
        $this->params['Title'] = $title;
        
        return $this;
    }    
    
    /* Version Parameter setter.
     * @param string $version as date in Y-m-d
     * @return AmazonProductRequest
     * @access public
     */    
    public function setVersion($version)
    {
        if ($this->validateDate($version) === false)
        {
            throw new Exception('Invalid Version parameter.');
        }
        
        $this->params['Version'] = $version;
        
        return $this;
    }
    
    /************************ 
     * powerStrings setters *
     ************************/
    
    /* After Year Power Search String setter.
     * @param int $afterYear
     * @return AmazonProductRequest
     * @access public
     */    
    public function setBookAfterYear($afterYear)
    {
        if (is_int($afterYear) === false || 
            $afterYear < 1)
        {
            throw new Exception('Invalid After Year parameter.');
        }
        
        $this->powerStrings['afterYear']['value'] = $afterYear;
        $this->powerStrings['afterYear']['prefix'] = 'pubdate:after ';
        
        return $this;
    }
    
    /* Before Year Power Search String setter.
     * @param int $beforeYear
     * @return AmazonProductRequest
     * @access public
     */    
    public function setBookBeforeYear($beforeYear)
    {
        if (is_int($beforeYear) === false || 
            $beforeYear < 1)
        {
            throw new Exception('Invalid Before Year parameter.');
        }
        
        $this->powerStrings['beforeYear']['value'] = $beforeYear;
        $this->powerStrings['beforeYear']['prefix'] = 'pubdate:before ';
        
        return $this;
    }
    
    /* During Year Power Search String setter.
     * @param int $duringYear
     * @return AmazonProductRequest
     * @access public
     */    
    public function setBookDuringYear($duringYear)
    {
        if (is_int($duringYear) === false || 
            $duringYear < 1)
        {
            throw new Exception('Invalid During Year parameter.');
        }
        
        $this->powerStrings['duringYear']['value'] = $duringYear;
        $this->powerStrings['duringYear']['prefix'] = 'pubdate:during ';
        
        return $this;
    }
    
    /* Language Power Search String setter.
     * @param string $language
     * @return AmazonProductRequest
     * @access public
     */    
    public function setBookLanguage($language)
    {
        if (is_string($language) === false)
        {
            throw new Exception('Invalid Language parameter.');
        }
        
        $this->powerStrings['language']['value'] = $language;
        $this->powerStrings['language']['prefix'] = 'language:';
        
        return $this;
    }
    
    /* Subject Power Search String setter.
     * @param string $subject
     * @return AmazonProductRequest
     * @access public
     */    
    public function setBookSubject($subject)
    {
        if (is_string($subject) === false)
        {
            throw new Exception('Invalid Subject parameter.');
        }
        
        $this->powerStrings['subject']['value'] = $subject;
        $this->powerStrings['subject']['prefix'] = 'subject:';
        
        return $this;
    }
    
    /********************* 
     * operation methods *
     *********************/
    
    /* Performs BrowseNodeLookup Operation.
     * @param string $browseNode
     * @return mixed string|SimpleXMLElement|array
     * @access public
     */    
    public function browseNodeLookup($browseNode)
    {
        $finalParams['Operation'] = 'BrowseNodeLookup';
        $finalParams = array_merge($this->params, $finalParams);
        
        if (is_string($browseNode) === false)
        {
            throw new Exception('Invalid Browse Node Parameter passed' . 
                                ' to browseNodeLookup.');
        }
        
        $finalParams['BrowseNodeId'] = $browseNode;
        
        $response = $this->execRequest($finalParams);
        
        if ($response === false)
        {
            throw new Exception($this->errorMsg);
        }
        
        return $response;
    }
    
    /* Performs ItemLookup Operation.
     * @param string $itemId 1 - 10 comma seperated itemIds
     * @return mixed string|SimpleXMLElement|array
     * @access public
     */
    public function itemLookup($itemId)
    {
        $finalParams['Operation'] = 'ItemLookup';
        $finalParams = array_merge($this->params, $finalParams);
        
        if (is_string($itemId) === false)
        {
            throw new Exception('Invalid itemId Parameter passed' . 
                                ' to itemLookup.');
        }
        
        $finalParams['ItemId'] = $itemId;
        
        $response = $this->execRequest($finalParams);
        
        if ($response === false)
        {
            throw new Exception($this->errorMsg);
        }
        
        return $response;
    }
    
    /* Performs ItemSearch Operation.
     * @param string $keywords optional space seperated keywords
     * @return mixed string|SimpleXMLElement|array
     * @access public
     */
    public function itemSearch($keywords = null)
    {    
        $finalParams['Operation'] = 'ItemSearch';
        $finalParams = array_merge($this->params, $finalParams);
        
        if ($keywords !== null)
        {
            if (is_string($keywords) === false)
            {
                throw new Exception('Invalid Keywords Parameter passed' . 
                                    ' to itemSearch.');
            }
            
            $finalParams['Keywords'] = $keywords;
        }
        
        if (empty($this->powerStrings) === false)
        {   
            $finalParams['Power'] = $this->buildPowerSearch($this->powerStrings);
        }
    
        $response = $this->execRequest($finalParams);
        
        if ($response === false)
        {
            throw new Exception($this->errorMsg);
        }
        
        return $response;
    }
    
    /* Resets member arrays $this->params and $this->powerStrings,
     * does not reset $this->config.
     * @return AmazonProductRequest
     * @access public
     */
    public function resetParams()
    {
        /* Reset PowerStrings. */
        $this->powerStrings = array();
        
        /* Reset Params. */
        $newParams = array();
        $newParams['AWSAccessKeyId'] = $this->params['AWSAccessKeyId'];
        $newParams['AssociateTag'] = $this->params['AssociateTag'];
        $newParams['Version'] = $this->params['Version'];
         
        $this->params = $newParams;
        
        return $this;
    }
     
     /*******************
      * private methods *
      *******************/
    
    /* Builds the value for Power parameter for use with the itemSearch 
     * operation. 
     * @param array $powerStrings each element must contain the
     * elements 'prefix' and 'value' for concatenation.
     * @return string|boolean - false upon failure
     * @access private
     */     
    private function buildPowerSearch($powerStrings)
    {        
        $powerSearchParam = array();
        
        foreach ($powerStrings as $powerString)
        {
            if (isset($powerString['prefix']) === false || 
                isset($powerString['value']) === false)
                {
                    return false;
                }
                
            $powerSearchParam[] = $powerString['prefix'] . 
                                  $powerString['value'];
        }
    
        return implode(' and ', $powerSearchParam);
    }
    
    /* Builds the http request url using the request parameters that have been
     * set. Does not add the protocol prefix to the url. 
     * @param array $requestParams.
     * @return string
     * @access private
     */ 
    private function buildRequest($requestParams)    
    {      
        $requestParams['Service'] = 'AWSECommerceService';
        $requestParams['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z");
    
        $host = 'webservices.amazon.' . $this->config['location'];
        $path = '/onca/xml';
        $method = 'GET';
        
        /* Sort keys in byte order, similiar to Alphabetical except 
         * lowercases are a higher value than uppercases as per byte value. */
        ksort($requestParams);
     
        /* Arrange and encode parameters into a parameter string. */
        $paramsString = http_build_query($requestParams, null, '&', 
                                         PHP_QUERY_RFC3986);
        
        /* Create the string to sign. */
        $stringToSign = implode("\n", array($method, $host, $path, 
                                $paramsString));

        /* Hash the signing string using the private key and encode it. */
        $signature = hash_hmac("sha256", $stringToSign, 
                               $this->config['secretKey'], true);
        $signature = rawurlencode(base64_encode($signature));
        
        /* Build the request and append the signature. */
        $request = $host . $path . "?". $paramsString . "&Signature=" . 
                   $signature;    
        
        return $request;
    }     
    
    /* Checks the response from Amazon for curl request failure, 
     * invalid xml response, or errors returned by Amazon.
     * If an error is encountered, $this->errorMsg will be populated and 
     * the method will return false. If the above checks pass, 
     * it will return true.
     * @param string $response xml or malformed xml response
     * @param $ch curl handle
     * @return bool
     * @access private
     */ 
    private function checkResponse($response, $ch)
    {    
        /* Populate errorMsg with last curl error message if curl GET fails. */
        if ($response === false)
        {
            $this->errorMsg = curl_error($ch);
            return false;
        }
        
        /* Temporarily suppress libxml errors so that we can handle them. */
        @$xml = simplexml_load_string($response);
        
        /* Populate errorMsg with appropriate message if invalid xml response. */  
        if ($xml === false)
        {
            $this->errorMsg = implode("\n", libxml_get_errors());
            return false;
        }    
        
        /* Extract error message(s) from xml. */    
        $namespaces = $xml->getNamespaces();
        $xml->registerXPathNamespace("ns", array_shift($namespaces));
        $error = $xml->xpath("//ns:Error/ns:Message");
        
        /* Set errorMsg to a string of the error mesage(s) if they exist. */
        if (isset($error[0]))
        {
            $this->errorMsg = implode("\n", $error);
            return false;
        }
        
        return true;
    }
    
    /* Executes request using cURL.
     * For SSL, a CA Root Certificates file is required. The path is
     * defined by CERTPATH.
     * @param array $requestParams appended with operation specific
     *     fields
     * @return mixed string|SimpleXMLElement|array|boolean - false if 
     *     invalid response
     * @access private
     */ 
    private function execRequest($requestParams)
    {    
        if ($this->config['delay'] === true)
        {
            sleep(1);
        }
        
        $ch = curl_init();                
        
        if ($this->config['ssl'] === true)
        {
            $protocol = 'https';
            curl_setopt ($ch, CURLOPT_CAINFO, $this->CERTPATH);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        else
        {
            $protocol = 'http';
        }
        
        $request = $protocol . '://' . $this->buildRequest($requestParams);
        
        /* Use GET request. */
        curl_setopt($ch, CURLOPT_URL,$request);
        /* Return response as a string value. */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $response = curl_exec($ch);
        $check = $this->checkResponse($response, $ch);
        
        return $check ? $this->formatResponse($response) : false;
    }
    
    /* Formats the response according to the setting in $this->responseFormat.
     * @param string $response validated xml response
     * @return mixed string|SimpleXMLElement|array
     * @access private
     */  
    private function formatResponse($response)
    {    
        switch($this->config['responseFormat'])
        {
            case 'simplexml':
                return simplexml_load_string($response);
            break;
            
            case 'array':
                return $this->xmlToArray($response);
            break;
            
            default:
                return $response;
        }
    }
    
    /* Validates a Date string. original function submitted to 
     * www.php.net/manual/en/function.checkdate.php
     * by glavic at gmail dot com.
     * @param string $dateToTest
     * @param string $format optional
     * @return boolean
     * @access private
     */  
    private function validateDate($dateToTest, $format = 'Y-m-d')
    {
        /* Suppress warnings requiring a timezone, 
         * timezone is irrelevant to date format checking and UTC is specified 
         * by default anyway. */
        @$date = DateTime::createFromFormat($format, $dateToTest);

        /* If $date was not created properly, it would be impossible to 
         * date->format(). */
        return $date && $date->format($format) == $dateToTest;
    }
    
    /* Converts an xml string to an associative array.
     * @param string $xml validated xml
     * @return array
     * @access private
     */ 
    private function xmlToArray($xml)
    {
        $xml = simplexml_load_string($xml);
        /* Xml has to be encoded in json before it can be decoded 
           as an associative array. */
        $json = json_encode($xml);
        $array = json_decode($json, true);
        
        return $array;
    }
}
