from subprocess import CalledProcessError

from libretime_analyzer.pipeline.exceptions import PipelineError

from ._ffmpeg import compute_replaygain, probe_replaygain
from .context import Context


def analyze_replaygain(ctx: Context) -> Context:
    """
    Extracts the Replaygain loudness normalization factor of a track using ffmpeg.
    """
    try:
        # First probe for existing replaygain metadata.
        track_gain = probe_replaygain(ctx.filepath)
        if track_gain is not None:
            ctx.metadata["replay_gain"] = track_gain
            return ctx
    except (CalledProcessError, FileNotFoundError) as exception:
        raise PipelineError(
            f"could not probe replaygain for {ctx.filepath}"
        ) from exception

    try:
        track_gain = compute_replaygain(ctx.filepath)
        if track_gain is not None:
            ctx.metadata["replay_gain"] = track_gain
    except (CalledProcessError, FileNotFoundError) as exception:
        raise PipelineError(
            f"could not compute replaygain for {ctx.filepath}"
        ) from exception

    return ctx
