from setuptools import setup
from subprocess import call
import sys
import os

# Change directory since setuptools uses relative paths
script_path = os.path.dirname(os.path.realpath(__file__))
print(script_path)
os.chdir(script_path)

no_init = False
run_postinst = False

# Allows us to avoid installing the upstart init script when deploying libretime_import
if '--no-init-script' in sys.argv:
    no_init = True
    data_files = []
    sys.argv.remove('--no-init-script') # super hax
else:
    run_postinst = True
    data_files = [('/etc/init', ['install/upstart/libretime_import.conf']),
                  ('/etc/init.d', ['install/sysvinit/libretime_import']),
                  ('/etc/systemd/system', ['install/systemd/libretime_import.service']),
                  ('/srv/airtime/stor/uploads',[])]
    print(data_files)
def postinst():
    if not no_init:
        # Make /etc/init.d file executable and set proper
        # permissions for the defaults config file
        os.chmod('/etc/init.d/libretime_import', 0o755)
        os.chmod('/srv/airtime/stor/uploads', 0o777)

setup(name='libretime_import',
      version='0.2',
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
          'python-daemon',
          'pyinotify',
          'urllib3'
      ],
      zip_safe=False,
      data_files=data_files)
if run_postinst:
    postinst()
# Remind users to reload the initctl config so that "service start libretime_import" works
if data_files:
    print("Remember to reload the initctl configuration")
    print("Run \"sudo initctl reload-configuration; sudo service libretime_import restart\" now.")
    print("Or on Ubuntu Xenial (16.04)")
    print("Remember to reload the systemd configuration")
    print("Run \"sudo systemctl daemon-reload; sudo service libretime_import restart\" now.")
