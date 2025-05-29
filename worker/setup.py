from setuptools import find_packages, setup

version = "4.4.0"  # x-release-please-version

setup(
    name="libretime-worker",
    version=version,
    description="LibreTime Worker",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="MIT",
    packages=find_packages(exclude=["*tests*", "*fixtures*"]),
    entry_points={
        "console_scripts": [
            "libretime-worker=libretime_worker.main:cli",
        ]
    },
    python_requires=">=3.8",
    install_requires=[
        "celery==4.4.7",
        "kombu==4.6.11",
        "mutagen>=1.45.1,<1.48",
        "requests>=2.32.2,<2.33",
    ],
    extras_require={
        "dev": [
            "requests-mock>=1.10.0,<2",
            "types-requests>=2.31.0,<3",
        ],
        "sentry": [
            "sentry-sdk>=1.15.0,<2",
        ],
    },
    zip_safe=False,
)
