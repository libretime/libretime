<?php

class Application_Form_AdvancedSearchRow extends Zend_Form_SubForm
{
    public function init()
    {
        $this->addElement(
            'select',
            'metadata',
            array(
                'required' => true,
                'multiOptions' => array(
					"dc:title" => "Title",
                    "dc:format" => "Format",
					"dc:creator" => "Artist/Creator",
					"dc:source" => "Album",
					"ls:bitrate" => "Bitrate",
				  	"ls:samplerate" => "Samplerate",
					"dcterms:extent" => "Length",
					"dc:description" => "Comments",
					"dc:type" => "Genre",
					"ls:channels" => "channels",
					"ls:year" => "Year",
					"ls:track_num" => "track_number",
					"ls:mood" => "mood",
					"ls:bpm" => "BPM",
					"ls:rating" => "rating",
					"ls:encoded_by" => "encoded_by",
					"dc:publisher" => "label",
					"ls:composer" => "Composer",
					"ls:encoder" => "Encoder",
					"ls:lyrics" => "lyrics",
					"ls:orchestra" => "orchestra",
					"ls:conductor" => "conductor",
					"ls:lyricist" => "lyricist",
					"ls:originallyricist" => "original_lyricist",
					"ls:isrcnumber" => "isrc_number",	
					"dc:language" => "Language",
                ), 
            )
        );
		$this->getElement('metadata')->removeDecorator('Label')->removeDecorator('HtmlTag');

		$this->addElement(
            'select',
            'match',
            array(
                'required' => true,
                'multiOptions' => array(
					"0" => "partial",
                    "1" => "=",
					"2" => "<",
					"3" => "<=",
					"4" => ">",
				  	"5" => ">=",
					"6" => "!=",
                ), 
            )
        );
		$this->getElement('match')->removeDecorator('Label')->removeDecorator('HtmlTag');

		$this->addElement('text', 'search', array(
		  'required' => true,
		));
		$this->getElement('search')->removeDecorator('Label')->removeDecorator('HtmlTag');
    }


}

