from datetime import time


def time_in_seconds(value: time) -> float:
    return (
        value.hour * 60 * 60
        + value.minute * 60
        + value.second
        + value.microsecond / 1000000.0
    )


def time_in_milliseconds(value: time) -> float:
    return time_in_seconds(value) * 1000
