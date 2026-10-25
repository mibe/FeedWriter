<?php

// SPDX-FileCopyrightText: 2008 Anis uddin Ahmad <anisniit@gmail.com>
// SPDX-License-Identifier: GPL-3.0-or-later

// You should use an autoloader instead of including the files directly.
// This is done here only to make the examples work out of the box.
require_once __DIR__ . '/../src/Item.php';
require_once __DIR__ . '/../src/Feed.php';
require_once __DIR__ . '/../src/RSS2.php';
require_once __DIR__ . '/../src/InvalidOperationException.php';

date_default_timezone_set('UTC');

use FeedWriter\RSS2;

// Creating an instance of RSS2 class.
$TestFeed = new RSS2();

// Setting the channel elements
// Use wrapper functions for common channel elements
$TestFeed->setTitle('Testing & Checking the RSS writer class');
$TestFeed->setLink('http://www.ajaxray.com');
$TestFeed->setDescription('This is a test of creating a RSS 2.0 feed Universal Feed Writer');

// Let's add some feed items: Create two empty Item instances
$itemOne = $TestFeed->createNewItem();
$itemTwo = $TestFeed->createNewItem();

// Add item details
$itemOne->setTitle('The title of the first entry.');
$itemOne->setLink('http://www.google.de');
$itemOne->setDate(time());
$itemOne->setDescription('And here\'s the description of the entry.');

$itemTwo->setTitle('Lorem ipsum');
$itemTwo->setLink('http://www.example.com');
$itemTwo->setDate(1234567890);
$itemTwo->setDescription('Lorem ipsum dolor sit amet, consectetur, adipisci velit');

// Now add the feed item
$TestFeed->addItem($itemOne);
$TestFeed->addItem($itemTwo);

// OK. Everything is done. Now generate the feed.
$TestFeed->printFeed();
