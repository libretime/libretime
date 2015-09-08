#! /bin/bash

cd ..

#generate a new .po file
#this will generate a file called messages.po
find airtime_mvc -iname "*.phtml" -o -name "*.php" | xargs xgettext -L php --from-code=UTF-8
find airtime_mvc -iname "*.phtml" -o -name "*.php" | xargs xgettext -L php --from-code=UTF-8 -k --keyword=_pro:1 -d pro --force-po

#merge the new messages from messages.po into each existing .po file
#this will generate new .po files
find ./airtime_mvc/locale/ -name "airtime.po" -exec msgmerge -N -U --no-wrap "{}" messages.po \;
find ./airtime_mvc/locale/ -name "pro.po" -exec msgmerge -N -U --no-wrap "{}" pro.po \;

#delete the old .po files
find ./airtime_mvc/locale/ -name "*.po~" -delete

#delete the temporary po files we create in the root directory
rm ./*.po
