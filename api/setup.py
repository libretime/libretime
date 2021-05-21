import os
import shutil
from setuptools import setup, find_packages

script_path = os.path.dirname(os.path.realpath(__file__))
print(script_path)
os.chdir(script_path)

setup(
    name='libretime-api',
    version='2.0.0a1',
    packages=find_packages(),
    include_package_data=True,
    description='LibreTime API backend server',
    url='https://github.com/LibreTime/libretime',
    author='LibreTime Contributors',
    scripts=['bin/libretime-api'],
    install_requires=[
        'coreapi',
        'Django~=3.0',
        'djangorestframework',
        'django-url-filter',
        'markdown',
        'model_bakery',
        'psycopg2',
    ],
    project_urls={
        'Bug Tracker': 'https://github.com/LibreTime/libretime/issues',
        'Documentation': 'https://libretime.org',
        'Source Code': 'https://github.com/LibreTime/libretime',
    },
)
