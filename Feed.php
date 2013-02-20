<?php
namespace FeedWriter;

use \DateTime;

/* 
 * Copyright (C) 2008 Anis uddin Ahmad <anisniit@gmail.com>
 * Copyright (C) 2010-2012 Michael Bemmerl <mail@mx-server.de>
 *
 * This file is part of the "Universal Feed Writer" project.
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
 */

// RSS 0.90  Officially obsoleted by 1.0
// RSS 0.91, 0.92, 0.93 and 0.94  Officially obsoleted by 2.0
// So, define constants for RSS 1.0, RSS 2.0 and ATOM

/**
 * Universal Feed Writer class
 *
 * Generate RSS 1.0, RSS2.0 and ATOM Feeds
 *
 * @package     UniversalFeedWriter
 * @author      Anis uddin Ahmad <anisniit@gmail.com>
 * @link        http://www.ajaxray.com/projects/rss
 */
abstract class Feed
{
	const RSS1 = 'RSS 1.0';
	const RSS2 = 'RSS 2.0';
	const ATOM = 'ATOM';

	private $channels      = array();  // Collection of channel elements
	private $items         = array();  // Collection of items as object of \FeedWriter\Item class.
	private $data          = array();  // Store some other version wise data
	private $CDATAEncoding = array();  // The tag names which have to encoded as CDATA

	private $version   = null;
	
	/**
	* Constructor
	*
	* @param    constant    the version constant (RSS1/RSS2/ATOM).
	*/
	protected function __construct($version = FeedWriter::RSS2)
	{
		$this->version = $version;

		// Setting default value for essential channel elements
		$this->channels['title'] = $version . ' Feed';
		$this->channels['link']  = 'http://www.ajaxray.com/blog';

		//Tag names to encode in CDATA
		$this->CDATAEncoding = array('description', 'content:encoded', 'summary');
	}

	// Start # public functions ---------------------------------------------
	
	/**
	* Set a channel element
	* @access   public
	* @param    string  name of the channel tag
	* @param    string  content of the channel tag
	* @return   void
	*/
	public function setChannelElement($elementName, $content)
	{
		$this->channels[$elementName] = $content;
	}
	
	/**
	* Set multiple channel elements from an array. Array elements
	* should be 'channelName' => 'channelContent' format.
	*
	* @access   public
	* @param    array   array of channels
	* @return   void
	*/
	public function setChannelElementsFromArray($elementArray)
	{
		if (!is_array($elementArray))
			return;

		foreach ($elementArray as $elementName => $content)
		{
			$this->setChannelElement($elementName, $content);
		}
	}

	/**
	* Get the appropriate MIME type string for the current feed.
	*
	* @access public
	* @return string
	*/
	public function getMIMEType()
	{
		switch($this->version)
		{
			case Feed::RSS2 : $mimeType = "application/rss+xml";
				break;
			case Feed::RSS1 : $mimeType = "application/rdf+xml";
				break;
			case Feed::ATOM : $mimeType = "application/atom+xml";
				break;
			default : $mimeType = "text/xml";
		}

		return $mimeType;
	}
	
	/**
	* Print the actual RSS/ATOM file
	*
	* Sets a Content-Type header and echoes the contents of the feed.
	* Should only be used in situations where direct output is desired;
	* if you need to pass a string around, use generateFeed() instead.
	*
	* @access   public
	* @param    bool  FALSE if the specific feed media type should be sent.
	* @return   void
	*/
	public function printFeed($useGenericContentType = false)
	{
		$contentType = "text/xml";

		if (!$useGenericContentType)
		{
			$contentType = $this->getMIMEType();
		}

		header("Content-Type: " . $contentType);
		echo $this->generateFeed();
	}

	/**
	* Generate the feed.
	*
	* @access public
	* @param  bool
	* @return string
	*/
	public function generateFeed()
	{
		return $this->makeHeader()
			. $this->makeChannels()
			. $this->makeItems()
			. $this->makeFooter();
	}
	
	/**
	* Create a new Item.
	* 
	* @access   public
	* @return   object  instance of Item class
	*/
	public function createNewItem()
	{
		$Item = new Item($this->version);
		return $Item;
	}
	
	/**
	* Add a FeedItem to the main class
	* 
	* @access   public
	* @param    object  instance of Item class
	* @return   void
	*/
	public function addItem(Item $feedItem)
	{
		if ($feedItem->getVersion() != $this->version)
			die('Feed type mismatch: This instance can handle ' . $this->version . ' feeds only, but item with type ' . $feedItem->getVersion() . ' given.');

		$this->items[] = $feedItem;
	}
	
	
	// Wrapper functions -------------------------------------------------------------------
	
