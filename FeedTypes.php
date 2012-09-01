<?php

/* 
 * Copyright (C) 2012 Michael Bemmerl <mail@mx-server.de>
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

if (!class_exists('FeedWriter'))
	require dirname(__FILE__) . '/FeedWriter.php';

/**
 * Universal Feed Writer class
 *
 * Generate RSS 1.0, RSS2.0 and ATOM Feeds
 *
 * @package     UniversalFeedWriter
 * @author      Anis uddin Ahmad <anisniit@gmail.com>
 * @link        http://www.ajaxray.com/projects/rss
 */

class RSS1FeedWriter extends FeedWriter
{
	function __construct()
	{
		parent::__construct(RSS1);
	}
}

class RSS2FeedWriter extends FeedWriter
{
	function __construct()
	{
		parent::__construct(RSS2);
	}
}

class ATOMFeedWriter extends FeedWriter
{
	function __construct()
	{
		parent::__construct(ATOM);
	}
}
