#!/bin/bash

# A bash script to convert all Rivendell's audio-library to MP3, extract meta-tags from Rivendell's
# database and set appropriate tags to each MP3 file.

# Notes:
# 1 - Rivendell store files in .wav format, airtime uses .mp3 format 
# 2 - WAV does not have Meta-tag support so all meta-tags need to be fetched from Rivendell database.


if [ $# -ne 2 ]; then
        echo "usage: $0 <rivendell_dir> <final_dir>"
        exit
fi

#*** MySql data ***#
user="INSERT_MYSQL_USERNAME_HERE"
pass="INSERT_MYSQL_PASSWORD_HERE"
db="Rivendell" #Edit this only if you changed Rivendell's database name :-)
#*** End ***#

rivendell_dir=$1
end_dir=$2

cd "$rivendell_dir"

for file in *
do
        lame "$file"
done

mv "$rivendell_dir"/*.mp3 "$end_dir"
cd "$end_dir"

for file in *
do
        id=`echo $file | head -c 10`
        title=`mysql -u $user -p$pass -sN -e "SELECT CU.DESCRIPTION FROM CUTS CU, CART CA WHERE CA.NUMBER=CU.CART_NUMBER AND CU.CUT_NAME=\"${id}\"" $db`
        artist=`mysql -u $user -p$pass -sN -e "SELECT CA.ARTIST FROM CUTS CU, CART CA WHERE CA.NUMBER=CU.CART_NUMBER AND CU.CUT_NAME=\"${id}\"" $db`
        album=`mysql -u $user -p$pass -sN -e "SELECT CA.ALBUM FROM CUTS CU, CART CA WHERE CA.NUMBER=CU.CART_NUMBER AND CU.CUT_NAME=\"${id}\"" $db`
        year=`mysql -u $user -p$pass -sN -e "SELECT CA.YEAR FROM CUTS CU, CART CA WHERE CA.NUMBER=CU.CART_NUMBER AND CU.CUT_NAME=\"${id}\"" $db`
        id3 -t "$title" -a "$artist" -A "$album" -y "$year" $file
        mv "$file" "$artist-$title.mp3"
done

exit
