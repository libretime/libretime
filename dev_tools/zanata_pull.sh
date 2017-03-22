#!/bin/bash -e

# Pull new LibreTime translations from zanato.org and merge them properly
#
# You need to run this on a box with a working zanata-cli install and the
# vagrant setup does not seem to be such an environment. A manual install
# gave me a version that actually exports umlauts on macOS, but now there
# is the issue of ~/Downloads/zanata-cli-4.1.1/bin/zanata-cli being on my
# ZANATA_BIN path for this to work.

# Map for mapping from zanata languages to local names
declare -A localeMap
localeMap=(
    ["cs"]="cs_CZ"
    ["da"]="da_DK"
    ["el"]="el_GR"
    ["fr"]="fr_FR"
    ["hr"]="hr_HR"
    ["hu"]="hu_HU"
    ["id"]="id_ID"
    ["it"]="it_IT"
    ["ko"]="ko_KR"
    ["nl"]="nl_NL"
    ["pl"]="pl_PL"
    ["ro"]="ro_RO"
    ["ru"]="ru_RU"
    ["sr-Cyrl"]="sr_RS"
    ["sr-Latn"]="sr_RS@latin"
)

# Actual call to zanata, you need to configure you ~/.config/zanata.ini beforehand
${ZANATA_BIN:-zanata-cli} pull

# merge each of the downloaded file as needed
for poFile in `find build/locale/po -name "airtime.po"`; do
    locale=$(basename $(dirname $poFile))
    mappedLocale="${localeMap[$locale]}"
    if [ $mappedLocale ]; then
        locale=$mappedLocale;
    fi
    targetFile="airtime_mvc/locale/${locale//-/_}/LC_MESSAGES/airtime.po"

    if [ ! -f ${targetFile} ]; then
        echo "Missing ${targetFile}" >&2
        exit 1
    fi
    cp ${poFile} ${targetFile}
    msgfmt -o ${targetFile/%po/mo} ${targetFile}
done
