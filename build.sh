#!/bin/bash -e
composer install --no-dev

git_build=""
if [ -d .git ]; then
    echo " * Building from Git"
    git_build="y"
fi

if [ "${git_build}" = "y" ]; then
    git_version=`git tag --points-at HEAD`
    echo " * Version from tag: ${git_version}"
    if [ "${git_version}" = "" ]; then
        git_version=`git rev-parse --short HEAD`
        echo " * Overriding empty version with sha1 commit-ish: ${git_version}"
    fi
    echo ${git_version} > VERSION
else
    # if no file was in tarball we create one letting the user know
    # travis should release tarballs with a pre-written VERSION file
    # at some stage
    if [ ! -f VERSION ]; then
        folder_name=$(basename `pwd`)
        echo "tarball install from folder ${folder_name}" > VERSION
    fi
fi

