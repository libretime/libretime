#! /bin/bash

cd ..

#generate a new .po file
#this will generate a file called messages.po
find legacy -print0 -iname "*.phtml" -o -name "*.php" | xargs xgettext -L php --from-code=UTF-8
find legacy -print0 -iname "*.phtml" -o -name "*.php" | xargs xgettext -L php --from-code=UTF-8 -k --keyword=_pro:1 -d pro --force-po

#merge the new messages from messages.po into each existing .po file
#this will generate new .po files
find ./legacy/locale/ -name "airtime.po" -exec msgmerge -N -U --no-wrap "{}" messages.po \;
find ./legacy/locale/ -name "pro.po" -exec msgmerge -N -U --no-wrap "{}" pro.po \;

#delete the old .po files
find ./legacy/locale/ -name "*.po~" -delete

#delete the temporary po files we create in the root directory
rm ./*.po
