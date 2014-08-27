#!/bin/bash

# Set up 3 way PO file merging, which we need for non-mainline branches
cp scripts/git-merge-po /usr/local/bin
chmod +x /usr/local/bin/git-merge-po
cat git-config-git-merge-po >> ../.git/config
cat git-attributes-git-merge-po >> ../.gitattributes


