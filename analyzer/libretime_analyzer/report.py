import requests
from loguru import logger

from .pipeline.context import Context, Status


def report_to_callback(ctx: Context):
    """Report the extracted metadata and status of the successfully imported file
    to the callback URL (which should be the Airtime File Upload API)
    """

    if ctx.status == Status.succeed:
        payload = ctx.metadata

    elif ctx.status == Status.failed:
        payload = {
            "import_status": ctx.status,
            "comment": str(ctx.error),
        }

    else:
        raise ValueError(f"invalid status '{ctx.status}'")

    logger.debug(f"sending {payload} to '{ctx.callback_url}'")
    resp = requests.put(
        ctx.callback_url,
        json=payload,
        auth=(ctx.callback_api_key, ""),
        timeout=5,
    )
    resp.raise_for_status()
