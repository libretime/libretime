from os import chdir
from pathlib import Path

from setuptools import setup

# Change directory since setuptools uses relative paths
here = Path(__file__).parent
chdir(here)

setup(
    name="libretime-console",
    version="0.1",
    description="Libretime Console",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="AGPLv3",
    packages=["libretime_console"],
    entry_points={
        "console_scripts": [
            "libretime=libretime_console.main:cli",
        ]
    },
    install_requires=[
        "click",
    ],
    zip_safe=False,
)
