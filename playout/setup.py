from os import chdir
from pathlib import Path

from setuptools import find_packages, setup

# Change directory since setuptools uses relative paths
here = Path(__file__).parent.resolve()
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
    packages=find_packages(exclude=["*tests*", "*fixtures*"]),
    package_data={"": ["**/*.liq", "*.types"]},
    entry_points={
        "console_scripts": [
            "libretime-playout=libretime_playout.main:cli",
            "libretime-liquidsoap=libretime_playout.liquidsoap.main:cli",
            "libretime-playout-notify=libretime_playout.notify.main:cli",
        ]
    },
    python_requires=">=3.6",
    install_requires=[
        "backports.zoneinfo>=0.2.1,<0.3;python_version<'3.9'",
        "defusedxml>=0.6.0,<0.8",
        "kombu==4.6.11",
        "mutagen>=1.45.1,<1.46",
        "requests>=2.25.1,<2.29",
        "typing-extensions",
    ],
    extras_require={
        "dev": [
            "distro",
            "requests-mock",
            f"libretime-api-client @ file://localhost{here.parent / 'api-client'}",
            f"libretime-shared @ file://localhost{here.parent / 'shared'}",
        ],
    },
    zip_safe=False,
)
