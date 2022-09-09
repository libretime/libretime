from pathlib import Path

from tools.packages import list_packages, load_packages

PACKAGE_INI = """
[common]
postgresql = buster, focal
# Some comment
curl = buster, bullseye

[legacy]
some-package = bullseye, focal

[=development]
ffmpeg = buster, bullseye, focal
"""

result_buster = {"curl", "postgresql"}
result_bullseye = {"some-package", "curl", "ffmpeg"}
result_focal = {"postgresql", "some-package", "ffmpeg"}
result_exclude = {"postgresql", "ffmpeg"}


def test_load_packages():
    assert load_packages(PACKAGE_INI, "buster", False) == result_buster
    assert load_packages(PACKAGE_INI, "bullseye", True) == result_bullseye
    assert load_packages(PACKAGE_INI, "focal", True) == result_focal
    assert load_packages(PACKAGE_INI, "focal", True, ["legacy"]) == result_exclude


def test_list_packages(tmp_path: Path) -> None:
    package_file = tmp_path / "packages.ini"
    package_file.write_text(PACKAGE_INI)

    assert list_packages([tmp_path, package_file], "buster", False) == result_buster
    assert list_packages([tmp_path, package_file], "bullseye", True) == result_bullseye
    assert list_packages([tmp_path, package_file], "focal", True) == result_focal
