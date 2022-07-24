from os import chdir
from pathlib import Path

from setuptools import find_packages, setup

# Change directory since setuptools uses relative paths
here = Path(__file__).parent.resolve()
chdir(here)

setup(
    name="libretime-celery",
    version="0.1",
    description="LibreTime Celery",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="MIT",
    packages=find_packages(exclude=["*tests*", "*fixtures*"]),
    python_requires=">=3.6",
    install_requires=[
        "celery==4.4.7",
        "kombu==4.6.11",
        "mutagen>=1.45.1,<1.46",
        "requests>=2.25.1,<2.29",
    ],
    extras_require={
        "dev": [
            f"libretime-shared @ file://localhost{here.parent / 'shared'}",
        ],
    },
    zip_safe=False,
)
