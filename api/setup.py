import os

from setuptools import find_packages, setup

# Change directory since setuptools uses relative paths
os.chdir(os.path.dirname(os.path.realpath(__file__)))

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
    include_package_data=True,
    python_requires=">=3.6",
    entry_points={
        "console_scripts": [
            "libretime-api=libretimeapi.cli:main",
        ]
    },
    install_requires=[
        "coreapi",
        "django>=3,<5",
        "djangorestframework",
        "django-filter",
        "drf-spectacular",
        "markdown",
        "model_bakery",
    ],
    extras_require={
        "prod": [
            "psycopg2",
        ],
        "dev": [
            "psycopg2-binary",
        ],
    },
)
