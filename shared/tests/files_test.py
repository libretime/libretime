from pathlib import Path

from libretime_shared.files import compute_md5


def test_compute_md5(tmp_path: Path) -> None:
    tmp_file = tmp_path / "somefile.txt"
    tmp_file.write_text("some test")

    assert compute_md5(tmp_file) == "f1b75ac7689ff88e1ecc40c84b115785"
