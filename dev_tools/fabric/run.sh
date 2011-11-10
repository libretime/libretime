#!/bin/bash

exec 2>&1

target="airtime_195_tar"
airtime_versions=(""  "airtime_182_tar" "airtime_194_tar")
ubuntu_versions=("ubuntu_lucid_32" "ubuntu_natty_32")


num1=${#ubuntu_versions[@]}
num2=${#airtime_versions[@]}

mkdir -p ./upgrade_logs

for i in $(seq 0 $(($num1 -1)));
do
    for j in $(seq 0 $(($num2 -1)));
    do
        echo fab -f fab_setup.py ${ubuntu_versions[$i]} ${airtime_versions[$j]} $target shutdown
        fab -f fab_release_test.py ${ubuntu_versions[$i]} ${airtime_versions[$j]} $target shutdown 2>&1 | tee "./upgrade_logs/${ubuntu_versions[$i]}_${airtime_versions[$j]}_$target.log"
    done
done
