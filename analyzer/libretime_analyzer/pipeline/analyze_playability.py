from subprocess import STDOUT, CalledProcessError, check_output

from loguru import logger

from .context import Context


class UnplayableFileError(Exception):
    pass


LIQUIDSOAP_EXECUTABLE = "liquidsoap"


def analyze_playability(ctx: Context) -> Context:
    """Checks if a file can be played by Liquidsoap.
    :param filename: The full path to the file to analyzer
    :param metadata: A metadata dictionary where the results will be put
    :return: The metadata dictionary
    """
    command = [
        LIQUIDSOAP_EXECUTABLE,
        "-v",
        "-c",
        "output.dummy(audio_to_stereo(single(argv(1))))",
        "--",
        ctx.filepath,
    ]
    try:
        check_output(command, stderr=STDOUT, close_fds=True)

    except OSError as exception:  # liquidsoap was not found
        logger.warning(
            "Failed to run: %s - %s. %s"
            % (command[0], exception.strerror, "Do you have liquidsoap installed?")
        )
    except (
        CalledProcessError,
        Exception,
    ) as exception:  # liquidsoap returned an error code
        logger.warning(exception)
        raise UnplayableFileError()

    return ctx
