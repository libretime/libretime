import os

from setuptools import setup

# Change directory since setuptools uses relative paths
os.chdir(os.path.dirname(os.path.realpath(__file__)))

setup(
    name="api_clients",
    version="2.0.0",
    description="LibreTime API Client",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="AGPLv3",
    packages=["api_clients"],
    python_requires=">=3.6",
    install_requires=[
        "configobj",
        "python-dateutil>=2.7.0",
        "requests",
    ],
    zip_safe=False,
)
