from datetime import datetime, time


def time_in_seconds(value: time):
    return (
        value.hour * 60 * 60
        + value.minute * 60
        + value.second
        + value.microsecond / 1000000.0
    )


def time_in_milliseconds(value: time):
    return time_in_seconds(value) * 1000


def time_fromisoformat(value: str):
    """
    This is required for Python 3.6 support. datetime.time.fromisoformat was
    only added in Python 3.7. Until LibreTime drops Python 3.6 support, this
    wrapper uses the old way of doing value.
    """
    try:
        obj = datetime.strptime(value, "%H:%M:%S.%f")
    except ValueError:
        obj = datetime.strptime(value, "%H:%M:%S")
    return obj.time()
