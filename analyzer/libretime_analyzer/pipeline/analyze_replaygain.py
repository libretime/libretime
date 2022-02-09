from subprocess import CalledProcessError

from ..ffmpeg import compute_replaygain, probe_replaygain
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
    except (CalledProcessError, OSError):
        pass

    try:
        track_gain = compute_replaygain(ctx.filepath)
        if track_gain is not None:
            ctx.metadata["replay_gain"] = track_gain
    except (CalledProcessError, OSError):
        pass

    return ctx
