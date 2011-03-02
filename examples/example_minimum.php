<?php

/* 
 * Copyright (C) 2008 Anis uddin Ahmad <anisniit@gmail.com>
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

  // This is a minimum example of using the class
  include("../FeedWriter.php");
  
  //Creating an instance of FeedWriter class. 
  $TestFeed = new FeedWriter(RSS2);
  
  //Setting the channel elements
  //Use wrapper functions for common channel elements
  $TestFeed->setTitle('Testing & Checking the RSS writer class');
  $TestFeed->setLink('http://www.ajaxray.com/projects/rss');
  $TestFeed->setDescription('This is test of creating a RSS 2.0 feed Universal Feed Writer');
  
  //Image title and link must match with the 'title' and 'link' channel elements for valid RSS 2.0
  $TestFeed->setImage('Testing the RSS writer class','http://www.ajaxray.com/projects/rss','http://www.rightbrainsolution.com/images/logo.gif');
  
	//Retriving informations from database addin feeds
	$db->query($query);
	$result = $db->result;

	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		//Create an empty FeedItem
		$newItem = $TestFeed->createNewItem();
		
		//Add elements to the feed item    
		$newItem->setTitle($row['title']);
		$newItem->setLink($row['link']);
		$newItem->setDate($row['create_date']);
		$newItem->setDescription($row['description']);
		
		//Now add the feed item
		$TestFeed->addItem($newItem);
	}
  
  //OK. Everything is done. Now genarate the feed.
  $TestFeed->generateFeed();
  
?>
