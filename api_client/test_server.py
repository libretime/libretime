from pathlib import Path
from urllib.parse import urlparse

from starlette.applications import Starlette
from starlette.endpoints import HTTPEndpoint
from starlette.requests import Request
from starlette.responses import JSONResponse
from starlette.routing import Route

logs = Path("requests.log")

logs_fd = logs.open("a")


def log_it(request: Request):

    call = request.url.path.rstrip("/api").replace("-", "_")

    print(
        f"""
{call}: m.{request.method}("{request.url}", **COMMON_RESPONSE)
    """,
        file=logs_fd,
    )


class Index(HTTPEndpoint):
    async def get(self, request: Request):
        log_it(request)
        return JSONResponse({"hello": "world"})

    async def post(self, request: Request):
        log_it(request)
        return JSONResponse({"hello": "world"})

    async def put(self, request: Request):
        log_it(request)
        return JSONResponse({"hello": "world"})


app = Starlette(
    debug=True,
    routes=[
        Route("/api/{action}", Index),
        Route("/api/rest/{action}", Index),
    ],
)
