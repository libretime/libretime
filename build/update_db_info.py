"""
The purpose of this script is to consolidate into one location where 
we need to update database host, dbname, username and password.

This script reads from database.conf.
"""
import os
import ConfigParser
import xml.dom.minidom
from xml.dom.minidom import Node

#Read the universal values
parser = ConfigParser.SafeConfigParser()
parser.read('airtime.conf')
section_names = parser.sections();
items_in_section = parser.items(section_names[0])


print "Updating ../application/configs/application.ini"
host = 'resources.db.params.host'
dbname = 'resources.db.params.dbname'
username = 'resources.db.params.username'
password = 'resources.db.params.password'

f = file('../application/configs/application.ini','r')
file_lines = []

for line in f:
    if line[0:len(host)] == host:
        line= '%s = "%s"\n' % (host, parser.get('database', 'host'))
    elif line[0:len(dbname)] == dbname:
        line= '%s = "%s"\n' % (dbname, parser.get('database', 'dbname'))
    elif line[0:len(username)] == username:
        line= '%s = "%s"\n' % (username, parser.get('database', 'dbuser'))
    elif line[0:len(password)] == password:
        line= '%s = "%s"\n' % (password, parser.get('database', 'dbpass'))
    file_lines.append(line)
f.close()

f = file('../application/configs/application.ini', 'w')
f.writelines(file_lines)
f.close()


print "Updating ./build.properties"

f = file('build.properties', 'r')
file_lines = []

db_url = 'propel.database.url'

for line in f:
    if line[0:len(db_url)] == db_url:
        line = '%s = pgsql:host=%s dbname=%s user=%s password=%s\n' % \
        (db_url, parser.get('database', 'host'), parser.get('database', 'dbname'), parser.get('database', 'dbuser'), \
        parser.get('database', 'dbpass'))
    file_lines.append(line)
f.close()

f = file('build.properties', 'w')
f.writelines(file_lines)
f.close()

print "Updating ./runtime-conf.xml"

doc = xml.dom.minidom.parse('./runtime-conf.xml')

node = doc.getElementsByTagName("dsn")[0]
node.firstChild.nodeValue = 'pgsql:host=%s;port=5432;dbname=%s;user=%s;password=%s' % (parser.get('database', 'host'), \
parser.get('database', 'dbname'), parser.get('database', 'dbuser'), parser.get('database', 'dbpass'))

xml_file = open('runtime-conf.xml', "w")
xml_file.writelines(doc.toxml('utf-8'))
xml_file.close()

print 'Regenerating propel-config.php'
os.system('../library/propel/generator/bin/propel-gen')

