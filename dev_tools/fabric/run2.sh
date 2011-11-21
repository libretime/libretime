#!/bin/bash

exec 2>&1

ubuntu_versions=("debian_squeeze_32")

num1=${#ubuntu_versions[@]}

mkdir -p ./upgrade_logs2

for i in $(seq 0 $(($num1 -1)));
do
    fab -f fab_liquidsoap_compile.py ${ubuntu_versions[$i]} compile_liquidsoap:filename=${ubuntu_versions[$i]} shutdown 2>&1 | tee "./upgrade_logs2/${ubuntu_versions[$i]}.log"
done
