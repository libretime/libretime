from setuptools import find_packages, setup

setup(
    name="libretime-api",
    version="3.2.0",
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
        "django-cors-headers>=3.14.0,<4.4",
        "django-filter>=2.4.0,<23.4",
        "django>=4.2.0,<4.3",
        "djangorestframework>=3.14.0,<3.15",
        "drf-spectacular>=0.22.1,<0.28",
        "requests>=2.31.0,<2.32",
    ],
    extras_require={
        "prod": [
            "gunicorn>=20.1.0,<21.3",
            "psycopg[c]>=3.1.8,<3.2",
            "uvicorn[standard]>=0.17.6,<0.24.0",
        ],
        "dev": [
            "django-coverage-plugin>=3.0.0,<4",
            "django-stubs>=1.14.0,<5",
            "djangorestframework-stubs>=1.8.0,<4",
            "model_bakery>=1.10.1,<2",
            "psycopg[binary]>=3.1.8,<4",
            "pylint-django>=2.5.3,<3",
            "pytest-django>=4.5.2,<5",
            "requests-mock>=1.10.0,<2",
        ],
        "sentry": [
            "sentry-sdk[django]>=1.15.0,<2",
        ],
    },
)
