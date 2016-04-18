<?php

// You should use an autoloader instead of including the files directly.
// This is done here only to make the examples work out of the box.
include '../Item.php';
include '../Feed.php';
include '../RSS2.php';

date_default_timezone_set('UTC');

use \FeedWriter\RSS2;

/**
 * Copyright (C) 2008 Anis uddin Ahmad <anisniit@gmail.com>
 * Copyright (C) 2013 Michael Bemmerl <mail@mx-server.de>
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

// Creating an instance of RSS2 class.
$TestFeed = new RSS2;

// Setting some basic channel elements. These three elements are mandatory.
$TestFeed->setTitle('Testing & Checking the Feed Writer project');
$TestFeed->setLink('https://github.com/mibe/FeedWriter');
$TestFeed->setDescription('This is just an example how to use the Feed Writer project in your code.');

// Image title and link must match with the 'title' and 'link' channel elements for RSS 2.0,
// which were set above.
$TestFeed->setImage('Testing & Checking the Feed Writer project', 'https://github.com/mibe/FeedWriter', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Rss-feed.svg/256px-Rss-feed.svg.png');

// Use core setChannelElement() function for other optional channel elements.
// See http://www.rssboard.org/rss-specification#optionalChannelElements
// for other optional channel elements. Here the language code for American English and
$TestFeed->setChannelElement('language', 'en-US');

// The date when this feed was lastly updated. The publication date is also set.
$TestFeed->setDate(date(DATE_RSS, time()));
$TestFeed->setChannelElement('pubDate', date(\DATE_RSS, strtotime('2013-04-06')));

// By using arrays as channelElement values, can be set element like this
//  <skipDays>
//      <day>Saturday</day>
//      <day>Sunday</day>
// </skipDays>
// Thanks to - Peter Fargaš <peter.fargas.work@googlemail.com>
$TestFeed->setChannelElement("skipDays", array('day'=>['Saturday','Sunday']));

// You can add additional link elements, e.g. to a PubSubHubbub server with custom relations.
// It's recommended to provide a backlink to the feed URL.
$TestFeed->setSelfLink('http://example.com/myfeed');
$TestFeed->setAtomLink('http://pubsubhubbub.appspot.com', 'hub');

// You can add more XML namespaces for more custom channel elements which are not defined
// in the RSS 2 specification. Here the 'creativeCommons' element is used. There are much more
// available. Have a look at this list: http://feedvalidator.org/docs/howto/declare_namespaces.html
$TestFeed->addNamespace('creativeCommons', 'http://backend.userland.com/creativeCommonsRssModule');
$TestFeed->setChannelElement('creativeCommons:license', 'http://www.creativecommons.org/licenses/by/1.0');

// If you want you can also add a line to publicly announce that you used
// this fine piece of software to generate the feed. ;-)
$TestFeed->addGenerator();

// Here we are done setting up the feed. What's next is adding some feed items.

// Create a new feed item.
$newItem = $TestFeed->createNewItem();

// Add basic elements to the feed item
// These are again mandatory for a valid feed.
$newItem->setTitle('Hello World!');
$newItem->setLink('http://www.example.com');
$newItem->setDescription('This is a test of adding a description by the <b>Feed Writer</b> classes. It\'s automatically CDATA encoded.');

// The following method calls add some optional elements to the feed item.

// Let's set the publication date of this item. You could also use a UNIX timestamp or
// an instance of PHP's DateTime class.
$newItem->setDate('2013-04-07 00:50:30');

// You can also attach a media object to a feed item. You just need the URL, the byte length
// and the MIME type of the media. Here's a quirk: The RSS2 spec says "The url must be an http url.".
// Other schemes like ftp, https, etc. produce an error in feed validators.
$newItem->setEnclosure('http://upload.wikimedia.org/wikipedia/commons/4/49/En-us-hello-1.ogg', 11779, 'audio/ogg');

// If you want you can set the name (and email address) of the author of this feed item.
$newItem->setAuthor('Anis uddin Ahmad', 'admin@ajaxray.com');

// You can set a globally unique identifier. This can be a URL or any other string.
// If you set permaLink to true, the identifier must be an URL. The default of the
// permaLink parameter is false.
$newItem->setId('http://example.com/URL/to/article', true);

// Use the addElement() method for other optional elements.
// This here will add the 'source' element. The second parameter is the value of the element
// and the third is an array containing the element attributes.
$newItem->addElement('source', 'Mike\'s page', array('url' => 'http://www.example.com'));

// Now add the feed item to the main feed.
$TestFeed->addItem($newItem);

// Another method to add feeds items is by using an array which contains key-value pairs
// of every item element. Elements which have attributes cannot be added by this way.
$newItem = $TestFeed->createNewItem();
$newItem->addElementArray(array('title'=> 'The 2nd item', 'link' => 'http://www.google.com', 'description' => 'Just another test.'));
$TestFeed->addItem($newItem);

// OK. Everything is done. Now generate the feed.
// Then do anything (e,g cache, save, attach, print) you want with the feed in $myFeed.
$myFeed = $TestFeed->generateFeed();

// If you want to send the feed directly to the browser, use the printFeed() method.
$TestFeed->printFeed();