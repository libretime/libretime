import os

from setuptools import setup

# Change directory since setuptools uses relative paths
os.chdir(os.path.dirname(os.path.realpath(__file__)))

setup(
    name="airtime-celery",
    version="0.1",
    description="Airtime Celery service",
    url="http://github.com/sourcefabric/Airtime",
    author="Sourcefabric",
    author_email="duncan.sommerville@sourcefabric.org",
    license="MIT",
    packages=["airtime-celery"],
    install_requires=[
        "celery==4.4.7",
        "kombu==4.6.10",
        "configobj",
    ],
    zip_safe=False,
)
