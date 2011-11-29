#!/bin/bash

exec 2>&1

target="airtime_git_branch"
#airtime_versions=(""  "airtime_182_tar" "airtime_190_tar" "airtime_191_tar" "airtime_192_tar" "airtime_192_tar" "airtime_194_tar" "airtime_195_tar")
airtime_versions=("airtime_191_tar" "airtime_192_tar" "airtime_192_tar" "airtime_194_tar" "airtime_195_tar")
#airtime_versions=("")
ubuntu_versions=("ubuntu_natty_64")
#ubuntu_versions=("ubuntu_lucid_32" "ubuntu_lucid_64" "ubuntu_maverick_32" "ubuntu_maverick_64" "ubuntu_natty_32" "ubuntu_natty_64" "ubuntu_oneiric_32" "ubuntu_oneiric_64" "debian_squeeze_32" "debian_squeeze_64")

num1=${#ubuntu_versions[@]}
num2=${#airtime_versions[@]}

mkdir -p ./upgrade_logs

for i in $(seq 0 $(($num1 -1)));
do
    #echo fab -f fab_setup.py os_update shutdown
    for j in $(seq 0 $(($num2 -1)));
    do
        echo fab -f fab_setup.py ${ubuntu_versions[$i]} ${airtime_versions[$j]} $target shutdown
        fab -f fab_release_test.py ${ubuntu_versions[$i]} ${airtime_versions[$j]} $target shutdown 2>&1 | tee "./upgrade_logs/${ubuntu_versions[$i]}_${airtime_versions[$j]}_$target.log"
    done
done
