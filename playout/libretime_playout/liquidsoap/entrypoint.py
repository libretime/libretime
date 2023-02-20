from pathlib import Path
from typing import Optional, Tuple

from jinja2 import DictLoader, Environment
from libretime_shared.config import AudioFormat, IcecastOutput, SystemOutput

from ..config import Config
from .models import Info, StreamPreferences
from .utils import quote

here = Path(__file__).parent

templates_loader = DictLoader(
    {"entrypoint.liq.j2": (here / "entrypoint.liq.j2").read_text(encoding="utf-8")}
)
templates = Environment(  # nosec
    loader=templates_loader,
    keep_trailing_newline=True,
)
templates.filters["quote"] = quote

# Liquidsoap has 4 hardcoded output stream set of variables, so we need to
# fill the missing stream outputs with placeholders so Liquidsoap does
# not fail with missing variables in the entrypoint.
_icecast_placeholder = IcecastOutput(
    enabled=False,
    mount="",
    source_password="",
    audio=dict(format="ogg", bitrate=256),
)

_system_placeholder = SystemOutput()


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
    missing_outputs = [_icecast_placeholder] * (4 - len(config.stream.outputs.merged))
    config.stream.outputs.icecast.extend(missing_outputs)

    if not config.stream.outputs.system:
        config.stream.outputs.system.append(_system_placeholder)

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
