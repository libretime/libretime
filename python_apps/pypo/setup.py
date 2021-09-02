import os

from setuptools import setup

# Change directory since setuptools uses relative paths
os.chdir(os.path.dirname(os.path.realpath(__file__)))


setup(
    name="airtime-playout",
    version="1.0",
    description="Airtime Playout Engine",
    url="http://github.com/sourcefabric/Airtime",
    author="sourcefabric",
    license="AGPLv3",
    packages=[
        "pypo",
        "pypo.media",
        "pypo.media.update",
        "liquidsoap",
    ],
    package_data={"": ["**/*.liq", "*.cfg", "*.types"]},
    scripts=[
        "bin/airtime-playout",
        "bin/airtime-liquidsoap",
        "bin/pyponotify",
    ],
    install_requires=[
        "amqplib",
        "anyjson",
        "argparse",
        "configobj",
        "docopt",
        "future",
        "kombu",
        "mutagen",
        "PyDispatcher",
        "pyinotify",
        "pytz",
        "requests",
        "defusedxml",
        "packaging",
    ],
    zip_safe=False,
)
