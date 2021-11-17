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
        "pypo.notify",
        "liquidsoap",
    ],
    package_data={"": ["**/*.liq", "*.cfg", "*.types"]},
    entry_points={
        "console_scripts": [
            "libretime-playout=pypo.main:run",
            "libretime-liquidsoap=liquidsoap.main:run",
            "libretime-playout-notify=pypo.notify.main:run",
        ]
    },
    python_requires=">=3.6",
    install_requires=[
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
