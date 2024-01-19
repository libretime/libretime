import dataclasses
from pathlib import Path
from typing import Optional, Tuple

from jinja2 import Environment, PackageLoader

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


@dataclasses.dataclass
class InfoVersion:
    info: Info
    version: Tuple[int, int, int]

    def __init__(self, inf: Info, ver: Tuple[int, int, int]):
        self.info = inf
        self.version = ver


def generate_entrypoint(
    log_filepath: Optional[Path],
    hls_output_path: Path,
    config: Config,
    preferences: StreamPreferences,
    infoversion: InfoVersion,
) -> str:
    info = infoversion.info
    version = infoversion.version
    paths = {}
    paths["hls_output_path"] = hls_output_path.resolve()
    paths["lib_filepath"] = here / f"{version[0]}.{version[1]}/ls_script.liq"

    if log_filepath is not None:
        paths["log_filepath"] = log_filepath.resolve()

    return templates.get_template("entrypoint.liq.j2").render(
        config=config.model_copy(),
        preferences=preferences,
        info=info,
        paths=paths,
        version=version,
    )
