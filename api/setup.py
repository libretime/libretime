from setuptools import find_packages, setup

setup(
    name="libretime-api",
    version="3.0.0",
    description="LibreTime API",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="AGPLv3",
    packages=find_packages(exclude=["*tests*", "*fixtures*"]),
    package_data={
        "libretime_api": ["legacy/migrations/sql/*.sql"],
    },
    include_package_data=True,
    python_requires=">=3.8",
    entry_points={
        "console_scripts": [
            "libretime-api=libretime_api.manage:main",
        ]
    },
    install_requires=[
        "django-filter>=2.4.0,<22.2",
        "django>=4.1.4,<4.2",
        "djangorestframework>=3.12.1,<3.15",
        "drf-spectacular>=0.22.1,<0.26",
        "requests>=2.25.1,<2.29",
    ],
    extras_require={
        "prod": [
            "psycopg2>=2.8.6,<2.10",
        ],
        "dev": [
            "django-coverage-plugin",
            "django-stubs",
            "djangorestframework-stubs",
            "model_bakery",
            "psycopg2-binary",
            "pylint-django",
            "pytest-django",
            "requests-mock",
        ],
    },
)
