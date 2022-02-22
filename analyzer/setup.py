from os import chdir
from pathlib import Path

from setuptools import setup

# Change directory since setuptools uses relative paths
here = Path(__file__).parent.resolve()
chdir(here)

setup(
    name="libretime-analyzer",
    version="0.1",
    description="Libretime Analyzer",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="AGPLv3",
    packages=[
        "libretime_analyzer",
        "libretime_analyzer.pipeline",
    ],
    entry_points={
        "console_scripts": [
            "libretime-analyzer=libretime_analyzer.main:cli",
        ]
    },
    python_requires=">=3.6",
    install_requires=[
        "mutagen>=1.45.1",
        "pika>=1.0.0",
        "requests>=2.7.0",
        "typing_extensions",
    ],
    extras_require={
        "dev": [
            "distro",
            f"libretime-shared @ file://localhost{here.parent / 'shared'}",
        ],
    },
    zip_safe=False,
)
