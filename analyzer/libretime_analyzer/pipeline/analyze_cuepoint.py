from datetime import timedelta
from math import isclose
from subprocess import CalledProcessError

from loguru import logger

from libretime_analyzer.pipeline.exceptions import PipelineError

from ._ffmpeg import compute_silences, probe_duration
from .context import Context


def analyze_cuepoint(ctx: Context) -> Context:
    """
    Extracts the cuein and cueout and sets the file duration using ffmpeg.
    """
    try:
        duration = probe_duration(ctx.filepath)

        if "length_seconds" in ctx.metadata and not isclose(
            ctx.metadata["length_seconds"],
            duration,
            abs_tol=0.1,
        ):
            logger.warning(
                f"existing duration {ctx.metadata['length_seconds']} differs "
                f"from the probed duration {duration}."
            )

        ctx.metadata["length_seconds"] = duration
        ctx.metadata["length"] = str(timedelta(seconds=duration))
        ctx.metadata["cuein"] = 0.0
        ctx.metadata["cueout"] = duration

    except (CalledProcessError, FileNotFoundError) as exception:
        raise PipelineError(
            f"could not probe duration for {ctx.filepath}"
        ) from exception

    try:
        silences = compute_silences(ctx.filepath)

        if len(silences) > 2:
            # Only keep first and last silence
            silences = silences[:: len(silences) - 1]

        for silence in silences:
            # Sanity check
            if silence[0] >= silence[1]:
                raise ValueError(
                    f"silence starts ({silence[0]}) after ending ({silence[1]})"
                )

            # Is this really the first silence ?
            if isclose(
                0.0,
                max(0.0, silence[0]),  # Clamp negative value
                abs_tol=0.1,
            ):
                ctx.metadata["cuein"] = max(0.0, silence[1])

            # Is this really the last silence ?
            elif isclose(
                min(silence[1], duration),  # Clamp infinity value
                duration,
                abs_tol=0.1,
            ):
                ctx.metadata["cueout"] = min(silence[0], duration)

        ctx.metadata["cuein"] = format(ctx.metadata["cuein"], "f")
        ctx.metadata["cueout"] = format(ctx.metadata["cueout"], "f")

    except (CalledProcessError, FileNotFoundError) as exception:
        raise PipelineError(
            f"could not compute silences for {ctx.filepath}"
        ) from exception

    return ctx
