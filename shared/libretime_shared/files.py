import hashlib
from pathlib import Path


def compute_md5(filepath: Path) -> str:
    """
    Compute a file md5sum.
    """
    with filepath.open("rb") as file:
        buffer = hashlib.md5()  # nosec
        while True:
            blob = file.read(8192)
            if not blob:
                break
            buffer.update(blob)

        return buffer.hexdigest()
