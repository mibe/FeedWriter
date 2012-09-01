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
  include("../FeedTypes.php");
  
  //Creating an instance of RSS2FeedWriter class. 
  $TestFeed = new RSS2FeedWriter();
  
  //Setting the channel elements
  //Use wrapper functions for common channel elements
  $TestFeed->setTitle('Testing & Checking the RSS writer class');
  $TestFeed->setLink('http://www.ajaxray.com/projects/rss');
  $TestFeed->setDescription('This is a test of creating a RSS 2.0 feed Universal Feed Writer');
  
  //Image title and link must match with the 'title' and 'link' channel elements for valid RSS 2.0
  $TestFeed->setImage('Testing the RSS writer class','http://www.ajaxray.com/projects/rss','http://www.rightbrainsolution.com/_resources/img/logo.png');
  
  //Let's add some feed items: Create two empty FeedItem instances
  $itemOne = $TestFeed->createNewItem();
  $itemTwo = $TestFeed->createNewItem();
  
  //Add item details
  $itemOne->setTitle('The title of the first entry.');
  $itemOne->setLink('http://www.google.de');
  $itemOne->setDate(time());
  $itemOne->setDescription('And here\'s the description of the entry.');
  $itemTwo->setTitle('Lorem ipsum');
  $itemTwo->setLink('http://www.example.com');
  $itemTwo->setDate(1234567890);
  $itemTwo->setDescription('Lorem ipsum dolor sit amet, consectetur, adipisci velit');
  
  //Now add the feed item
  $TestFeed->addItem($itemOne);
  $TestFeed->addItem($itemTwo);
  
  //OK. Everything is done. Now genarate the feed.
  $TestFeed->generateFeed();
  
?>
