from setuptools import find_packages, setup

setup(
    name="libretime-analyzer",
    version="3.0.2",
    description="Libretime Analyzer",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="AGPLv3",
    packages=find_packages(exclude=["*tests*", "*fixtures*"]),
    entry_points={
        "console_scripts": [
            "libretime-analyzer=libretime_analyzer.main:cli",
        ]
    },
    python_requires=">=3.6",
    install_requires=[
        "mutagen>=1.45.1,<1.47",
        "pika>=1.0.0,<1.4",
        "requests>=2.25.1,<2.31",
        "typing_extensions",
    ],
    extras_require={
        "dev": [
            "distro",
            "types-requests",
        ],
    },
    zip_safe=False,
)
