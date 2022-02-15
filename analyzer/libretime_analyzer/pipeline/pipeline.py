from typing import List

from loguru import logger
from typing_extensions import Protocol

from .analyze_cuepoint import analyze_cuepoint
from .analyze_metadata import analyze_metadata
from .analyze_playability import analyze_playability
from .analyze_replaygain import analyze_replaygain
from .context import Context, Status
from .exceptions import PipelineError, StepError
from .organise_file import organise_file


class Step(Protocol):
    @staticmethod
    def __call__(ctx: Context) -> Context:
        ...


def run_pipeline(ctx: Context) -> Context:
    """
    Run each pipeline step on the incoming audio file.

    If a `Step` raise a `StepError`, the pipeline will stop and the error will be
    reported back to the API.

    If a `Step` raise a `PipelineError`, the pipeline will stop and the analyzer
    reject the message. User should take action to fix any `PipelineError`, such as
    missing executables or else.
    """
    steps: List[Step] = [
        analyze_playability,
        analyze_metadata,
        analyze_cuepoint,
        analyze_replaygain,
        organise_file,
    ]

    if not ctx.filepath.is_file():
        raise PipelineError(f"invalid or missing file {ctx.filepath}")

    try:
        for step in steps:
            ctx = step(ctx)

        ctx.status = Status.SUCCEED

    except StepError as exception:
        ctx.status = Status.FAILED
        ctx.error = str(exception)
        logger.warning(exception)

    return ctx
