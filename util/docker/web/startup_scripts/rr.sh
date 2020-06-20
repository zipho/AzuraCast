#!/bin/bash

# Copy the RoadRunner file template.
dockerize -template "/var/azuracast/rr/.rr.yaml.tmpl:/var/azuracast/rr/.rr.yaml" \
    true
