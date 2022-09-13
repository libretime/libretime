from setuptools import find_packages, setup

setup(
    name="libretime-api-client",
    version="3.0.0-beta.0",
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
    python_requires=">=3.6",
    install_requires=[
        "python-dateutil>=2.8.1,<2.9",
        "requests>=2.25.1,<2.29",
    ],
    extras_require={
        "dev": [
            "requests-mock",
            "types-python-dateutil",
            "types-requests",
        ],
    },
    zip_safe=False,
)
