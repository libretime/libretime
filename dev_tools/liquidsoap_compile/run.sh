#!/bin/bash

exec 2>&1

ROOT_UID="0"

#Check if run as root
if [ "$UID" -ne "$ROOT_UID" ] ; then
        echo "You must have 'sudo' right to do that!"
        exit 1
fi

rm -rf ./liquidsoap-compile_logs
mkdir -p ./liquidsoap-compile_logs

showhelp () {
    echo "Usage: run.sh [options] [parameters]
-c all|ubuntu_lucid_32    Compile liquidsoap on all platforms or specified platform.
-b all|ubuntu_lucid_32    Build shroot environments for all platforms or specified platform.
-u username               Local username will be used as sudo user of chroot env. Must be assigned before -b options"
    exit 0
}

build_env () {
    if [ $sudo_user = "-1" ];then
        echo "Please use -u to assign sudo username before build environments."
        exit 1
    fi
  
    echo "build_env $1"
    #exec > >(tee ./liquidsoap_compile_logs/build_env_$1.log)
    os=`echo $1 | awk '/(debian)/'`
    cpu=`echo $1 | awk '/(64)/'`
    dist=`echo $1 | awk -F "_" '{print $2}'`

    rm -f /etc/schroot/chroot.d/$1.conf
    if cat /etc/passwd | awk -F:'{print $1}' | grep "tmp" >/dev/null 2>&1;then
        echo "User tmp exists."
    else
        useradd tmp
	echo "User tmp is created."
    fi
    
    apt-get update
    apt-get --force-yes -y install debootstrap dchroot
    echo [$1] > /etc/schroot/chroot.d/$1.conf
    echo description=$1 >> /etc/schroot/chroot.d/$1.conf
    echo directory=/srv/chroot/$1 >> /etc/schroot/chroot.d/$1.conf
    echo type=directory >> /etc/schroot/chroot.d/$1.conf
    echo users=$sudo_user,tmp >> /etc/schroot/chroot.d/$1.conf
    echo root-users=$sudo_user >> /etc/schroot/chroot.d/$1.conf
    rm -rf /srv/chroot/$1
    mkdir -p /srv/chroot/$1

    #cp liquidsoap_compile.sh /srv/chroot/$1/
    if [ "$os" = "" ];then
       if [ "$cpu" = "" ];then
           echo "debootstrap --variant=buildd --arch=i386 $dist /srv/chroot/$1 http://archive.ubuntu.com/ubuntu/"
           debootstrap --variant=buildd --arch=i386 $dist /srv/chroot/$1 http://archive.ubuntu.com/ubuntu/
       else
           echo "debootstrap --variant=buildd --arch=amd64 $dist /srv/chroot/$1 http://archive.ubuntu.com/ubuntu/"
           debootstrap --variant=buildd --arch=amd64 $dist /srv/chroot/$1 http://archive.ubuntu.com/ubuntu/
       fi
    else
       if [ "$cpu" = "" ];then
           echo "debootstrap --variant=buildd --arch=i386 $dist /srv/chroot/$1 http://ftp.debian.com/debian/"
           debootstrap --variant=buildd --arch=i386 $dist /srv/chroot/$1 http://ftp.debian.com/debian/
       else
           echo "debootstrap --variant=buildd --arch=amd64 $dist /srv/chroot/$1 http://ftp.debian.com/debian/"
           debootstrap --variant=buildd --arch=amd64 $dist /srv/chroot/$1 http://ftp.debian.com/debian/
       fi
    fi

}

compile_liq () {
    echo "complie_liq $1"
    #exec > >(tee ./liquidsoap_compile_logs/compile_liq_$1.log)
    binfilename=`echo $1 | sed -e 's/ubuntu/liquidsoap/g' -e 's/debian/liquidsoap/g' -e 's/32/i386/g' -e 's/64/amd64/g'`
    rm -f /srv/chroot/$1/liquidsoap-compile.sh
    rm -f /srv/chroot/$1/liquidsoap
    cp liquidsoap-compile.sh /srv/chroot/$1/
    schroot -c $1 -u root -d / -- /liquidsoap-compile.sh
    cp /srv/chroot/$1/liquidsoap ./$binfilename
    if [ $? = 0 ];then
        echo "$binfilename is generated successfully"
    else
        mv ./liquidsoap-compile_logs/compile_liq_$1.log ./liquidsoap-compile_logs/fail_to_compile_liq_$1.log
    fi
}  

os_versions=("ubuntu_lucid_32" "ubuntu_lucid_64" "ubuntu_precise_32" "ubuntu_precise_64" "ubuntu_quantal_32" "ubuntu_quantal_64" "ubuntu_raring_32" "ubuntu_raring_64" "debian_squeeze_32" "debian_squeeze_64" "debian_wheezy_32" "debian_wheezy_64")

num=${#os_versions[@]}
flag=
os=
sudo_user="-1"

if [ x$1 = x ];then
    showhelp
fi

while getopts b:c:u: arg
do
    case $arg in
        b)
	    if [ "$OPTARG" = "all" ];then
	        echo "Building all platforms on server..."
                for i in $(seq 0 $(($num -1)));
                do
		    build_env ${os_versions[$i]} | tee ./liquidsoap-compile_logs/build_env_${os_versions[$i]}.log
		done
	    else
	        flag=1
		for i in $(seq 0 $(($num -1)));
		do
		    if [ "$OPTARG" = ${os_versions[$i]} ];then
		        echo "Building platform: $OPTARG ..."
                        build_env ${os_versions[$i]} | tee ./liquidsoap-compile_logs/build_env_${os_versions[$i]}.log
                        flag=0
		    fi
		done
	        if [ $flag = 1 ];then
	            echo "Unsupported Platform from:"
                    for j in "${os_versions[@]}"
                    do
                        echo $j
		    done
                    exit 1
                fi
	    fi
            ;;
	 c)
	     if [ "$OPTARG" = "all" ];then
                 echo "Compiling liquidsoap for all platforms on server..."
                 for i in $(seq 0 $(($num -1)))
                 do
                     compile_liq ${os_versions[$i]} | tee ./liquidsoap-compile_logs/compile_liq_${os_versions[$i]}.log
                 done

             else
	         flag=1
                 for i in $(seq 0 $(($num -1)));
                 do
                     if [ "$OPTARG" = ${os_versions[$i]} ];then
                         echo "Compiling liquidsoap for platform: $OPTARG ..."
                         compile_liq ${os_versions[$i]} | tee ./liquidsoap-compile_logs/compile_liq_${os_versions[$i]}.log
                         flag=0
                     fi
                 done    
                 if [ $flag = 1 ];then
                     echo "Unsupported Platform from:"
                     for k in "${os_versions[@]}"
                     do
                         echo $k
                     done
                     exit 1
                 fi
             fi
             ;;
          u)
             sudo_user="$OPTARG"
             echo "sudo_user is set as $sudo_user."
             ;;
	  ?)
	     showhelp
	     ;;
    esac
done

