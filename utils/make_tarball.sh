#!/bin/bash
if [ -z "${1}" ]; then
    echo "Usage: make_tarball.sh git_tag"
    exit
fi
GIT_TAG=${1}
git archive ${GIT_TAG} --prefix ${GIT_TAG}/ -o "${GIT_TAG}".tar.gz
