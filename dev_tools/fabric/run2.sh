#!/bin/bash

exec 2>&1

ubuntu_versions=("ubuntu_lucid_32" "ubuntu_lucid_64" "ubuntu_natty_32" "ubuntu_natty_64" "ubuntu_oneiric_32" "ubuntu_oneiric_64" "debian_squeeze_32" "debian_squeeze_64" "ubuntu_precise_32" "ubuntu_precise_64" "ubuntu_quantal_32" "ubuntu_quantal_64" "debian_wheezy_32" "debian_wheezy_64")

#ubuntu_versions=("ubuntu_quantal_64")
num1=${#ubuntu_versions[@]}

mkdir -p ./upgrade_logs2

for i in $(seq 0 $(($num1 -1)));
do
    
    binfilename=`echo ${ubuntu_versions[$i]} | sed -e 's/ubuntu/liquidsoap/g' -e 's/debian/liquidsoap/g' -e 's/32/i386/g' -e 's/64/amd64/g'`
    echo "fab -f fab_liquidsoap_compile.py ${ubuntu_versions[$i]} compile_liquidsoap:filename=$binfilename shutdown"
    fab -f fab_liquidsoap_compile.py ${ubuntu_versions[$i]} compile_liquidsoap:filename=$binfilename shutdown 2>&1 #| tee "./upgrade_logs2/${ubuntu_versions[$i]}.log"
done
