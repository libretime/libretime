from typing import Any


def quote(value: Any, double=False) -> str:
    """
    Quote and escape strings quotes for liquidsoap.

    Double will escape the quotes twice, this is usually only used for the socket
    communication to liquidsoap.
    """
    if not isinstance(value, str):
        value = str(value)
    escaper = "\\\\" if double else "\\"
    escaped = value.replace('"', f'{escaper}"')
    return f'"{escaped}"'
