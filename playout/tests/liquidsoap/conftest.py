import pytest

from libretime_playout.liquidsoap.version import get_liquidsoap_version


@pytest.fixture(
    name="liq_version",
    scope="session",
)
def liq_version_fixture():
    try:
        version = get_liquidsoap_version()
    except FileNotFoundError:
        pytest.skip("liquidsoap is not installed")

    return version


@pytest.fixture(
    name="liq_version_param",
    scope="session",
    params=[
        pytest.param((2, 1), id="2.1"),
    ],
)
def liq_version_param_fixture(request, liq_version):
    """
    Fixture to only run the test when the current liquisoap version equals the fixture
    value, otherwise the test is skipped.

    E.g the current distro is bookworm, all test for the other distro will be skipped.
    """
    if request.param != liq_version[0:2]:
        pytest.skip(f"liquisoap {request.param} is not installed")

    return liq_version
