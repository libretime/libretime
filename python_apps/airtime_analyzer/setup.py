import os

from setuptools import setup

# Change directory since setuptools uses relative paths
os.chdir(os.path.dirname(os.path.realpath(__file__)))

setup(
    name="libretime-analyzer",
    version="0.1",
    description="Libretime Analyzer Worker and File Importer",
    url="https://libretime.org",
    author="LibreTime Contributors",
    license="AGPLv3",
    packages=["airtime_analyzer"],
    entry_points={
        "console_scripts": [
            "libretime-analyzer=airtime_analyzer.cli:main",
        ]
    },
    install_requires=[
        "mutagen==1.42.0",
        "pika>=1.0.0",
        "file-magic",
        "requests>=2.7.0",
        "rgain3==1.1.0",
        "pycairo==1.19.1",
    ],
    zip_safe=False,
)
