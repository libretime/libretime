#!/bin/sh
#
# description: removes silence from the beginning and the end
#              of a file
# version: 20050807-3
# usage: ecatrimsilence.sh <inputfile>

tmp=ecatrimsilence-tmp.wav
ECASOUND=ecasound

if test -e $tmp ; then
  echo "error: temp file $tmp exists, unable to continue..."
  exit 1
fi

if test ! -e $1 ; then
  echo "error: input file $1 does not exist, unable to continue..."
  exit 2
fi

format=`ecalength -sf $1`

echo "Trimming file ${1}."
echo "Removing silence at the end..."
$ECASOUND -q -f:${format} -i reverse,${1} -o ${tmp} -ge:1,0,1 -b:64
rm -f ${1}
echo "Removing silence at the beginning..."
$ECASOUND -q -f:${format} -i reverse,${tmp} -o ${1} -ge:1,0,1 -b:64
rm -f ${tmp}
echo "Done."
