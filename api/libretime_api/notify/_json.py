"""
JSON encoding compatible with the ``json_encode`` calls of the legacy service.

The messages LibreTime sends over RabbitMQ are compared byte for byte by the
tools in ``tools/rabbitmq-equivalence``, so the encoding has to match what
PHP puts on the wire:

* no whitespace between the values,
* keys keep the order they were inserted in,
* non-ASCII characters are escaped, including astral characters as surrogate
  pairs, which both ``json_encode`` and :func:`json.dumps` do,
* forward slashes are escaped, which only ``json_encode`` does.
"""

import json
from typing import Any


def php_json_dumps(value: Any) -> str:
    """Encode ``value`` the way ``json_encode($value)`` would."""
    # A slash never appears outside of a string in JSON output, so escaping
    # them after the encoding is equivalent to PHP escaping them while it.
    return json.dumps(value, ensure_ascii=True, separators=(",", ":")).replace(
        "/", "\\/"
    )
