"""
Regression tests for LiquidsoapConnection.close() resilience.

The original symptom: when liquidsoap was restarted, the existing connection
went stale and the next __exit__ bubbled OSError out of the `with` block,
leaving the client unusable. close() now swallows OSError during the shutdown
handshake; these tests pin that contract and verify a subsequent connect()
succeeds.

These tests deliberately reach into LiquidsoapConnection._sock to break the
underlying FD — that is the only way to deterministically reproduce the
stale-socket scenario in unit-test time.
"""

# pylint: disable=protected-access

import socket
import threading
from typing import Generator, List, Tuple

import pytest

from libretime_playout.liquidsoap.client import LiquidsoapConnection


@pytest.fixture(name="tcp_server")
def tcp_server_fixture() -> Generator[Tuple[str, int, List[socket.socket]], None, None]:
    """A minimal TCP server that accepts connections and stays silent.

    Yields ``(host, port, accepted)`` where ``accepted`` is the running list of
    server-side sockets — the test can close them to simulate a peer that went
    away.
    """
    server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    server.bind(("127.0.0.1", 0))
    server.listen(5)
    host, port = server.getsockname()

    accepted: List[socket.socket] = []
    stop = threading.Event()

    def accept_loop() -> None:
        server.settimeout(0.2)
        while not stop.is_set():
            try:
                conn, _ = server.accept()
            except (socket.timeout, OSError):
                continue
            accepted.append(conn)

    thread = threading.Thread(target=accept_loop, daemon=True)
    thread.start()

    try:
        yield host, port, accepted
    finally:
        stop.set()
        server.close()
        thread.join(timeout=1)
        for conn in accepted:
            try:
                conn.close()
            except OSError:
                pass


def _wait_for_accept(accepted: List[socket.socket], expected: int) -> None:
    for _ in range(50):
        if len(accepted) >= expected:
            return
        threading.Event().wait(0.05)
    raise AssertionError(
        f"server only accepted {len(accepted)} of {expected} connections"
    )


def test_close_does_not_raise_when_underlying_socket_is_broken(tcp_server):
    host, port, _ = tcp_server
    conn = LiquidsoapConnection(host=host, port=port, timeout=2)
    conn.connect()

    # Break the FD out from under the connection — sendall/recv inside close()
    # will raise OSError(EBADF). The fix must swallow it.
    conn._sock.close()  # type: ignore[union-attr]

    conn.close()
    assert conn._sock is None


def test_context_manager_exit_does_not_raise_on_stale_socket(tcp_server):
    """The original symptom: __exit__ raised OSError after liquidsoap restarted."""
    host, port, _ = tcp_server
    conn = LiquidsoapConnection(host=host, port=port, timeout=2)

    with conn:
        # Simulate the peer disappearing while we hold the connection open.
        conn._sock.close()  # type: ignore[union-attr]
    # Falling out of the `with` must not raise.

    assert conn._sock is None


def test_close_does_not_raise_after_peer_disconnect(tcp_server):
    """Simulate a liquidsoap restart by closing the server-side socket."""
    host, port, accepted = tcp_server
    conn = LiquidsoapConnection(host=host, port=port, timeout=2)
    conn.connect()

    _wait_for_accept(accepted, 1)
    accepted[0].close()

    conn.close()
    assert conn._sock is None


def test_connect_succeeds_after_stale_socket_close(tcp_server):
    """The point of the fix: the connection must remain reusable."""
    host, port, accepted = tcp_server
    conn = LiquidsoapConnection(host=host, port=port, timeout=2)

    conn.connect()
    _wait_for_accept(accepted, 1)
    conn._sock.close()  # type: ignore[union-attr]
    conn.close()

    conn.connect()
    _wait_for_accept(accepted, 2)
    assert conn._sock is not None
    conn.close()


def test_close_is_idempotent_after_break(tcp_server):
    host, port, _ = tcp_server
    conn = LiquidsoapConnection(host=host, port=port, timeout=2)
    conn.connect()
    conn._sock.close()  # type: ignore[union-attr]

    conn.close()
    conn.close()  # second close must be a no-op, not raise
    assert conn._sock is None
