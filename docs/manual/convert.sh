#!/bin/bash

book="booktype-25"
base_path=`pwd`
pandoc_cmd="docker run --rm -ti -v ${base_path}:${base_path} jagregory/pandoc"

for html in `find ${book} -name 'index.html'`; do
    pushd `dirname $html`
    mkdir -p ${base_path}/`dirname ${html#*/}`
    $pandoc_cmd -o - -f html -t markdown_github ${base_path}/${html} > `echo "${base_path}/${html#*/}" | sed 's/html$/md/'`
    popd
done

for static in `find ${book} -name 'static'`; do
    cp -rp ${static} ${static#*/}
done