	/**
	* Set the 'title' channel element
	* 
	* @access   public
	* @param    string  value of 'title' channel tag
	* @return   void
	*/
	public function setTitle($title)
	{
		$this->setChannelElement('title', $title);
	}

	/**
	* Set the 'updated' channel element of an ATOM feed
	* 
	* @access   public
	* @param    string  value of 'updated' channel tag
	* @return   void
	*/
	public function setDate($date)
	{
		if ($this->version != Feed::ATOM)
			return;

		if ($date instanceof DateTime)
			$date = $date->format(DateTime::ATOM);
		else if(is_numeric($date))
			$date = date(\DATE_ATOM, $date);
		else
			$date = date(\DATE_ATOM, strtotime($date));

		$this->setChannelElement('updated', $date);
	}
	
	/**
	* Set the 'description' channel element
	*
	* @access   public
	* @param    string  value of 'description' channel tag
	* @return   void
	*/
	public function setDescription($description)
	{
		if ($this->version != Feed::ATOM)
			$this->setChannelElement('description', $description);
	}
	
	/**
	* Set the 'link' channel element
	* 
	* @access   public
	* @param    string  value of 'link' channel tag
	* @return   void
	*/
	public function setLink($link)
	{
		$this->setChannelElement('link', $link);
	}
	
	/**
	* Set the 'image' channel element
	* 
	* @access   public
	* @param    string  title of image
	* @param    string  link url of the image
	* @param    string  path url of the image
	* @return   void
	*/
	public function setImage($title, $link, $url)
	{
		$this->setChannelElement('image', array('title'=>$title, 'link'=>$link, 'url'=>$url));
	}
	
	/**
	* Set the 'about' channel element. Only for RSS 1.0
	* 
	* @access   public
	* @param    string  value of 'about' channel tag
	* @return   void
	*/
	public function setChannelAbout($url)
	{
		$this->data['ChannelAbout'] = $url;
	}
	
	/**
	* Generates an UUID
	* @author     Anis uddin Ahmad <admin@ajaxray.com>
	* @param      string  an optional prefix
	* @return     string  the formated uuid
	*/
	public static function uuid($key = null, $prefix = '')
	{
		$key = ($key == null)? uniqid(rand()) : $key;
		$chars = md5($key);
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);

