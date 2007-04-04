#!/usr/bin/env python
# -*- coding: utf-8 -*-
#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the Campcaster project.
#   http://campcaster.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   Campcaster is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   Campcaster is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with Campcaster; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $URL$
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#   This script converts an ICU localization file from Serbian Latin
#   to Serbian Cyrillic.
#-------------------------------------------------------------------------------

import sys, re, codecs

usageString = 'Usage: serbianLatinToCyrillicConverter.py' \
              ' inputfile outputfile'

if len(sys.argv) >= 3:
    fileNameIn  = sys.argv[1]
    fileNameOut = sys.argv[2]
else:
    print usageString
    sys.exit(1)

oldLines = codecs.open(fileNameIn, 'r', 'utf-8').readlines()
newLines = [ ]

def cyrillize(word):
    if re.match(r'#.*#\Z', word):
        return word
    
    compound = { u'lj' : u'љ', u'Lj' : u'Љ',
                 u'nj' : u'њ', u'Nj' : u'Њ',
                 u'dž' : u'џ', u'Dž' : u'Џ' }
    simple = dict(zip(u'abvgdđežzijklmnoprstćufhcčšw',
                      u'абвгдђежзијклмнопрстћуфхцчшв'))
    simple.update(dict(zip(u'ABVGDĐEŽZIJKLMNOPRSTĆUFHCČŠW',
                           u'АБВГДЂЕЖЗИЈКЛМНОПРСТЋУФХЦЧШВ')))
    exceptions = { ur'\н'       :   ur'\n',
                   u'Фаде ин'   :   u'Фејд ин',
                   u'Фаде оут'  :   u'Фејд аут',
                   u'фаде ин'   :   u'фејд ин',
                   u'фаде оут'  :   u'фејд аут',
                   u'есцапе'    :   u'ескејп',
                   u'Плаy'      :   u'Плеј',
                   u'Паусе'     :   u'Поуз',
                   u'трацк'     :   u'трак',
                   u'УРИ'       :   u'URI',
                   u'РДС'       :   u'RDS',
                   u'БПМ'       :   u'BPM',
                   u'ИСРЦ'      :   u'ISRC' }
    
    for latin, cyrillic in compound.iteritems():
        word = word.replace(latin, cyrillic)
    for latin, cyrillic in simple.iteritems():
        word = word.replace(latin, cyrillic)
    for bad, good in exceptions.iteritems():
        word = word.replace(bad, good)
    
    return word

for line in oldLines:
    m = re.match(r'(.*)"(.*)"(.*)\n', line)
    if m:
        line = m.groups()[0] + '"' \
             + cyrillize(m.groups()[1]) + '"' \
             + m.groups()[2] + '\n'
    
    elif line == 'sr_CS:table\n':
        line = 'sr_CS_CYRILLIC:table\n'
    
    newLines += [line]

codecs.open(fileNameOut, 'w', 'utf-8').writelines(newLines)
