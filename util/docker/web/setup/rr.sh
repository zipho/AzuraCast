#!/bin/bash
set -e
source /bd_build/buildconfig
set -x

mkdir -p /var/azuracast/rr
chown -R azuracast:azuracast /var/azuracast/rr

cp /bd_build/rr/.rr.yaml.tmpl /var/azuracast/rr/.rr.yaml.tmpl
