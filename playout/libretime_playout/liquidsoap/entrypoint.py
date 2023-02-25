from pathlib import Path
from typing import Optional, Tuple

from jinja2 import Environment, PackageLoader
from libretime_shared.config import AudioFormat

from ..config import Config
from .models import Info, StreamPreferences
from .utils import quote

here = Path(__file__).parent

templates_loader = PackageLoader(__name__, "templates")
templates = Environment(  # nosec
    loader=templates_loader,
    keep_trailing_newline=True,
)
templates.filters["quote"] = quote


def generate_entrypoint(
    entrypoint_filepath: Path,
    log_filepath: Optional[Path],
    config: Config,
    preferences: StreamPreferences,
    info: Info,
    version: Tuple[int, int, int],
):
    paths = {}
    paths["auth_filepath"] = here / "liquidsoap_auth.py"
    paths["lib_filepath"] = here / f"{version[0]}.{version[1]}/ls_script.liq"

    if log_filepath is not None:
        paths["log_filepath"] = log_filepath.resolve()

    config = config.copy()

    # Global icecast_vorbis_metadata until it is
    # handled per output
    icecast_vorbis_metadata = any(
        o.enabled and o.audio.format == AudioFormat.OGG and o.audio.enable_metadata  # type: ignore
        for o in config.stream.outputs.icecast
    )

    entrypoint_filepath.write_text(
        templates.get_template("entrypoint.liq.j2").render(
            config=config,
            preferences=preferences,
            info=info,
            paths=paths,
            version=version,
            icecast_vorbis_metadata=icecast_vorbis_metadata,
        ),
        encoding="utf-8",
    )
