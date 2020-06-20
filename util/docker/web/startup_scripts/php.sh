#!/bin/bash

# Copy the php.ini template to its destination.
dockerize -template "/etc/php/05-azuracast.ini.tmpl:/etc/php/7.4/cli/conf.d/05-azuracast.ini" \
    true
