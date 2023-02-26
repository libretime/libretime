import logging
from pathlib import Path
from socket import AF_UNIX, SOCK_STREAM, create_connection, socket
from threading import Lock
from typing import Optional

logger = logging.getLogger(__name__)


class InvalidConnection(Exception):
    """
    Call was made with an invalid connection
    """


class LiquidsoapConnection:
    _host: str
    _port: int
    _path: Optional[Path] = None
    _timeout: int

    _lock: Lock
    _sock: Optional[socket] = None
    _eof = b"END"

    def __init__(
        self,
        host: str = "localhost",
        port: int = 0,
        path: Optional[Path] = None,
        timeout: int = 5,
    ):
        """
        Create a connection to a Liquidsoap server.

        Args:
            host: Host of the Liquidsoap server. Defaults to "localhost".
            port: Port of the Liquidsoap server. Defaults to 0.
            path: Unix socket path of the Liquidsoap server. If defined, use a unix
                socket instead of the host and port address. Defaults to None.
            timeout: Socket timeout. Defaults to 5.
        """
        self._lock = Lock()
        self._path = path
        self._host = host
        self._port = port
        self._timeout = timeout

    def address(self):
        return f"{self._host}:{self._port}" if self._path is None else self._path

    def __enter__(self):
        try:
            self.connect()
            return self
        except (ConnectionError, TimeoutError) as exception:
            self._sock = None
            self._lock.release()
            raise exception

    def __exit__(self, exc_type, exc_value, _traceback):
        self.close()

    def connect(self):
        logger.debug("trying to acquire lock")
        # pylint: disable=consider-using-with
        self._lock.acquire()
        logger.debug("connecting to %s", self.address())

        if self._path is not None:
            self._sock = socket(AF_UNIX, SOCK_STREAM)
            self._sock.settimeout(self._timeout)
            self._sock.connect(str(self._path))
        else:
            self._sock = create_connection(
                address=(self._host, self._port),
                timeout=self._timeout,
            )

    def close(self):
        if self._sock is not None:
            logger.debug("closing connection to %s", self.address())

            self.write("exit")
            # Reading for clean exit
            while self._sock.recv(1024):
                continue

            sock = self._sock
            self._sock = None
            sock.close()

        self._lock.release()

    def write(self, *messages: str):
        if self._sock is None:
            raise InvalidConnection()

        for message in messages:
            logger.debug("sending %s", message)
            buffer = message.encode(encoding="utf-8")
            buffer += b"\n"

            self._sock.sendall(buffer)

    def read(self) -> str:
        if self._sock is None:
            raise InvalidConnection()

        chunks = []
        while True:
            chunk = self._sock.recv(1024)
            if not chunk:
                break

            eof_index = chunk.find(self._eof)
            if eof_index >= 0:
                chunk = chunk[:eof_index]
                chunks.append(chunk)
                break

            chunks.append(chunk)

        buffer = b"".join(chunks)
        buffer = buffer.replace(b"\r\n", b"\n")
        buffer = buffer.rstrip(b"END")
        buffer = buffer.strip(b"\n")
        message = buffer.decode("utf-8")

        logger.debug("received %s", message)
        return message
