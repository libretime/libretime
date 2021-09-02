import os

from setuptools import setup

# Change directory since setuptools uses relative paths
os.chdir(os.path.dirname(os.path.realpath(__file__)))

setup(
    name="api_clients",
    version="2.0.0",
    description="LibreTime API Client",
    url="http://github.com/LibreTime/Libretime",
    author="LibreTime Contributors",
    license="AGPLv3",
    packages=["api_clients"],
    install_requires=[
        "configobj",
        "python-dateutil>=2.7.0",
        "requests",
    ],
    zip_safe=False,
)
