from setuptools import setup
from subprocess import call
import sys
import os

# Change directory since setuptools uses relative paths
script_path = os.path.dirname(os.path.realpath(__file__))
print script_path
os.chdir(script_path)
#todo need to figure out why its only going into /usr/local/bin vs /usr/bin when installed

# Allows us to avoid installing the upstart init script when deploying libretime_import
if '--no-init-script' in sys.argv:
    data_files = []
    sys.argv.remove('--no-init-script') # super hax
else:
    data_files = [('/etc/init', ['install/upstart/libretime_import.conf']),
                  ('/etc/init.d', ['install/sysvinit/libretime_import'])]
    print data_files

setup(name='libretime_import',
      version='0.1',
      description='Libretime Automatic Folder Import',
      url='http://github.com/libretime/LibreTime',
      author='Robb Ebright',
      author_email='robbt@azone.org',
      license='MIT',
      packages=['libretime_import'],
      scripts=['bin/libretime_import'],
      console_scripts=['bin/libretime_import'],
      install_requires=[
          'daemon',
          'file-magic',
          'nose',
          'coverage',
          'mock',
          'python-daemon==1.6',
          'requests>=2.7.0',
          'pyinotify'
      ],
      zip_safe=False,
      data_files=data_files)

# Remind users to reload the initctl config so that "service start airtime_analyzer" works
if data_files:
    print "Remember to reload the initctl configuration"
    print "Run \"sudo initctl reload-configuration; sudo service libretime_import restart\" now."
    print "Or on Ubuntu Xenial (16.04)"
    print "Remember to reload the systemd configuration"
    print "Run \"sudo systemctl daemon-reload; sudo service libretime_import restart\" now."
