#!/bin/bash -e
apt-get install -y --force-yes lsb-release sudo
dist=`lsb_release -is`
code=`lsb_release -cs`
cpu=`getconf LONG_BIT`
cpuvalue=

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
apt-get install wget
wget http://apt.sourcefabric.org/misc/libopus_1.0.1/libopus-dbg_1.0.1~$code~sfo-1_$cpuvalue.deb
wget http://apt.sourcefabric.org/misc/libopus_1.0.1/libopus-dev_1.0.1~$code~sfo-1_$cpuvalue.deb
wget http://apt.sourcefabric.org/misc/libopus_1.0.1/libopus0_1.0.1~$code~sfo-1_$cpuvalue.deb

apt-get -y --force-yes install git-core ocaml-findlib libao-ocaml-dev \
libportaudio-ocaml-dev libmad-ocaml-dev libtaglib-ocaml-dev libalsa-ocaml-dev \
libvorbis-ocaml-dev libladspa-ocaml-dev libxmlplaylist-ocaml-dev libflac-dev \
libxml-dom-perl libxml-dom-xpath-perl patch autoconf libmp3lame-dev \
libcamomile-ocaml-dev libcamlimages-ocaml-dev libtool libpulse-dev camlidl \
libfaad-dev libpcre-ocaml-dev

dpkg -i libopus-dbg_1.0.1~$code~sfo-1_$cpuvalue.deb libopus-dev_1.0.1~$code~sfo-1_$cpuvalue.deb libopus0_1.0.1~$code~sfo-1_$cpuvalue.deb 
rm -rf liquidsoap-full
git clone https://github.com/savonet/liquidsoap-full
chmod -R 777 liquidsoap-full
cd liquidsoap-full
sudo -u tmp make init
sudo -u tmp make update

sudo -u tmp cp PACKAGES.minimal PACKAGES

sed -i "s/#ocaml-portaudio/ocaml-portaudio/g" PACKAGES
sed -i "s/#ocaml-alsa/ocaml-alsa/g" PACKAGES
sed -i "s/#ocaml-pulseaudio/ocaml-pulseaudio/g" PACKAGES
sed -i "s/#ocaml-faad/ocaml-faad/g" PACKAGES
sed -i "s/#ocaml-opus/ocaml-opus/g" PACKAGES
#sed -i "s/#ocaml-shine/ocaml-shine/g" PACKAGES

sudo -u tmp ./bootstrap
sudo -u tmp ./configure
sudo -u tmp make
cp /liquidsoap-full/liquidsoap/src/liquidsoap /
