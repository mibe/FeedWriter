<?php

// SPDX-FileCopyrightText: 2012 Michael Bemmerl <mail@mx-server.de>
// SPDX-License-Identifier: GPL-3.0-or-later

namespace FeedWriter;

/**
 * Wrapper for creating ATOM feeds.
 */
class ATOM extends Feed
{
    public function __construct()
    {
        parent::__construct(Feed::ATOM);
    }
}
