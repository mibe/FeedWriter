<?php

// SPDX-FileCopyrightText: 2008 Anis uddin Ahmad <anisniit@gmail.com>
// SPDX-License-Identifier: GPL-3.0-or-later

// You should use an autoloader instead of including the files directly.
// This is done here only to make the examples work out of the box.
require_once __DIR__ . '/../src/Item.php';
require_once __DIR__ . '/../src/Feed.php';
require_once __DIR__ . '/../src/RSS1.php';
require_once __DIR__ . '/../src/InvalidOperationException.php';

date_default_timezone_set('UTC');

use FeedWriter\RSS1;

// Creating an instance of RSS1 class.
$TestFeed = new RSS1();

// Setting the channel elements
// Use wrapper functions for common elements
// For other optional channel elements, use setChannelElement() function
$TestFeed->setTitle('Testing the RSS writer class');
$TestFeed->setLink('http://www.ajaxray.com/rss2/channel/about');
$TestFeed->setDescription('This is test of creating a RSS 1.0 feed by Universal Feed Writer');

// It's important for RSS 1.0
$TestFeed->setChannelAbout('http://www.ajaxray.com/rss2/channel/about');

// An image is optional.
$TestFeed->setImage('https://upload.wikimedia.org/wikipedia/commons/0/07/Rss.jpg', 'You can add an image, if you want', 'http://www.ajaxray.com/rss2/channel/about');

// Adding a feed. Generally this portion will be in a loop and add all feeds.

// Create an empty FeedItem
$newItem = $TestFeed->createNewItem();

// Add elements to the feed item
// Use wrapper functions to add common feed elements
$newItem->setTitle('The first feed');
$newItem->setLink('http://www.yahoo.com');
// The parameter is a timestamp for setDate() function
$newItem->setDate(time());
$newItem->setDescription('This is test of adding CDATA encoded description by the php <b>Universal Feed Writer</b> class');
// Use core addElement() function for other supported optional elements
$newItem->addElement('dc:subject', 'Nothing but test');

// Now add the feed item
$TestFeed->addItem($newItem);

// Adding multiple elements from array
// Elements which have an attribute cannot be added by this way
$newItem = $TestFeed->createNewItem();
$newItem->addElementArray(['title' => 'The 2nd feed', 'link' => 'http://www.google.com', 'description' => 'This is a test of the FeedWriter class']);
$TestFeed->addItem($newItem);

// OK. Everything is done. Now generate the feed.
$TestFeed->printFeed();
