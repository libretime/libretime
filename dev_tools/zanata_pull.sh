#!/bin/bash -e


# Pull new LibreTime translations from zanato.org and merge them properly

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
zanata-cli pull

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
    msgmerge -N -U --no-wrap ${targetFile} ${poFile}
done
