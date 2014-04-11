# AmazonProductRequest 

A lightweight and easily extendable class for making REST requests to Amazon's Product Advertising API.

## Overview

AmazonProductRequest is intended to make requests to the Amazon Product Advertising API easily incorporated into any PHP application. Currently there are three operations supported, BrowsenodeLookup, ItemLookup, and ItemSearch. There are also multiple parameters that can set for these operations and multiple configuration options available.


## Requirements
AmazonProductRequest requires PHP Version >= 5.4.0.
PHP's cURL extension is also required in order to make the REST requests.

## Initialization
You probably want to initialize AmazonProductRequest by creating a new instance.
The required parameters are your Amazon Access Key, Secret Access Key, Associate Tag, and Version of the API.

```php
$request = new AmazonProductRequest($ACCESS_KEY_ID, $ASSOCIATE_TAG, 
                                            $SECRET_ACCESS_KEY, $VERSION);
```

Additionally, you can pass your desired location into the last argument of the constructor.

```php
$request = new AmazonProductRequest($ACCESS_KEY_ID, $ASSOCIATE_TAG, 
                                            $SECRET_ACCESS_KEY, $VERSION, 'co.uk');
```

The default location is 'com'. This can be changed at any time using the appropriate configuration setter.

## Configuration

The following methods are available to configure the request:

### setConfigDelay($delay)

Amazon requires a 1 second delay between requests, so if you want to make more than one request sequentially, you'll have to turn on the delay. 

### setConfigLocation($location)

This will change the location (i.e. co.uk, fr, etc.). The member variable $LOCATIONS contains a list of valid values. The default value is 'com'.

### setConfigResponseFormat($format)

The response formats that are currently supported are string, simpleXml, and array (not case sensitive). The default response format is simpleXml.

### setConfigSecretKey($secretKey)

The secret key is not actually sent as a parameter, rather it is used to create the signature and therefore it is stored as a configuration variable.

### setConfigSsl($ssl)

AmazonProductRequest supports requests over SSL, however a file containing the Certificate Authority Root Certificates must be present and references by the member variable $CERTPATH. Valid values are true and false.

All configuration options can be chained like this:

```php
<?php
$request->setConfigSsl(true)->setConfigResponseFormat('string');
?>
```

## Parameters

Different parameters can be specified that can be used depending on the operation. A full list of supported parameters and their appropriate operations are listed in the Operations section.
The following parameters setters can always be used regardless of the operation:
* setAssociateTag($tag)
* setAWSAccessKeyId($keyId)
* setVersion($version)

## Operations
### BrowseNodeLookup

The BrowseNodeLookup operation must have the BrowseNodeId passed as the argument. 
The only parameter setter that can be used for this operation is:
* setResponseGroup($responseGroup)

Example:

```php
<?php
$response = $request->browseNodeLookup('172282');
?>
```

```php
<?php
$response = $request->setResponseGroup('MostWishedFor')->browseNodeLookup('172282');
?>
```