		return $prefix . $uuid;
	}
	// End # public functions ----------------------------------------------
	
	// Start # private functions ----------------------------------------------
	
	/**
	* Returns the xml and rss namespace
	* 
	* @access   private
	* @return   void
	*/
	private function makeHeader()
	{
		$out  = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
		
		if($this->version == Feed::RSS2)
		{
			$out .= '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/">';
		}
		elseif($this->version == Feed::RSS1)
		{
			$out .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/" xmlns:dc="http://purl.org/dc/elements/1.1/">';
		}
		else if($this->version == Feed::ATOM)
		{
			$out .= '<feed xmlns="http://www.w3.org/2005/Atom">';
		}

		$out .= PHP_EOL;

		return $out;
	}
	
	/**
	* Closes the open tags at the end of file
	* 
	* @access   private
	* @return   void
	*/
	private function makeFooter()
	{
		if($this->version == Feed::RSS2)
		{
			return '</channel>' . PHP_EOL . '</rss>';
		}
		elseif($this->version == Feed::RSS1)
		{
			return '</rdf:RDF>';
		}
		else if($this->version == Feed::ATOM)
		{
			return '</feed>';
		}
	}

	/**
	* Creates a single node as xml format
	*
	* @access   private
	* @param    string  name of the tag
	* @param    mixed   tag value as string or array of nested tags in 'tagName' => 'tagValue' format
	* @param    array   Attributes(if any) in 'attrName' => 'attrValue' format
	* @return   string  formatted xml tag
	*/
	private function makeNode($tagName, $tagContent, $attributes = null)
	{
		$nodeText = '';
		$attrText = '';

		if(is_array($attributes) && count($attributes) > 0)
		{
			foreach ($attributes as $key => $value)
			{
				$value = htmlspecialchars($value);
				$attrText .= " $key=\"$value\"";
			}
		}
		
		if(is_array($tagContent) && $this->version == Feed::RSS1)
		{
			$attrText = ' rdf:parseType="Resource"';
		}
		
		$attrText .= (in_array($tagName, $this->CDATAEncoding) && $this->version == Feed::ATOM) ? ' type="html"' : '';
		$nodeText .= "<{$tagName}{$attrText}>";
		$nodeText .= (in_array($tagName, $this->CDATAEncoding)) ? '<![CDATA[' : '';
		
		if(is_array($tagContent))
		{
			foreach ($tagContent as $key => $value)
			{
				$nodeText .= $this->makeNode($key, $value);
			}
		}
		else
		{
			$nodeText .= (in_array($tagName, $this->CDATAEncoding)) ? $this->sanitizeCDATA($tagContent) : htmlspecialchars($tagContent);
		}
		
		$nodeText .= (in_array($tagName, $this->CDATAEncoding)) ? ']]>' : '';
		$nodeText .= "</$tagName>" . PHP_EOL;

		return $nodeText;
	}
	
	/**
	* Make the channels.
	*
	* @access   private
	* @return   void
	*/
	private function makeChannels()
	{
		$out = '';

		//Start channel tag
		switch ($this->version)
		{
			case Feed::RSS2:
				$out .= '<channel>' . PHP_EOL;
				break;
			case Feed::RSS1:
				$out .= (isset($this->data['ChannelAbout']))? "<channel rdf:about=\"{$this->data['ChannelAbout']}\">" : "<channel rdf:about=\"{$this->channels['link']}\">";
				break;
		}
		
		//Print Items of channel
		foreach ($this->channels as $key => $value)
		{
			if($this->version == Feed::ATOM && $key == 'link')
			{
				// ATOM prints link element as href attribute
				$out .= $this->makeNode($key,'', array('href' => $value));
				//Add the id for ATOM
				$out .= $this->makeNode('id', Feed::uuid($value, 'urn:uuid:'));
			}
			else
			{
				$out .= $this->makeNode($key, $value);
			}
			
		}
		
		//RSS 1.0 have special tag <rdf:Seq> with channel
		if($this->version == Feed::RSS1)
		{
			$out .= "<items>" . PHP_EOL . "<rdf:Seq>" . PHP_EOL;
			foreach ($this->items as $item)
			{
				$thisItems = $item->getElements();
				$out .= "<rdf:li resource=\"{$thisItems['link']['content']}\"/>" . PHP_EOL;
			}
			$out .= "</rdf:Seq>" . PHP_EOL . "</items>" . PHP_EOL . "</channel>" . PHP_EOL;
		}

		return $out;
	}
	
	/**
	* Prints formatted feed items
	* 
	* @access   private
	* @return   void
	*/
	private function makeItems()
	{
		$out = '';

		foreach ($this->items as $item)
		{
			$thisItems = $item->getElements();
			
			// the argument is printed as rdf:about attribute of item in rss 1.0
			$out .= $this->startItem($thisItems['link']['content']);
			
			foreach ($thisItems as $feedItem)
			{
				$out .= $this->makeNode($feedItem['name'], $feedItem['content'], $feedItem['attributes']);
			}
			$out .= $this->endItem();
		}

		return $out;
	}
	
	/**
	* Make the starting tag of channels
	* 
	* @access   private
	* @param    string  The vale of about tag which is used for RSS 1.0 only.
	* @return   void
	*/
	private function startItem($about = false)
	{
		$out = '';

		if($this->version == Feed::RSS2)
		{
			$out .= '<item>' . PHP_EOL;
		}
		else if($this->version == Feed::RSS1)
		{
			if($about)
			{
				$out .= "<item rdf:about=\"$about\">" . PHP_EOL;
			}
			else
			{
				throw new Exception("link element is not set - It's required for RSS 1.0 to be used as the about attribute of the item tag.");
			}
		}
		else if($this->version == Feed::ATOM)
		{
			$out .= "<entry>" . PHP_EOL;
		}

		return $out;
	}
	
	/**
	* Closes feed item tag
	*
	* @access   private
	* @return   void
	*/
	private function endItem()
	{
		if($this->version == Feed::RSS2 || $this->version == Feed::RSS1)
		{
			return '</item>' . PHP_EOL;
		}
		else if($this->version == Feed::ATOM)
		{
			return '</entry>' . PHP_EOL;
		}
	}
	
	/**
	* Sanitizes data which will be later on returned as CDATA in the feed.
	*
	* A "]]>" respectively "<![CDATA" in the data would break the CDATA in the
	* XML, so the brackets are converted to a HTML entity.
	*
	* @access   private
	* @param    string  Data to be sanitized
	* @return   string  Sanitized data
	*/
	private function sanitizeCDATA($text)
	{
		$text = str_replace("]]>", "]]&gt;", $text);
		$text = str_replace("<![CDATA[", "&lt;![CDATA[", $text);

		return $text;
	}
	
	// End # private functions ----------------------------------------------
	
} // end of class FeedWriter
