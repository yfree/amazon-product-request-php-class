<?php
require_once('AmazonProductRequest.php');

$ACCESS_KEY_ID = 'SET ME';
$SECRET_ACCESS_KEY = 'SET ME';
$ASSOCIATE_TAG = 'SET ME';
$VERSION =  '2011-08-01';
       
/* In these examples we are going to explore some of the features 
 * supported by the AmazonProductRequest class. 
 */
try 
{
    $request = new AmazonProductRequest($ACCESS_KEY_ID, $ASSOCIATE_TAG, 
                                        $SECRET_ACCESS_KEY, $VERSION);

    /* Set our response to array format. */
    $request->setConfigResponseFormat('array');

    /* BrowseNodeLookup.
     * Let's look up some information about the browse node 172282 
     * which currently represents Electronics in the US.
     * The default Response Group BrowseNodeInfo will be automatically
     * used.
     */
    $response = $request->browseNodeLookup('172282');
    //print_r($response);

    /* Turn on the delay because Amazon requires a delay of 1 
     * second between requests. 
     */
    $request->setConfigDelay(true);

    /* Next, let's do an ItemLookup. We'll use the Amazon ASIN B004V4IPCQ
     * which represents One Flew Over the Cuckoo's Nest, 
     * Penguin Classics.
     * The default Response Group 'Small' will be used.
     */
    $response = $request->itemLookup('B004V4IPCQ');
    //print_r($response);
    
    /* Or an itemLookup for a specific SKU which represents a Lenovo NAS 
     * device in Electronics. We'll also use the Response Groups 
     * Large and ItemAttributes. */
    $response = $request->setResponseGroup('Large,ItemAttributes')
        ->setSearchIndex('Electronics')
        ->setIdType('SKU')
        ->itemLookup('70A29000NA');
    //print_r($response);

    /* Parameters are maintained request to request, so you don't have to 
     * keep setting them. However, parameters that are specified as the 
     * operation argument are not kept between requests. 
     * You can clear your parameters by using resetParams. 
     * This will not clear your credentials or the request options that 
     * are not sent as parameters such as SSL or responseFormat. */
    $request->resetParams();

    /* Now let's do an itemSearch for C++ Books published by Pearson.
     * The default Response Group 'Small' will be used. */
    $response = $request->setSearchIndex('Books')
        ->setPublisher('pearson')
        ->itemSearch('C++');
    //print_r($response);

    /* Or itemSearch all search indexes using the keywords 'dog cat'.
     * We'll also set Response Group to Medium and 
     * retrieve page 2 of the results. */
    $response = $request->resetParams()
        ->setResponseGroup('Medium')
        ->setItemPage(2)
        ->setSearchIndex('All')
        ->itemSearch('dog cat');
    //print_r($response);

    /* Let's do the same search but only returning available items. */
    $response = $request->setAvailability(true)
        ->itemSearch('dog cat');
    //print_r($response);

    /* Let's look for available Nike Shoes and let's do the request over SSL. 
     * This time we will want to return page 1 again and use the 
     * Response Group Large. We have already set Availability to true.*/
    $response = $request->setResponseGroup('Large')
        ->setItemPage(1)
        ->setConfigSsl(true)
        ->setSearchIndex('Shoes')
        ->setBrand('nike')
        ->itemSearch();
    //print_r($response);

    /* Ok, let's drop ssl and reset the parameters. */
    $request->setConfigSsl(false)->resetParams();

    /* Now let's check out some of the Power Search parameters. 
     * These are only useful when Search Index is Books.
     * Parameters that are Power Search Parameters are marked in their 
     * setters with the prefix 'Book'.
     * Let's return books by John Grisham that were published after 1969 
     * and before 1991. We'll use the ItemAttributes Response Group. 
     * Let's also sort the results Alphabetically. */
    $response = $request->setSearchIndex('Books')
        ->setAuthor('John Grisham')
        ->setBookBeforeYear(1991)
        ->setBookAfterYear(1979)
        ->setResponseGroup('ItemAttributes')
        ->setSort('titlerank')
        ->itemSearch();
    //print_r($response);

    /* Finally, let's do a search for Books that are fiction 
     * and published during 1995 containing the keyword 'magic'.
     * We'll use the Response Groups Large and ItemAttributes.
     */
    $response = $request->resetParams()
        ->setSearchIndex('Books')
        ->setBookDuringYear(1995)
        ->setBookSubject('fiction')
        ->setResponseGroup('Large,ItemAttributes')
        ->itemSearch('magic');
    //print_r($response);
}
catch (Exception $e)
{
    print $e->getMessage();
}
