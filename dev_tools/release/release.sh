#!/bin/bash -e

#release.sh 1.8.2
#creates an airtime folder with a "1.8.2" suffix
#creates tarballs with a "1.8.2" suffix

#release.sh 1.8.2 RC
#creates an airtime folder with a "1.8.2" suffix
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

echo "Creating tarballs with ${suffix} suffix"

target=/tmp/airtime-${version}
target_file=/tmp/airtime-${suffix}.tar.gz

rm -rf $target
rm -f $target_file
git clone file://$gitrepo $target

cd $target

echo "Checking out tag airtime-${suffix}"
git checkout airtime-${suffix}


cd $target
rm -rf .git .gitignore .gitmodules .zfproject.xml dev_tools/ audio_samples/ 

#echo "Minimizing Airtime Javascript files..."
#cd $dir
#find $target/airtime_mvc/public/js/airtime/ -iname "*.js" -exec bash -c 'echo {}; jsmin/jsmin < {} > {}.min' \;
#find $target/airtime_mvc/public/js/airtime/ -iname "*.js" -exec mv {}.min {} \;
#echo "Done"

#zip -r airtime-${suffix}.zip airtime-${version}
cd /tmp/
tar -czf $target_file airtime-${version}

echo "Output file available at $target_file"
