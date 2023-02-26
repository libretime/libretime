import logging
from subprocess import CalledProcessError, CompletedProcess, run

logger = logging.getLogger(__name__)


def run_(*args, **kwargs) -> CompletedProcess:
    try:
        return run(
            args,
            check=True,
            capture_output=True,
            text=True,
            **kwargs,
        )

    except OSError as exception:  # executable was not found
        cmd = args[0]
        logger.warning(f"Failed to run: {cmd} - {exception}. Is {cmd} installed?")
        raise exception

    except CalledProcessError as exception:  # returned an error code
        logger.error(exception)
        raise exception
