from typing import Any, Dict, Protocol


class Step(Protocol):
    @staticmethod
    def __call__(filename: str, metadata: Dict[str, Any]):
        ...
