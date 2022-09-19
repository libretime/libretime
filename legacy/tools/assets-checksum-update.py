#!/usr/bin/env python3

# Extract the checksum for all the assets in the public directory.
import hashlib
import json
from pathlib import Path

legacy_root = Path(__file__).parent.parent

scan_dir = legacy_root / "public"
dest_file = legacy_root / "application/assets.json"
result = {}


def compute_md5(filepath: Path) -> str:
    with filepath.open("rb") as file:
        buffer = hashlib.md5()  # nosec
        while True:
            blob = file.read(8192)
            if not blob:
                break
            buffer.update(blob)

        return buffer.hexdigest()


def compute_md5_foreach_asset_in(root: Path):
    for path in root.iterdir():
        if path.is_dir():
            compute_md5_foreach_asset_in(path)
        else:
            if path.suffix in (".js", ".css"):
                result_key = str(path)[len(str(scan_dir)) + 1 :]
                result[result_key] = compute_md5(path)


compute_md5_foreach_asset_in(scan_dir)
dest_file.write_text(json.dumps(dict(sorted(result.items())), indent=2) + "\n")
