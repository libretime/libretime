from subprocess import PIPE, CompletedProcess, run

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

    except FileNotFoundError as exception:  # executable was not found
        cmd = args[0]
        logger.error(f"Failed to run: {cmd} - {exception}. Is {cmd} installed?")
        raise exception
