from typing import List

from loguru import logger
from typing_extensions import Protocol

from .analyze_cuepoint import analyze_cuepoint
from .analyze_metadata import analyze_metadata
from .analyze_playability import analyze_playability
from .analyze_replaygain import analyze_replaygain
from .context import Context, Status
from .organise_file import organise_file


class Step(Protocol):
    @staticmethod
    def __call__(ctx: Context) -> Context:
        ...


def run_pipeline(ctx: Context) -> Context:
    steps: List[Step] = [
        analyze_playability,
        analyze_metadata,
        analyze_cuepoint,
        analyze_replaygain,
        organise_file,
    ]

    try:
        for step in steps:
            try:
                ctx = step(ctx)
            except Exception as exception:
                logger.warning(exception)
                ctx.status = Status.failed
                ctx.error = str(exception)

        ctx.status = Status.succeed
        return ctx
    except Exception as exception:
        logger.error(exception)
        raise
