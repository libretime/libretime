from pathlib import Path

from tools.packages import list_packages, load_packages

PACKAGE_INI = """
[common]
postgresql = buster
# Some comment
curl = buster, bionic

[legacy]
apache2 = bionic

[=development]
ffmpeg = buster, bionic
"""

result1 = {"curl", "postgresql"}
result2 = {"apache2", "curl", "ffmpeg"}


def test_load_packages():
    assert load_packages(PACKAGE_INI, "buster", False) == result1
    assert load_packages(PACKAGE_INI, "bionic", True) == result2


def test_list_packages(tmp_path: Path):
    package_file = tmp_path / "packages.ini"
    package_file.write_text(PACKAGE_INI)

    assert list_packages([tmp_path, package_file], "buster", False) == result1
    assert list_packages([tmp_path, package_file], "bionic", True) == result2
