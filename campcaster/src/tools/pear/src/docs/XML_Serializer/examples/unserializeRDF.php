<?PHP
/**
 * This example shows how to create any object
 * from an XML document. In this case we get
 * some aggregated objects for channel and items
 * from an RSS feed.
 *
 * @author  Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);

    require_once '../Unserializer.php';

   /**
    * class for the RDF docuemnt
    *
    *
    */
    class rdfDocument
    {
        var $channel;
        var $item;

        function getItems($amount)
        {
            return array_splice($this->item,0,$amount);
        }
    }


   /**
    * class that is used for a channel in the RSS file
    *
    * you could implement whatever you like in this class,
    * properties will be set from the XML document
    */
    class channel
    {
        function getTitle()
        {
            return  $this->title;
        }
    }
    
   /**
    * class that is used for an item in the RSS file
    *
    * you could implement whatever you like in this class,
    * properties will be set from the XML document
    */
    class item
    {
        function getTitle()
        {
            return  $this->title;
        }
    }


    $options = array(
                     "complexType" => "object",
                     "tagMap"      => array(
                                                "rdf:RDF"   => "rdfDocument",   // this is used to specify a classname for the root tag
                                            )
                    );
    
    //  be careful to always use the ampersand in front of the new operator 
    $unserializer = &new XML_Unserializer($options);

    $status = $unserializer->unserialize("http://pear.php.net/feeds/latest.rss",true);    

    if (PEAR::isError($status)) {
        echo    "Error: " . $status->getMessage();
    } else {
        $rss = $unserializer->getUnserializedData();

        echo "This has been returned by XML_Unserializer:<br>";
        
        echo "<pre>";
        print_r( $rss );
        echo "</pre>";

        echo "<br><br>Root Tagname: ".$unserializer->getRootName()."<br>";
        
        echo "Title of the channel: ".$rss->channel->getTitle()."<br>";

        $items = $rss->getItems(3);
        echo "<br>Titles of the last three releases:<br>";
        foreach ($items as $item) {
            echo "Title : ".$item->getTitle()."<br>";
        }
    }
?>