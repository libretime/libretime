from django.utils import timezone

from ..models import Session
from ..tasks import clean_expired_sessions


def make_session(session_id: str, age: int, lifetime: int | None) -> Session:
    return Session.objects.create(
        id=session_id,
        updated_at=int(timezone.now().timestamp()) - age,
        lifetime=lifetime,
        data="",
    )


# pylint: disable=invalid-name,unused-argument
def test_clean_expired_sessions(db):
    make_session("expired", age=7200, lifetime=3600)
    make_session("valid", age=60, lifetime=3600)
    make_session("valid_long_lived", age=7200, lifetime=86400)

    assert clean_expired_sessions() == 1

    assert set(map(str.strip, Session.objects.values_list("id", flat=True))) == {
        "valid",
        "valid_long_lived",
    }


# pylint: disable=invalid-name,unused-argument
def test_clean_expired_sessions_nothing_to_delete(db):
    make_session("valid", age=60, lifetime=3600)

    assert clean_expired_sessions() == 0
    assert Session.objects.count() == 1


# pylint: disable=invalid-name,unused-argument
def test_clean_expired_sessions_ignores_null_values(db):
    make_session("no_lifetime", age=7200, lifetime=None)

    assert clean_expired_sessions() == 0
    assert Session.objects.count() == 1
