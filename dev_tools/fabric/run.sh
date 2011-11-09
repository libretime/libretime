#!/bin/bash

exec 2>&1

target="airtime_195_tar"
airtime_versions=(""  "airtime_182_tar" "airtime_194_tar")
ubuntu_versions=("ubuntu_lucid_32" "ubuntu_natty_32")


num1=${#ubuntu_versions[@]}
num2=${#airtime_versions[@]}


for i in $(seq 0 $(($num1 -1)));
do
    for j in $(seq 0 $(($num2 -1)));
    do
        echo fab -f fab_setup.py ${ubuntu_versions[$i]} ${airtime_versions[$j]} $target 2>&1 | tee $LOG
        fab -f fab_setup.py ${ubuntu_versions[$i]} ${airtime_versions[$j]} $target 2>&1 | tee $LOG
    done
done
