<?php

// You should use an autoloader instead of including the files directly.
// This is done here only to make the examples work out of the box.
include '../Item.php';
include '../Feed.php';
include '../ATOM.php';
include '../InvalidOperationException.php';

date_default_timezone_set('UTC');

use \FeedWriter\ATOM;

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

// IMPORTANT : No need to add id for feed or channel. It will be automatically created from link.

//Creating an instance of ATOM class.
$TestFeed = new ATOM();

//Setting the channel elements
//Use wrapper functions for common elements
$TestFeed->setTitle('Testing the RSS writer class');
$TestFeed->setDescription('This is just a short example for using the FeedWriter classes...');
$TestFeed->setLink('http://www.ajaxray.com/rss2/channel/about');
$TestFeed->setDate(new DateTime());

//For other channel elements, use setChannelElement() function
$TestFeed->setChannelElement('author', array('name'=>'Anis uddin Ahmad'));

//You can add additional link elements, e.g. to a PubSubHubbub server with custom relations.
$TestFeed->setSelfLink('http://example.com/myfeed');
$TestFeed->setAtomLink('http://pubsubhubbub.appspot.com', 'hub');

//Adding a feed. Generally this portion will be in a loop and add all feeds.

//Create an empty Item
$newItem = $TestFeed->createNewItem();

//Add elements to the feed item
//Use wrapper functions to add common feed elements
$newItem->setTitle('The first feed');
$newItem->setLink('http://www.yahoo.com');
$newItem->setDate(time());
$newItem->setAuthor('Anis uddin Ahmad', 'anis@example.invalid');
$newItem->addEnclosure('http://upload.wikimedia.org/wikipedia/commons/4/49/En-us-hello-1.ogg', 11779, 'audio/ogg');

//Internally changed to "summary" tag for ATOM feed
$newItem->setDescription('This is a test of adding CDATA encoded description by the php <b>Universal Feed Writer</b> class');
$newItem->setContent('<h1>hi.</h1> <p>This is the content for the entry.</p>');

//Now add the feed item
$TestFeed->addItem($newItem);

//OK. Everything is done. Now generate the feed.
$TestFeed->printFeed();
