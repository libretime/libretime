from enum import Enum
from pathlib import Path
from typing import Any, Dict, Optional

from pydantic import BaseModel


class Status(int, Enum):
    succeed = 0
    pending = 1
    failed = 2


class Context(BaseModel):
    filepath: Path
    original_filename: str
    storage_url: str

    callback_api_key: str
    callback_url: str

    metadata: Dict[str, Any] = {}
    status: Status = Status.pending
    error: Optional[str]
