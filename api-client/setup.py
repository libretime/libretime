from setuptools import find_packages, setup

version = "4.1.0"  # x-release-please-version

setup(
    name="libretime-api-client",
    version=version,
    description="LibreTime API Client",
    author="LibreTime Contributors",
    url="https://github.com/libretime/libretime",
    project_urls={
        "Bug Tracker": "https://github.com/libretime/libretime/issues",
        "Documentation": "https://libretime.org",
        "Source Code": "https://github.com/libretime/libretime",
    },
    license="AGPLv3",
    packages=find_packages(exclude=["*tests*", "*fixtures*"]),
    package_data={"": ["py.typed"]},
    python_requires=">=3.8",
    install_requires=[
        "python-dateutil>=2.8.1,<2.10",
        "requests>=2.32.2,<2.33",
    ],
    extras_require={
        "dev": [
            "requests-mock>=1.10.0,<2",
            "types-python-dateutil>=2.8.1,<3",
            "types-requests>=2.31.0,<3",
        ],
    },
    zip_safe=False,
)
