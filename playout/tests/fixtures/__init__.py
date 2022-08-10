from pathlib import Path

fixtures_path = Path(__file__).parent

icecast_stats = fixtures_path / "icecast_stats.xml"
shoutcast_admin = fixtures_path / "shoutcast_admin.xml"

entrypoint_1_1 = fixtures_path / "entrypoint-1.1.liq"
entrypoint_1_1_snapshot = entrypoint_1_1.read_text(encoding="utf-8")
entrypoint_1_4 = fixtures_path / "entrypoint-1.4.liq"
entrypoint_1_4_snapshot = entrypoint_1_4.read_text(encoding="utf-8")
