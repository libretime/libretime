"""
The purpose of this script is to consolidate into one location where 
we need to update database host, dbname, username and password.

This script reads from airtime.conf.
"""
import os
import sys
import ConfigParser
import xml.dom.minidom
from xml.dom.minidom import Node

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

#Read the universal values
parser = ConfigParser.SafeConfigParser()
parser.read('/etc/airtime/airtime.conf')

host = 'resources.db.params.host'
dbname = 'resources.db.params.dbname'
username = 'resources.db.params.username'
password = 'resources.db.params.password'

airtime_dir = parser.get('general', 'airtime_dir')
print 'Airtime root folder found at %s' % airtime_dir

print ("Updating %s/application/configs/application.ini" % airtime_dir)
f = file('%s/application/configs/application.ini' % airtime_dir,'r')
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

f = file('%s/application/configs/application.ini' % airtime_dir, 'w')
f.writelines(file_lines)
f.close()


print ("Updating %s/build.properties" % airtime_dir)

f = file('%s/build/build.properties' % airtime_dir, 'r')
file_lines = []

db_url = 'propel.database.url'

for line in f:
    if line[0:len(db_url)] == db_url:
        line = '%s = pgsql:host=%s dbname=%s user=%s password=%s\n' % \
        (db_url, parser.get('database', 'host'), parser.get('database', 'dbname'), parser.get('database', 'dbuser'), \
        parser.get('database', 'dbpass'))
    file_lines.append(line)
f.close()

f = file('%s/build/build.properties' % airtime_dir, 'w')
f.writelines(file_lines)
f.close()

print ("Updating %s/runtime-conf.xml" % airtime_dir)

doc = xml.dom.minidom.parse('%s/build/runtime-conf.xml' % airtime_dir)

node = doc.getElementsByTagName("dsn")[0]
node.firstChild.nodeValue = 'pgsql:host=%s;port=5432;dbname=%s;user=%s;password=%s' % (parser.get('database', 'host'), \
parser.get('database', 'dbname'), parser.get('database', 'dbuser'), parser.get('database', 'dbpass'))

xml_file = open('%s/build/runtime-conf.xml' % airtime_dir, "w")
xml_file.writelines(doc.toxml('utf-8'))
xml_file.close()

print 'Regenerating propel-config.php'
os.system('cd %s/build && %s/library/propel/generator/bin/propel-gen' % (airtime_dir, airtime_dir))

