from pathlib import Path

from tools.packages import list_packages, load_packages

PACKAGE_INI = """
[common]
postgresql = jammy, focal
# Some comment
curl = jammy, bullseye

[legacy]
some-package = bullseye, focal

[=development]
ffmpeg = jammy, bullseye, focal
"""

result_jammy = {"curl", "postgresql"}
result_bullseye = {"some-package", "curl", "ffmpeg"}
result_focal = {"postgresql", "some-package", "ffmpeg"}
result_exclude = {"postgresql", "ffmpeg"}


def test_load_packages():
    assert load_packages(PACKAGE_INI, "jammy", False) == result_jammy
    assert load_packages(PACKAGE_INI, "bullseye", True) == result_bullseye
    assert load_packages(PACKAGE_INI, "focal", True) == result_focal
    assert load_packages(PACKAGE_INI, "focal", True, ["legacy"]) == result_exclude


def test_list_packages(tmp_path: Path) -> None:
    package_file = tmp_path / "packages.ini"
    package_file.write_text(PACKAGE_INI)

    assert list_packages([tmp_path, package_file], "jammy", False) == result_jammy
    assert list_packages([tmp_path, package_file], "bullseye", True) == result_bullseye
    assert list_packages([tmp_path, package_file], "focal", True) == result_focal
