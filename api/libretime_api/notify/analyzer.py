"""
Notifications for the analyzer service.

Files imported into the library are queued for analysis on the
``airtime-uploads`` topic exchange, which the analyzer consumes from the
``airtime-uploads`` queue.
"""

from typing import Any

from ._amqp import ExchangeSpec, publish

# Declared by the legacy service as durable without auto delete.
UPLOADS_EXCHANGE = ExchangeSpec(name="airtime-uploads", type="topic")


def send_message_to_analyzer(
    tmp_file_path: str,
    import_directory: str,
    original_filename: str,
    file_id: int,
    analyze_cue_points: bool | None = None,
) -> None:
    """Ask the analyzer to read and import an uploaded file.

    Equivalent of ``Application_Model_RabbitMq::SendMessageToAnalyzer``. The
    ``options`` field is empty unless the track type of the file is known, the
    only option the analyzer reads being the cue point analysis.
    """
    options: dict[str, Any] = {}
    if analyze_cue_points is not None:
        options["analyze_cue_points"] = analyze_cue_points

    publish(
        UPLOADS_EXCHANGE,
        {
            "file_id": file_id,
            "tmp_file_path": tmp_file_path,
            "import_directory": import_directory,
            "original_filename": original_filename,
            "options": options,
        },
    )
