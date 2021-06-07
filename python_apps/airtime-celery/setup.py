import os
import sys
from pathlib import Path
from subprocess import call

from setuptools import setup

# Change directory since setuptools uses relative paths
script_path = os.path.dirname(os.path.realpath(__file__))
print(script_path)
os.chdir(script_path)

install_args = ["install", "install_data", "develop"]
no_init = False
run_postinst = False

# XXX Definitely not the best way of doing this...
if sys.argv[1] in install_args and "--no-init-script" not in sys.argv:
    run_postinst = True
    data_files = [
        ("/etc/default", ["install/conf/airtime-celery"]),
        ("/etc/init.d", ["install/initd/airtime-celery"]),
    ]
else:
    if "--no-init-script" in sys.argv:
        no_init = True
        run_postinst = True  # We still want to run the postinst here
        sys.argv.remove("--no-init-script")
    data_files = []


def postinst():
    initd = Path("/etc/init.d/airtime-celery")
    conf = Path("/etc/default/airtime-celery")
    if not no_init and initd.is_file() and conf.is_file():
        # Make /etc/init.d file executable and set proper
        # permissions for the defaults config file
        os.chmod("/etc/init.d/airtime-celery", 0o755)
        os.chmod("/etc/default/airtime-celery", 0o640)
    print('Run "sudo service airtime-celery restart" now.')


setup(
    name="airtime-celery",
    version="0.1",
    description="Airtime Celery service",
    url="http://github.com/sourcefabric/Airtime",
    author="Sourcefabric",
    author_email="duncan.sommerville@sourcefabric.org",
    license="MIT",
    packages=["airtime-celery"],
    install_requires=["celery==5.1.0", "kombu==4.6.10", "configobj"],
    zip_safe=False,
    data_files=data_files,
)

if run_postinst:
    postinst()
