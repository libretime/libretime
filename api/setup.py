from os import chdir
from pathlib import Path

from setuptools import find_packages, setup

# Change directory since setuptools uses relative paths
here = Path(__file__).parent.resolve()
chdir(here)


setup(
    name="libretime-api",
    version="2.0.0a1",
    description="LibreTime API",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="AGPLv3",
    packages=find_packages(),
    package_data={
        "libretime_api": ["legacy/migrations/sql/*.sql"],
    },
    include_package_data=True,
    python_requires=">=3.6",
    entry_points={
        "console_scripts": [
            "libretime-api=libretime_api.manage:main",
        ]
    },
    install_requires=[
        "coreapi~=2.3.3",
        "django~=3.2.13",
        "djangorestframework~=3.13.1",
        "django-filter~=21.1.0",
        "drf-spectacular~=0.22.1",
        "markdown",
    ],
    extras_require={
        "prod": [
            "psycopg2",
        ],
        "dev": [
            "django-stubs",
            "djangorestframework-stubs",
            "model_bakery",
            "psycopg2-binary",
            "pylint-django",
            "pytest-django",
            f"libretime-shared @ file://localhost/{here.parent / 'shared'}#egg=libretime_shared",
        ],
    },
)
