from setuptools import find_packages, setup

setup(
    name="libretime-playout",
    version="3.1.0",
    description="LibreTime Playout",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="AGPLv3",
    packages=find_packages(exclude=["*tests*", "*fixtures*"]),
    package_data={"": ["**/*.liq", "**/*.liq.j2", "*.types"]},
    entry_points={
        "console_scripts": [
            "libretime-playout=libretime_playout.main:cli",
            "libretime-liquidsoap=libretime_playout.liquidsoap.main:cli",
            "libretime-playout-notify=libretime_playout.notify.main:cli",
        ]
    },
    python_requires=">=3.8",
    install_requires=[
        "backports.zoneinfo>=0.2.1,<0.3;python_version<'3.9'",
        "jinja2>=3.0.3,<3.2",
        "kombu==4.6.11",
        "lxml>=4.5.0,<4.10.0",
        "mutagen>=1.45.1,<1.47",
        "python-dateutil>=2.8.1,<2.9",
        "requests>=2.31.0,<2.32",
        "typing-extensions",
    ],
    extras_require={
        "dev": [
            "distro>=1.8.0,<1.9",
            "requests-mock>=1.10.0,<1.12",
            "syrupy>=4.0.0,<4.3",
            "types-backports>=0.1.3,<0.2",
            "types-python-dateutil>=2.8.1,<2.9",
            "types-requests>=2.31.0,<2.32",
        ],
        "sentry": [
            "sentry-sdk>=1.15.0,<1.30",
        ],
    },
    zip_safe=False,
)
