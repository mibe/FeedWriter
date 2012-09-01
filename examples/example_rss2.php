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

  
  include("../FeedTypes.php");
  
  //Creating an instance of RSS2FeedWriter class. 
  //The constant RSS2 is passed to mention the version
  $TestFeed = new RSS2FeedWriter();
  
  //Setting the channel elements
  //Use wrapper functions for common channel elements
  $TestFeed->setTitle('Testing & Checking the RSS writer class');
  $TestFeed->setLink('http://www.ajaxray.com/projects/rss');
  $TestFeed->setDescription('This is a test of creating a RSS 2.0 feed with Universal Feed Writer');
  
  //Image title and link must match with the 'title' and 'link' channel elements for RSS 2.0
  $TestFeed->setImage('Testing the RSS writer class','http://www.ajaxray.com/projects/rss','http://www.rightbrainsolution.com/_resources/img/logo.png');
  
  //Use core setChannelElement() function for other optional channels
  $TestFeed->setChannelElement('language', 'en-us');
  $TestFeed->setChannelElement('pubDate', date(DATE_RSS, time()));
  
  //Adding a feed. Genarally this portion will be in a loop and add all feeds.
  
  //Create an empty FeedItem
  $newItem = $TestFeed->createNewItem();
  
  //Add elements to the feed item
  //Use wrapper functions to add common feed elements
  $newItem->setTitle('The first feed');
  $newItem->setLink('http://www.yahoo.com');
  //The parameter is a timestamp for setDate() function
  $newItem->setDate(time());
  $newItem->setDescription('This is a test of adding CDATA encoded description by the php <b>Universal Feed Writer</b> class');
  $newItem->setEncloser('http://www.attrtest.com', '1283629', 'audio/mpeg');
  //Use core addElement() function for other supported optional elements
  $newItem->addElement('author', 'admin@ajaxray.com (Anis uddin Ahmad)');
  //Attributes have to passed as array in 3rd parameter
  $newItem->addElement('guid', 'http://www.ajaxray.com',array('isPermaLink'=>'true'));
  
  //Now add the feed item
  $TestFeed->addItem($newItem);
  
  //Another method to add feeds from array()
  //Elements which have attribute cannot be added by this way
  $newItem = $TestFeed->createNewItem();
  $newItem->addElementArray(array('title'=>'The 2nd feed', 'link'=>'http://www.google.com', 'description'=>'This is a test of the FeedWriter class'));
  $TestFeed->addItem($newItem);
  
  //OK. Everything is done. Now genarate the feed.
  $TestFeed->generateFeed();
  
?>
