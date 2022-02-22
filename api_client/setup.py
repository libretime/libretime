from os import chdir
from pathlib import Path

from setuptools import setup

# Change directory since setuptools uses relative paths
here = Path(__file__).parent.resolve()
chdir(here)

setup(
    name="libretime-api-client",
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
    packages=["libretime_api_client"],
    python_requires=">=3.6",
    install_requires=[
        "python-dateutil>=2.7.0",
        "requests",
    ],
    extras_require={
        "dev": [
            f"libretime-shared @ file://localhost{here.parent / 'shared'}",
        ],
    },
    zip_safe=False,
)
