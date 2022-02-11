#!/usr/bin/env python3

import shutil
from pathlib import Path
from tempfile import TemporaryDirectory
from time import sleep

from kombu import Connection, Producer

from libretime_analyzer.config import Config
from libretime_analyzer.message_handler import ANALYZER_EXCHANGE, PIPELINE_ROUTING_KEY
from tests.fixtures import fixtures_path

sample = fixtures_path / "s1.flac"


with TemporaryDirectory() as tmp_dir:
    tmp_path = Path(tmp_dir)
    tmp_sample = tmp_path / "s1.flac"
    shutil.copy(sample, tmp_sample)

    dest_dir = tmp_path / "dest"
    dest_dir.mkdir(parents=True, exist_ok=True)

    message = {
        "filepath": str(tmp_sample),
        "original_filename": "some-test-filename.flac",
        "storage_url": str(dest_dir),
        "callback_api_key": "test_key",
        "callback_url": "http://localhost",
    }

    with Connection(Config().rabbitmq.url) as conn:
        with conn.channel() as channel:
            producer = Producer(
                exchange=ANALYZER_EXCHANGE,
                channel=channel,
            )
            producer.publish(
                message,
                exchange=ANALYZER_EXCHANGE,
                routing_key=PIPELINE_ROUTING_KEY,
            )

    # Wait for the pipeline to finish !
    sleep(5)
