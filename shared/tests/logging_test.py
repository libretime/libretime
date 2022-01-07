from pathlib import Path

import pytest
from loguru import logger

from libretime_shared.logging import (
    DEBUG,
    INFO,
    create_task_logger,
    level_from_verbosity,
    setup_logger,
)


@pytest.mark.parametrize(
    "verbosity,level_name,level_no",
    [
        (-100, "error", 40),
        (-1, "error", 40),
        (0, "warning", 30),
        (1, "info", 20),
        (2, "debug", 10),
        (3, "trace", 5),
        (100, "trace", 5),
    ],
)
def test_level_from_verbosity(verbosity, level_name, level_no):
    level = level_from_verbosity(verbosity)
    assert level.name == level_name
    assert level.no == level_no


def test_setup_logger(tmp_path: Path):
    log_filepath = tmp_path / "test.log"
    extra_log_filepath = tmp_path / "extra.log"

    setup_logger(INFO, log_filepath)

    extra_logger = create_task_logger(DEBUG, extra_log_filepath, True)

    logger.info("test info")
    extra_logger.info("extra info")
    logger.debug("test debug")

    extra_logger.complete()
    logger.complete()

    assert len(log_filepath.read_text().splitlines()) == 1
    assert len(extra_log_filepath.read_text().splitlines()) == 1
