<?php
namespace FeedWriter;

use \DateTime;

/* 
 * Copyright (C) 2008 Anis uddin Ahmad <anisniit@gmail.com>
 * Copyright (C) 2010-2013 Michael Bemmerl <mail@mx-server.de>
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

/**
 * Universal Feed Writer
 *
 * Item class - Used as feed element in Feed class
 *
 * @package         UniversalFeedWriter
 * @author          Anis uddin Ahmad <anisniit@gmail.com>
 * @link            http://www.ajaxray.com/projects/rss
 */
class Item
{
	/**
	* Collection of feed item elements
	*/
	private $elements = array();

	/**
	* Contains the format of this feed.
	*/
	private $version;
	
	/**
	* Constructor
	* 
	* @param    constant     (RSS1/RSS2/ATOM) RSS2 is default.
	*/ 
	function __construct($version = Feed::RSS2)
	{
		$this->version = $version;
	}
	
	/**
	* Add an element to elements array
	*
	* @access   public
	* @param    string  The tag name of an element
	* @param    string  The content of tag
	* @param    array   Attributes(if any) in 'attrName' => 'attrValue' format
	* @param    boolean Specifies, if an already existing element is overwritten.
	* @return   void
	*/
	public function addElement($elementName, $content, $attributes = null, $overwrite = FALSE)
	{
		// return if element already exists & if overwriting is disabled.
		if (isset($this->elements[$elementName]) && !$overwrite)
			return;

		$this->elements[$elementName]['name']       = $elementName;
		$this->elements[$elementName]['content']    = $content;
		$this->elements[$elementName]['attributes'] = $attributes;
	}
	
	/**
	* Set multiple feed elements from an array.
	* Elements which have attributes cannot be added by this method
	*
	* @access   public
	* @param    array   array of elements in 'tagName' => 'tagContent' format.
	* @return   void
	*/
	public function addElementArray($elementArray)
	{
		if (!is_array($elementArray))
			return;

		foreach ($elementArray as $elementName => $content)
		{
			$this->addElement($elementName, $content);
		}
	}
	
	/**
	* Return the collection of elements in this feed item
	*
	* @access   public
	* @return   array
	*/
	public function getElements()
	{
		return $this->elements;
	}

	/**
	* Return the type of this feed item
	* 
	* @access   public
	* @return   string  The feed type, as defined in Feed.php
	*/
	public function getVersion()
	{
		return $this->version;
	}
	
	// Wrapper functions ------------------------------------------------------
	
	/**
	* Set the 'description' element of feed item
	* 
	* @access   public
	* @param    string  The content of 'description' or 'summary' element
	* @return   void
	*/
	public function setDescription($description)
	{
		$tag = ($this->version == Feed::ATOM) ? 'summary' : 'description';
		$this->addElement($tag, $description);
	}

	/**
	 * Set the 'content' element of the feed item
	 * For ATOM feeds only
	 *
	 * @access public
	 * @param string Content for the item (i.e., the body of a blog post).
	 * @return void
	 */
	public function setContent($content)
	{
		if ($this->version != Feed::ATOM)
			die('The content element is supported in ATOM feeds only.');

		$this->addElement('content', $content, array('type' => 'html'));
	}

	
	/**
	* Set the 'title' element of feed item
	*
	* @access   public
	* @param    string  The content of 'title' element
	* @return   void
	*/
	public function setTitle($title)
	{
		$this->addElement('title', $title);
	}
	
