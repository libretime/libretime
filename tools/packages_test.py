from pathlib import Path

from tools.packages import list_packages, load_packages

PACKAGE_INI = """
[common]
postgresql = buster, focal
# Some comment
curl = buster, bionic

[legacy]
apache2 = bionic, focal

[=development]
ffmpeg = buster, bionic, focal
"""

result_buster = {"curl", "postgresql"}
result_bionic = {"apache2", "curl", "ffmpeg"}
result_focal = {"postgresql", "apache2", "ffmpeg"}
result_exclude = {"postgresql", "ffmpeg"}


def test_load_packages():
    assert load_packages(PACKAGE_INI, "buster", False) == result_buster
    assert load_packages(PACKAGE_INI, "bionic", True) == result_bionic
    assert load_packages(PACKAGE_INI, "focal", True) == result_focal
    assert load_packages(PACKAGE_INI, "focal", True, ["legacy"]) == result_exclude


def test_list_packages(tmp_path: Path) -> None:
    package_file = tmp_path / "packages.ini"
    package_file.write_text(PACKAGE_INI)

    assert list_packages([tmp_path, package_file], "buster", False) == result_buster
    assert list_packages([tmp_path, package_file], "bionic", True) == result_bionic
    assert list_packages([tmp_path, package_file], "focal", True) == result_focal
