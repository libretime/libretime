from setuptools import find_packages, setup

setup(
    name="libretime-api",
    version="3.0.2",
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
        "django-cors-headers>=3.14.0,<4.1",
        "django-filter>=2.4.0,<23.3",
        "django>=4.2.0,<4.3",
        "djangorestframework @ git+https://github.com/encode/django-rest-framework@38a74b42da10576857d6bf8bd82a73b15d12a7ed",
        "drf-spectacular>=0.22.1,<0.27",
        "requests>=2.31.0,<2.32",
    ],
    extras_require={
        "prod": [
            "gunicorn>=20.1.0,<20.2",
            "psycopg[c]>=3.1.8,<3.2",
            "uvicorn[standard]>=0.17.6,<0.23.0",
        ],
        "dev": [
            "django-coverage-plugin>=3.0.0,<3.1",
            "django-stubs>=1.14.0,<4.3",
            "djangorestframework-stubs>=1.8.0,<3.15",
            "model_bakery>=1.10.1,<1.12",
            "psycopg[binary]>=3.1.8,<3.2",
            "pylint-django>=2.5.3,<2.6",
            "pytest-django>=4.5.2,<4.6",
            "requests-mock>=1.10.0,<1.11",
        ],
        "sentry": [
            "sentry-sdk[django]>=1.15.0,<1.24",
        ],
    },
)
