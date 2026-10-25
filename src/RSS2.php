<?php

// SPDX-FileCopyrightText: 2012 Michael Bemmerl <mail@mx-server.de>
// SPDX-License-Identifier: GPL-3.0-or-later

namespace FeedWriter;

/**
 * Wrapper for creating RSS2 feeds.
 */
class RSS2 extends Feed
{
    public function __construct()
    {
        parent::__construct(Feed::RSS2);
    }
}
