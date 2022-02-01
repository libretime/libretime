from .client import API_VERSION, ApiClient

from loguru import logger


class ApiClientCompat(ApiClient):
    """
    Compatibility layer class on top of ApiClient to provide additional logic.
    """

    # pylint: disable=unused-argument
    def is_server_compatible(self, verbose=True):
        try:
            payload = self.version()
            api_version = payload["api_version"]
        except Exception as exception:
            logger.error(f"Unable to get API version: {exception}")
            return False

        logger.debug(f"Found API version {api_version}")

        if api_version[0:3] != API_VERSION[0:3]:
            logger.error(f"client is incompatible with API version {API_VERSION}")
            return False

        return True
