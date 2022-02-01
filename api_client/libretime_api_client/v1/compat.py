from .client import API_VERSION, ApiClient

from loguru import logger


class ApiClientCompat(ApiClient):
    """
    Compatibility layer class on top of ApiClient to provide additional logic.
    """
