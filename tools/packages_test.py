from pathlib import Path

from tools.packages import list_packages, load_packages

PACKAGE_INI = """
[common]
postgresql = noble, resolute
# Some comment
curl = trixie, resolute

[legacy]
some-package = noble, trixie

[=development]
ffmpeg = noble, trixie, resolute
"""

result_resolute = {"curl", "postgresql"}
result_trixie = {"some-package", "curl", "ffmpeg"}
result_noble = {"postgresql", "some-package", "ffmpeg"}
result_exclude = {"postgresql", "ffmpeg"}


def test_load_packages():
    assert load_packages(PACKAGE_INI, "resolute", False) == result_resolute
    assert load_packages(PACKAGE_INI, "trixie", True) == result_trixie
    assert load_packages(PACKAGE_INI, "noble", True) == result_noble
    assert load_packages(PACKAGE_INI, "noble", True, ["legacy"]) == result_exclude


def test_list_packages(tmp_path: Path) -> None:
    package_file = tmp_path / "packages.ini"
    package_file.write_text(PACKAGE_INI)

    assert list_packages([tmp_path, package_file], "resolute", False) == result_resolute
    assert list_packages([tmp_path, package_file], "trixie", True) == result_trixie
    assert list_packages([tmp_path, package_file], "noble", True) == result_noble
