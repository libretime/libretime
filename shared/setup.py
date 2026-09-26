from setuptools import find_packages, setup

version = "5.0.0"  # x-release-please-version

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
        "click>=8.0.4,<8.6",
        "pydantic>=2.5.0,<2.14",
        "pyyaml>=5.3.1,<6.1",
    ],
    extras_require={
        "dev": [
            "types-pyyaml>=5.3.1,<7",
        ],
    },
)
