from os import chdir
from pathlib import Path

from setuptools import setup

here = Path(__file__).parent
chdir(here)

setup(
    name="libretime-shared",
    version="1.0.0",
    description="LibreTime Shared",
    url="http://github.com/libretime/libretime",
    author="LibreTime Contributors",
    license="AGPLv3",
    packages=["libretime_shared"],
    package_data={"": ["py.typed"]},
    install_requires=[
        "click",
        "loguru",
        "pydantic",
        "pyyaml",
    ],
    extras_require={
        "dev": [
            "types-pyyaml",
        ],
    },
)
