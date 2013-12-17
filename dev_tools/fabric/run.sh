#!/bin/bash

exec 2>&1

target="airtime_git_branch:2.5.x"
#target="airtime_git_branch:airtime-2.0.0-RC1"
#airtime_versions=("")
airtime_versions=("airtime_241_tar")
ubuntu_versions=("ubuntu_lucid_32" "ubuntu_lucid_64" "ubuntu_precise_32" "ubuntu_precise_64" "ubuntu_quantal_32" "ubuntu_quantal_64" "ubuntu_raring_32" "ubuntu_raring_64" "debian_squeeze_32" "debian_squeeze_64" "debian_wheezy_32" "debian_wheezy_64" "ubuntu_saucy_32" "ubuntu_saucy_64")
#ubuntu_versions=("ubuntu_saucy_64" "ubuntu_saucy_32")

num1=${#ubuntu_versions[@]}
num2=${#airtime_versions[@]}
upgrade_log_folder="upgrade_logs"
rm -rf ./upgrade_logs
mkdir -p ./upgrade_logs

for i in $(seq 0 $(($num1 -1)));
do
    #echo fab -f fab_setup.py os_update shutdown
    for j in $(seq 0 $(($num2 -1)));
    do
        #since 2.5.0 airtime start to support saucy, before that, we don't need to test on those combinations
        platform=`echo ${ubuntu_versions[$i]} | awk '/(saucy)/'`
        airtime=`echo ${airtime_versions[$j]} | awk '/2[2-4][0-3]/'`
        if [ "$platform" = "" ] || [ "$airtime" = "" ];then
            echo fab -f fab_release_test.py ${ubuntu_versions[$i]} ${airtime_versions[$j]} $target shutdown
            fab -f fab_release_test.py ${ubuntu_versions[$i]} ${airtime_versions[$j]} $target shutdown 2>&1 | tee "./$upgrade_log_folder/${ubuntu_versions[$i]}_${airtime_versions[$j]}_$target.log"
            #touch "./$upgrade_log_folder/${ubuntu_versions[$i]}_${airtime_versions[$j]}_$target.log"
            tail -20 "./$upgrade_log_folder/${ubuntu_versions[$i]}_${airtime_versions[$j]}_$target.log" | grep -E "Your installation of Airtime looks OK"
            returncode=$?
            if [ "$returncode" -ne "0" ]; then
                mv "./$upgrade_log_folder/${ubuntu_versions[$i]}_${airtime_versions[$j]}_$target.log" "./$upgrade_log_folder/fail_${ubuntu_versions[$i]}_${airtime_versions[$j]}_$target.log"
            fi
        fi
    done
done

