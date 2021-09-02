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
    scripts=["bin/libretime-api"],
    install_requires=[
        "coreapi",
        "Django~=3.0",
        "djangorestframework",
        "django-url-filter",
        "markdown",
        "model_bakery",
        "psycopg2",
    ],
)
