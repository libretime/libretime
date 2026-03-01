from uvicorn.workers import UvicornWorker  # pylint: disable=import-error


class Worker(UvicornWorker):
    CONFIG_KWARGS = {"lifespan": "off"}
