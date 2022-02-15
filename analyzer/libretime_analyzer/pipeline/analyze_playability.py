from subprocess import CalledProcessError

from ._liquidsoap import _liquidsoap
from .context import Context
from .exceptions import PipelineError, StepError


def analyze_playability(ctx: Context) -> Context:
    """
    Checks if a file can be played by liquidsoap.
    """
    try:
        _liquidsoap(
            "--verbose",
            "--check",
            "output.dummy(audio_to_stereo(single(argv(1))))",
            "--",
            str(ctx.filepath),
        )

    except CalledProcessError as exception:  # liquidsoap returned an error
        raise StepError("The file could not be played by liquidsoap.") from exception

    except FileNotFoundError as exception:
        raise PipelineError(
            f"could not analyze playability for {ctx.filepath}"
        ) from exception

    return ctx
