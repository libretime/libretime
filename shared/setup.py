from setuptools import find_packages, setup

version = "4.2.0"  # x-release-please-version

setup(
    name="libretime-shared",
    version=version,
    description="LibreTime Shared",
    url="https://github.com/libretime/libretime",
    author="LibreTime Contributors",
    license="AGPLv3",
    packages=find_packages(exclude=["*tests*", "*fixtures*"]),
    package_data={"": ["py.typed"]},
    install_requires=[
        "backports.zoneinfo>=0.2.1,<0.3;python_version<'3.9'",
        "click>=8.0.4,<8.2",
        "pydantic>=2.5.0,<2.11",
        "pyyaml>=5.3.1,<6.1",
    ],
    extras_require={
        "dev": [
            "types-backports>=0.1.3,<1",
            "types-pyyaml>=5.3.1,<7",
        ],
    },
)
