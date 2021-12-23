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
        "libretime_playout",
        "libretime_playout.notify",
        "libretime_liquidsoap",
    ],
    package_data={"": ["**/*.liq", "*.cfg", "*.types"]},
    entry_points={
        "console_scripts": [
            "libretime-playout=libretime_playout.main:run",
            "libretime-liquidsoap=libretime_liquidsoap.main:run",
            "libretime-playout-notify=libretime_playout.notify.main:run",
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
