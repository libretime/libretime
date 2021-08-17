from pathlib import Path

from .packages import list_packages, load_packages

package_ini = """
[common]
postgresql = buster
# Some comment
curl = buster, bionic

[legacy]
apache2 = bionic
"""


def test_load_packages():
    assert load_packages(package_ini, "buster") == {"curl", "postgresql"}
    assert load_packages(package_ini, "bionic") == {"apache2", "curl"}


def test_list_packages(tmp_path: Path):
    package_file = tmp_path / "packages.ini"
    package_file.write_text(package_ini)

    assert list_packages([tmp_path, package_file], "buster") == {"curl", "postgresql"}
    assert list_packages([tmp_path, package_file], "bionic") == {"apache2", "curl"}