	/**
	* Set the 'date' element of the feed item.
	* 
	* The value of the date parameter can be either an instance of the
	* DateTime class, an integer containing a UNIX timestamp or a string
	* which is parseable by PHP's 'strtotime' function.
	* 
	* @access   public
	* @param    DateTime|int|string  Date which should be used.
	* @return   void
	*/
	public function setDate($date)
	{
		if(!is_numeric($date))
		{
			if ($date instanceof DateTime)
				$date = $date->getTimestamp();
			else
				$date = strtotime($date);
		}
		else if ($date < 0)
			die('The given date is not an UNIX timestamp.');
		
		if($this->version == Feed::ATOM)
		{
			$tag    = 'updated';
			$value  = date(\DATE_ATOM, $date);
		}
		elseif($this->version == Feed::RSS2)
		{
			$tag    = 'pubDate';
			$value  = date(\DATE_RSS, $date);
		}
		else
		{
			$tag    = 'dc:date';
			$value  = date("Y-m-d", $date);
		}
		
		$this->addElement($tag, $value);
	}
	
	/**
	* Set the 'link' element of feed item
	* 
	* @access   public
	* @param    string  The content of 'link' element
	* @return   void
	*/
	public function setLink($link)
	{
		if($this->version == Feed::RSS2 || $this->version == Feed::RSS1)
		{
			$this->addElement('link', $link);
		}
		else
		{
			$this->addElement('link','',array('href'=>$link));
			$this->addElement('id', Feed::uuid($link,'urn:uuid:'));
		}
	}
	
	/**
	* Attach a external media to the feed item.
	* For RSS 2.0 only.
	* 
	* See RFC 4288 for syntactical correct MIME types.
	* 
	* @access   public
	* @param    string  The URL of the media.
	* @param    integer The length of the media.
	* @param    string  The MIME type attribute of the media.
	* @return   void
	* @link     https://tools.ietf.org/html/rfc4288
	*/
	public function setEnclosure($url, $length, $type)
	{
		if ($this->version != Feed::RSS2)
			die('The enclosure element is supported in RSS2 feeds only.');

		if (!is_numeric($length) || $length <= 0)
			die('The length parameter must be an integer and greater than zero.');

		// Regex used from RFC 4287, page 41
		if (!is_string($type) || preg_match('/.+\/.+/', $type) != 1)
			die('type parameter must be a string and a MIME type.');

		$attributes = array('url' => $url, 'length' => $length, 'type' => $type);
		$this->addElement('enclosure', '', $attributes);
	}

	/**
	* Set the 'author' element of feed item.
	* Not supported in RSS 1.0 feeds.
	* 
	* @access   public
	* @param    string  The author of this item
	* @param    string  Optional email address of the author
	* @param    string  Optional URI related to the author
	* @return   void
	*/
	public function setAuthor($author, $email = null, $uri = null)
	{
		switch($this->version)
		{
			case Feed::RSS1: die('The author element is not supported in RSS1 feeds.');
				break;
			case Feed::RSS2:
				if ($email != null)
					$author = $email . ' (' . $author . ')';

				$this->addElement('author', $author);
				break;
			case Feed::ATOM:
				$elements = array('name' => $author);

				// Regex from RFC 4287 page 41
				if ($email != null && preg_match('/.+@.+/', $email) == 1)
					$elements['email'] = $email;

				if ($uri != null)
					$elements['uri'] = $uri;

				$this->addElement('author', $elements);
				break;
		}
	}

	/**
	* Set the unique identifier of the feed item
	* 
	* @access   public
	* @param    string  The unique identifier of this item
	* @param    boolean The value of the 'isPermaLink' attribute in RSS 2 feeds.
	* @return   void
	*/
	public function setId($id, $permaLink = false)
	{
		if ($this->version == Feed::RSS2)
		{
			if (!is_bool($permaLink))
				die('The permaLink parameter must be boolean.');

			$permaLink = $permaLink ? 'true' : 'false';

			$this->addElement('guid', $id, array('isPermaLink' => $permaLink));
		}
		else if ($this->version == Feed::ATOM)
		{
			$this->addElement('id', Feed::uuid($id,'urn:uuid:'), NULL, TRUE);
		}
		else
			die('A unique ID is not supported in RSS1 feeds.');
	}
	
 } // end of class Item
