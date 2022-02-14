from subprocess import PIPE, CalledProcessError, CompletedProcess, run

from loguru import logger


def run_(*args, **kwargs) -> CompletedProcess:
    try:
        return run(
            args,
            check=True,
            stdout=PIPE,
            stderr=PIPE,
            universal_newlines=True,
            **kwargs,
        )

    except OSError as exception:  # executable was not found
        cmd = args[0]
        logger.warning(f"Failed to run: {cmd} - {exception}. Is {cmd} installed?")
        raise exception

    except CalledProcessError as exception:  # returned an error code
        logger.error(exception)
        raise exception