For more information about the BrowseNodeLookup operation see:  
[Amazon's Documentation on BrowseNodeLookup](http://docs.aws.amazon.com/AWSECommerceService/latest/DG/BrowseNodeLookup.html)

### ItemLookup

The ItemLookup operation must have the ItemID passed as the argument. 
The parameter setters that are currently supported for this operation are:
* setCondition($condition)
* setIdType($idType)
* setMerchantId($id)
* setRelatedItemPage($page)
* setRelationshipType($relationshipType)
* setResponseGroup($responseGroup)
* setSearchIndex($searchIndex)

Example:

```php
<?php
$response = $request->itemLookup('B004V4IPCQ');
?>
```

```php
<?php
$response = $request->setResponseGroup('Large,ItemAttributes')
            ->setSearchIndex('Electronics')
            ->setIdType('SKU')
            ->itemLookup('70A29000NA');
?>
```

For more information about the ItemLookup operation see:  
[Amazon's Documentation on ItemLookup](http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html)

### ItemSearch

The ItemSearch operation can optionally include a keywords argument passed to the method.
If Search Index is not set, an empty list will be returned by Amazon. Also, using the ‘All' Search Index limits the usage of parameters to keywords only.
The parameter setters that are currently supported for this operation are:
* setAuthor($author)
* setAvailability($availability)
* setBrand($brand)
* setBrowseNode($browseNode)
* setCondition($condition)
* setItemPage($page)
* setMaxPrice($maxPrice)
* setMerchantId($id)
* setMinPrice($minPrice)
* setPublisher($publisher)
* setRelatedItemPage($page)
* setRelationshipType($relationshipType)
* setResponseGroup($responseGroup)
* setSearchIndex($searchIndex)
* setSort($sort)
* setTitle($title)

Example:
```php
<?php
$response = $request->setResponseGroup('Medium')
                    ->setItemPage(2)
                    ->setSearchIndex('All')
                    ->itemSearch('dog cat');
?>
```

```php
<?php
$response = $request->setResponseGroup('Large')
                    ->setItemPage(1)
                    ->setSearchIndex('Shoes')
                    ->setBrand('nike')
                    ->itemSearch();
?>
```
Additionally, when Search Index is set to ‘Books', Power Search parameters can be used.

#### Power Search

Amazon offers a powerful conditional search query that can be used when searching for books via itemSearch. Using a Search Index of 'Books' is necessary when using these parameters. AmazonProductRequest supports using some of the Power Search fields because they are not possible outside of the Power Search. For each Power Search Parameter used, they will be added to the final Power parameter using the 'and' keyword. Other keywords such as 'or' are not supported at this time.
Power Search Strings are maintained in a separate variable called $powerStrings, which are used to build the Power parameter. The setters for these parameters all have the prefix 'Book'.
This is a list of supported Power Search setters:

* setBookAfterYear($afterYear)
* setBookBeforeYear($beforeYear)
* setBookDuringYear($duringYear)
* setBookLanguage($language)
* setBookSubject($subject)

Example:
```php
<?php
$response = $request->setSearchIndex('Books')
    ->setAuthor('John Grisham')
    ->setBookBeforeYear(1991)
    ->setBookAfterYear(1969)
    ->setResponseGroup('ItemAttributes')
    ->setSort('titlerank')
    ->itemSearch();
?>
```

```php
<?php
$response = $request->setSearchIndex('Books')
    ->setBookDuringYear(1995)
    ->setBookSubject('fiction')
    ->setResponseGroup('Large,ItemAttributes')
    ->itemSearch('magic');
?>
```

For more information about the ItemSearch operation see:  
[Amazon's Documentation on ItemSearch](http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html)

## ResetParams

Parameters are maintained between requests. However, you can clear them by calling resetParams. This is more convenient than initializing an instance of AmazonProductRequest everytime you want to clear your parameters. Configuration settings will not be cleared and neither will the following three parameters: AWSAccessKeyId, AssociateTag, Version.

## Looping

Remember, Amazon requires a 1 second delay between requests. When looping, the delay must be turned on following the first request.

## Exception Handling
Exceptions are thrown when invalid values have been passed to a setter or the constructor. Additionally, execRequest will fail if the cURL request is not successful, if an invalid xml response is received, or if the Amazon API responds with an error (or multiple errors). execRequest will then return false and the member variable errorMsg will populate. An exception is thrown by the operation method when an execRequest fails. The user of this class can then decide how to handle exceptions by creating try and catch blocks.

## Extending the Class

### Adding parameters
All that is required is the addition of an accessor and mutator for adding a parameter to the library. Validation of the setter argument should be done within the method.

Example:

```php
<?php
public function get<Param>()
{
    return isset($this->params[<Param>]) ? $this->params[<Param>] : null;
}
?>	
```

```php
<?php
public function set<Param>($param)
{
    if (<Param Validation Condition> === false)
    {
        throw new Exception('Invalid <Param> parameter.');
    }
		
    $this->params[<Param>] = $param;
		
    return $this;
}
?>
```

### Adding an operation 
All that is required is the addition of the new operation method. It must set the Operation parameter and any argument specific to the operation, appending these with the other parameters that have been set in the member variable $params into a new parameter array. Error checking specific to the operation should also be done within this method. 

Example: 

```php
<?php
$finalParams['Operation'] = <Operation>;
$finalParams = array_merge($this->params, $finalParams);

if (<Operation Argument> !== null)
{
    if (<Param Validation Condition> === false)
    {
        throw new Exception('Invalid <Param> Parameter passed to <Operation>.');
    }
		    
    $finalParams[<Operation Argument>] = <Operation Argument>;
}
?>
```

After this is done, the operation method should call execRequest, passing these final parameters as an associative array. You should also then check to see if execRequest returned false. Afterward, return the response, which will be formatted properly upon success.

Example:

```php
<?php
$response = $this->execRequest($finalParams);
		
if ($response === false)
{
    throw new Exception($this->errorMsg);
}
		
return $response;
?>
```

