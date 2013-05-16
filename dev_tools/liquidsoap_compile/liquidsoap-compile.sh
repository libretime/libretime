#!/bin/bash -e
apt-get install -y --force-yes lsb-release sudo
dist=`lsb_release -is`
code=`lsb_release -cs`
cpu=`getconf LONG_BIT`
cpuvalue=

#enable apt.sourcefabric.org source
set +e
grep -E "deb http://apt.sourcefabric.org $code main" /etc/apt/sources.list
returncode=$?
set -e
if [ "$returncode" -ne "0" ]; then
   echo "deb http://apt.sourcefabric.org $code main" >> /etc/apt/sources.list
fi
apt-get update
apt-get -y --force-yes install sourcefabric-keyring
apt-get update



if [ "$dist" = "Ubuntu" ]; then
    set +e
    grep -E "deb http://ca.archive.ubuntu.com/ubuntu/ $code multiverse" /etc/apt/sources.list
    returncode=$?
    set -e
    if [ "$returncode" -ne "0" ]; then
        echo "deb http://ca.archive.ubuntu.com/ubuntu/ $code multiverse" >> /etc/apt/sources.list
        echo "deb http://ca.archive.ubuntu.com/ubuntu/ $code universe" >> /etc/apt/sources.list
    fi
fi

#enable squeeze backports to get lame packages
if [ "$dist" = "Debian" -a "$code" = "squeeze" ]; then
    set +e
    grep -E "deb http://backports.debian.org/debian-backports squeeze-backports main" /etc/apt/sources.list
    returncode=$?
    set -e
    if [ "$returncode" -ne "0" ]; then
        echo "deb http://backports.debian.org/debian-backports squeeze-backports main" >> /etc/apt/sources.list
    fi
fi

echo "System is $cpu bit..."
if [ "$cpu" = "64" ]; then
    cpuvalue="amd64"
else
    cpuvalue="i386"
fi

apt-get update
apt-get -o Dpkg::Options::="--force-confold" upgrade
apt-get -y --force-yes install libopus0 libopus-dev libopus-dbg libopus-doc
#obsoleted code start
#apt-get -y --force-yes install wget
#rm -f libopu*
#rm -f aacplus*
#wget http://apt.sourcefabric.org/misc/libopus_1.0.1/libopus-dbg_1.0.1~$code~sfo-1_$cpuvalue.deb
#wget http://apt.sourcefabric.org/misc/libopus_1.0.1/libopus-dev_1.0.1~$code~sfo-1_$cpuvalue.deb
#wget http://apt.sourcefabric.org/misc/libopus_1.0.1/libopus0_1.0.1~$code~sfo-1_$cpuvalue.deb
#wget http://packages.medibuntu.org/pool/free/a/aacplusenc/aacplusenc_0.17.5-0.0medibuntu1_$cpuvalue.deb
#obsoleted code end

apt-get -y --force-yes install git-core ocaml-findlib libao-ocaml-dev \
libportaudio-ocaml-dev libmad-ocaml-dev libtaglib-ocaml-dev libalsa-ocaml-dev \
libvorbis-ocaml-dev libladspa-ocaml-dev libxmlplaylist-ocaml-dev libflac-dev \
libxml-dom-perl libxml-dom-xpath-perl patch autoconf libmp3lame-dev \
libcamomile-ocaml-dev libcamlimages-ocaml-dev libtool libpulse-dev camlidl \
libfaad-dev libpcre-ocaml-dev libfftw3-3 dialog

if [ "$code" != "lucid" ]; then
    apt-get -y --force-yes install libvo-aacenc-dev
fi

#dpkg -i libopus-dbg_1.0.1~$code~sfo-1_$cpuvalue.deb libopus-dev_1.0.1~$code~sfo-1_$cpuvalue.deb libopus0_1.0.1~$code~sfo-1_$cpuvalue.deb aacplusenc_0.17.5-0.0medibuntu1_$cpuvalue.deb

#for aac+
#rm -rf libaac*
#apt-get -y --force-yes install libfftw3-dev pkg-config autoconf automake libtool unzip
#wget http://217.20.164.161/~tipok/aacplus/libaacplus-2.0.2.tar.gz
#tar -xzf libaacplus-2.0.2.tar.gz
#cd libaacplus-2.0.2
#./autogen.sh --enable-shared --enable-static
#make
#make install
#ldconfig
#cd ..
#end of aac+

rm -rf liquidsoap-full
git clone https://github.com/savonet/liquidsoap-full
cd liquidsoap-full
git checkout master 
make init
make update

#tmp
#cd liquidsoap
#git checkout ifdef-encoder
#git merge master
#cd ..
#tmp end

cp PACKAGES.minimal PACKAGES

sed -i "s/#ocaml-portaudio/ocaml-portaudio/g" PACKAGES
sed -i "s/#ocaml-alsa/ocaml-alsa/g" PACKAGES
sed -i "s/#ocaml-pulseaudio/ocaml-pulseaudio/g" PACKAGES
sed -i "s/#ocaml-faad/ocaml-faad/g" PACKAGES
sed -i "s/#ocaml-opus/ocaml-opus/g" PACKAGES
#sed -i "s/#ocaml-aacplus/ocaml-aacplus/g" PACKAGES
#sed -i "s/#ocaml-shine/ocaml-shine/g" PACKAGES
if [ "$code" != "lucid" ]; then
    sed -i "s/#ocaml-voaacenc/ocaml-voaacenc/g" PACKAGES
fi

chown -R tmp /liquidsoap-full
chmod -R 777 /liquidsoap-full
sudo -u tmp ./bootstrap
sudo -u tmp ./configure
sudo -u tmp make
cp /liquidsoap-full/liquidsoap/src/liquidsoap /
