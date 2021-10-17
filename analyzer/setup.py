import os

from setuptools import setup

# Change directory since setuptools uses relative paths
os.chdir(os.path.dirname(os.path.realpath(__file__)))

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
    packages=["airtime_analyzer"],
    entry_points={
        "console_scripts": [
            "libretime-analyzer=airtime_analyzer.cli:main",
        ]
    },
    python_requires=">=3.6",
    install_requires=[
        "mutagen>=1.31.0",
        "pika>=1.0.0",
        "file-magic",
        "requests>=2.7.0",
        "rgain3==1.1.1",
        "PyGObject>=3.34.0",
        # If this version is changed, it needs changing in the install script too
        "pycairo==1.19.1",
    ],
    extras_require={
        "dev": [
            "distro",
        ],
    },
    zip_safe=False,
)
