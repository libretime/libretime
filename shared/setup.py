from setuptools import find_packages, setup

setup(
    name="libretime-shared",
    version="3.0.1",
    description="LibreTime Shared",
    url="https://github.com/libretime/libretime",
    author="LibreTime Contributors",
    license="AGPLv3",
    packages=find_packages(exclude=["*tests*", "*fixtures*"]),
    package_data={"": ["py.typed"]},
    install_requires=[
        "backports.zoneinfo>=0.2.1,<0.3;python_version<'3.9'",
        "click>=8.0.4,<8.2",
        "loguru==0.6.0",
        "pydantic>=1.7.4,<1.11",
        "pyyaml>=5.3.1,<6.1",
    ],
    extras_require={
        "dev": [
            "types-backports>=0.1.3,<0.2",
            "types-pyyaml>=5.3.1,<6.1",
        ],
    },
)
