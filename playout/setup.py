from setuptools import find_packages, setup

setup(
    name="libretime-playout",
    version="3.0.0",
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
    python_requires=">=3.6",
    install_requires=[
        "backports.zoneinfo>=0.2.1,<0.3;python_version<'3.9'",
        "dataclasses>=0.8,<0.9;python_version<'3.7'",
        "jinja2>=3.0.3,<3.2",
        "kombu==4.6.11",
        "lxml>=4.5.0,<4.10.0",
        "mutagen>=1.45.1,<1.47",
        "python-dateutil>=2.8.1,<2.9",
        "requests>=2.25.1,<2.29",
        "typing-extensions",
    ],
    extras_require={
        "dev": [
            "distro",
            "requests-mock",
            "syrupy",
            "types-backports",
            "types-python-dateutil",
            "types-requests",
        ],
    },
    zip_safe=False,
)
