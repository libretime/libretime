from setuptools import setup, find_packages

TEST_REQUIRES = ["ConfigObj>=4.5.3", "nose>=0.10"]


setup(
    name = "SoundCloudAPI",
    version = "0.1",
    packages = find_packages(),
    author = "Diez B. Roggisch",
    author_email = "deets@web.de",
    description = "This is an implementation of the SoundCloud RESTful API",
    license = "MIT",
    keywords = "Soundcloud client API REST",
    url = "http://wiki.github.com/soundcloud/api/python-api-wrapper/",
    install_requires = ['simplejson'] + TEST_REQUIRES,
#     tests_require = TEST_REQUIRES,
#     extras_require = dict(
#         test = TEST_REQUIRES
#         ),
    test_suite = 'nose.collector'
)
