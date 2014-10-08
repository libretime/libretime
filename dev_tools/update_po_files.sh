#! /bin/bash

cd ..

#generate a new .po file
#this will generate a file called messages.po
find . -iname "*.phtml" -o -name "*.php" | xargs xgettext -L php --from-code=UTF-8

#merge the new messages from messages.po into each existing .po file
#this will generate new .po files
find ./airtime_mvc/locale/ -name "*.po" -exec msgmerge -N -U --no-wrap "{}" messages.po \;

#delete the old .po files
find ./airtime_mvc/locale/ -name "*.po~" -delete
