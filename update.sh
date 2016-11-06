#!/bin/bash

sudo pear channel-update pear.phpdoc.org
sudo pear upgrade
phpdoc run -d /media/smb -t ~/tmp
