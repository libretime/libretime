import threading

import pytest

from libretime_playout.liquidsoap.client import LiquidsoapClient, LiquidsoapClientError
from libretime_playout.liquidsoap.client._client import _OwnedLock


def test_liq_client():
    assert LiquidsoapClient(
        host="localhost",
        port=1234,
        timeout=15,
    )


def test_liq_client_wait_for_version(liq_client: LiquidsoapClient):
    assert liq_client.wait_for_version()


def test_liq_client_wait_for_version_invalid_host():
    liq_client = LiquidsoapClient(
        host="invalid",
        port=1234,
    )
    with pytest.raises(LiquidsoapClientError):
        liq_client.wait_for_version(timeout=1)


def test_owned_lock_tracks_holder_thread():
    lock = _OwnedLock()
    assert not lock.held_by_current_thread()

    seen_by_other: list = []
    with lock:
        assert lock.held_by_current_thread()

        other = threading.Thread(
            target=lambda: seen_by_other.append(lock.held_by_current_thread())
        )
        other.start()
        other.join()

    assert seen_by_other == [False]
    assert not lock.held_by_current_thread()


def test_set_var_rejects_calls_without_held_lock():
    """Internal helpers must refuse to run when the caller doesn't own the lock."""
    client = LiquidsoapClient(host="localhost", port=1234)
    with pytest.raises(RuntimeError, match="_set_var must be called"):
        # pylint: disable=protected-access
        client._set_var("anything", "value")  # noqa: SLF001


def test_liq_client_version_thread_safe(liq_client: LiquidsoapClient):
    """Concurrent callers must not corrupt the request/response stream.

    Regression: prior to the internal lock, two threads racing through
    ``with self.conn:`` would overwrite ``self._sock`` for each other, mixing
    requests and responses so some ``version()`` calls returned ``(0, 0, 0)``
    or raised ``OSError``. With the lock, all calls return the same valid
    version tuple.
    """
    expected = liq_client.version()

    threads_count = 8
    iterations = 25
    start = threading.Barrier(threads_count)
    collect_lock = threading.Lock()
    errors: list = []
    results: list = []

    def worker() -> None:
        start.wait()  # release all threads at once to maximize contention
        for _ in range(iterations):
            try:
                version = liq_client.version()
            except (OSError, ValueError, IndexError) as exc:
                # OSError: torn socket; ValueError/IndexError: corrupted parse.
                with collect_lock:
                    errors.append(exc)
                return
            with collect_lock:
                results.append(version)

    threads = [threading.Thread(target=worker) for _ in range(threads_count)]
    for thread in threads:
        thread.start()
    for thread in threads:
        thread.join(timeout=60)
        assert not thread.is_alive(), "worker did not finish — possible deadlock"

    assert not errors, f"errors raised under concurrent use: {errors}"
    assert len(results) == threads_count * iterations
    distinct = set(results)
    assert distinct == {
        expected
    }, f"inconsistent versions returned (stream corruption): {distinct}"
