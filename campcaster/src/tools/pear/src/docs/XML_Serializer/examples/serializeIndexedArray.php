<?PHP
/**
 * This example demonstrates the use of
 * mode => simplexml
 *
 * It can be used to serialize an indexed array
 * like ext/simplexml does, by using the name
 * of the parent tag, while omitting this tag.
 *
 * @author Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);
    
    require_once 'XML/Serializer.php';

    $options = array(
                        "indent"         => "    ",
                        "linebreak"      => "\n",
						"rootName"       => "rdf:RDF",
                        "rootAttributes" => array("version" => "0.91"),
                        "mode"           => "simplexml"
                    );
    
    $serializer = new XML_Serializer($options);

    
    $rdf    =   array(
						"channel" => array(
											"title" => "Example RDF channel",
											"link"  => "http://www.php-tools.de",
											"image"	=>	array(
																"title"	=> "Example image",
																"url"	=>	"http://www.php-tools.de/image.gif",
																"link"	=>	"http://www.php-tools.de"
															),
                                            "item"   =>  array(
                    											array(
                    												"title"	=> "Example item",
                    												"link"	=> "http://example.com"
                    											),
                    											array(
                    												"title"	=> "Another item",
                    												"link"	=> "http://example.com"
                    											),
                    											array(
                    												"title"	=> "I think you get it...",
                    												"link"	=> "http://example.com"
                    											)
                                                              )
										)
                    );
    
    $result = $serializer->serialize($rdf);
    
    if( $result === true ) {
        echo    "<pre>";
        echo    htmlentities($serializer->getSerializedData());
        echo    "</pre>";
    }
?>