from datetime import datetime


def seconds_between(base: datetime, target: datetime) -> float:
    """
    Get seconds between base and target datetime.

    Return 0 if target is older than base.
    """
    return max(0, (target - base).total_seconds())
