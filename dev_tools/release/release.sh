#!/bin/bash -e

#release.sh 1.8.2
#creates a libretime folder with a "1.8.2" suffix
#creates tarballs with a "1.8.2" suffix

#release.sh 1.8.2 RC
#creates a libretime folder with a "1.8.2-RC" suffix
#creates tarballs with a "1.8.2-RC" suffix

#release.sh 1.8.2-RC
#creates a libretime folder with a "1.8.2-RC" suffix
#creates tarballs with a "1.8.2-RC" suffix

if [ $# == 0 ]; then
    echo "Zero arguments"
    exit
elif [ $# == 1 ]; then
    suffix=$1
    version=$1
else
    suffix=$1-$2
    version=$1
fi

dir=$(dirname $(readlink -f $0))
gitrepo=$(readlink -f ./../../)

echo "Creating tarball for LibreTime ${suffix}."

target=/tmp/libretime-${suffix}
target_file=${gitrepo}/build/libretime-${suffix}.tar.gz

rm -rf $target
rm -f $target_file
echo -n "Cloning temporary git repo..."
git clone --quiet --depth=1 file://$gitrepo $target
echo " Done"

echo -n "Creating VERSION file for ${suffix}..."
echo -n "${suffix}" > ${target}/VERSION
echo " Done"

pushd $target

echo -n "Checking out tag ${suffix}..."
git fetch --quiet --tags
git checkout --quiet tags/${suffix}
echo " Done"

echo -n "Running composer install..."
composer install --quiet --no-dev --ignore-platform-reqs
echo " Done"

popd

#echo "Minimizing LibreTime Javascript files..."
#cd $dir
#find $target/airtime_mvc/public/js/airtime/ -iname "*.js" -exec bash -c 'echo {}; jsmin/jsmin < {} > {}.min' \;
#find $target/airtime_mvc/public/js/airtime/ -iname "*.js" -exec mv {}.min {} \;
#echo "Done"

pushd /tmp/
find libretime-${suffix} -type f -exec dos2unix {} \;
echo -n "Creating tarball..."
tar -czf $target_file \
        --owner=root --group=root \
        --exclude-vcs \
        --exclude .zfproject.xml \
        --exclude .gitignore \
        --exclude .gitattributes \
        --exclude .travis.yml \
        --exclude travis \
        --exclude dev_tools \
        --exclude vendor/phing \
        --exclude vendor/simplepie/simplepie/tests \
    libretime-${suffix} 
echo " Done"
popd


echo "Output file available at $target_file"
