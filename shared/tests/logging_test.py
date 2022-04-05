from pathlib import Path

import pytest
from loguru import logger

from libretime_shared.logging import (
    DEBUG,
    INFO,
    create_task_logger,
    level_from_name,
    setup_logger,
)


@pytest.mark.parametrize(
    "name,level_name,level_no",
    [
        ("error", "error", 40),
        ("warning", "warning", 30),
        ("info", "info", 20),
        ("debug", "debug", 10),
        ("trace", "trace", 5),
    ],
)
def test_level_from_name(name, level_name, level_no):
    level = level_from_name(name)
    assert level.name == level_name
    assert level.no == level_no


def test_level_from_name_invalid():
    with pytest.raises(ValueError):
        level_from_name("invalid")


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

    assert len(log_filepath.read_text(encoding="utf-8").splitlines()) == 1
    assert len(extra_log_filepath.read_text(encoding="utf-8").splitlines()) == 1
