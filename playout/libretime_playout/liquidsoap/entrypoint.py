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

    entrypoint_filepath.write_text(
        templates.get_template("entrypoint.liq.j2").render(
            config=config.copy(),
            preferences=preferences,
            info=info,
            paths=paths,
            version=version,
        ),
        encoding="utf-8",
    )
