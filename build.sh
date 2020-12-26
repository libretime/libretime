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
    # if you run in to this you should grab an enriched tarball built
    # by travis. It already contains the VERSION file and also bundles
    # all the PHP you vendors files making the install much faster on
    # your part.
    if [ ! -f VERSION ]; then
        echo "could not detect version for VERSION file" > VERSION
    fi
fi

pushd ui
yarn install
yarn build
popd
cp ui/dist/js/*.js.map ui/dist/js/*.js airtime_mvc/public/js/
cp ui/dist/css/*.css airtime_mvc/public/css/
