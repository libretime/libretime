from os import chdir
from pathlib import Path

from setuptools import setup

# Change directory since setuptools uses relative paths
here = Path(__file__).parent
chdir(here)

setup(
    name="libretime-playout",
    version="1.0",
    description="LibreTime Playout",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="AGPLv3",
    packages=[
        "pypo",
        "liquidsoap",
    ],
    package_data={"": ["**/*.liq", "*.cfg", "*.types"]},
    scripts=[
        "bin/libretime-playout",
        "bin/libretime-liquidsoap",
        "bin/libretime-playout-notify",
    ],
    python_requires=">=3.6",
    install_requires=[
        f"libretime-api-client @ file://localhost/{here.parent}/api_clients#egg=libretime-api-client",
        "amqplib",
        "configobj",
        "defusedxml",
        "kombu",
        "mutagen",
        "packaging",
        "pytz",
        "requests",
    ],
    zip_safe=False,
)
