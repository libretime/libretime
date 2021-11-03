import os

from setuptools import setup

# Change directory since setuptools uses relative paths
os.chdir(os.path.dirname(os.path.realpath(__file__)))

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
    packages=["airtime-celery"],
    python_requires=">=3.6",
    install_requires=[
        "celery==4.4.7",
        "kombu==5.2.0",
        "configobj",
    ],
    zip_safe=False,
)
