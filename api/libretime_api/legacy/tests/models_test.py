from datetime import datetime
from typing import Optional

import pytest

from ...core.models import Role, User
from ..models import (
    LEGACY_SESSION_LIFETIME,
    LegacySession,
    legacy_session_decode,
    legacy_session_encode,
)
from ..vendor import phpserialize


def make_legacy_session_data(session_data: Optional[dict] = None):
    if session_data is not None:
        storage = phpserialize.phpobject(name="stdClass", d=session_data)
    else:
        storage = None

    return {
        "__ZF": {"csrf_namespace": {"ENT": 1686060245}},
        "csrf_namespace": {"authtoken": "985a17a922c86a12f28c3ebd1abd09375e1dfde4"},
        "libretime": {"storage": storage},
    }


TEST_SESSION_DATA_RAW = """a:3:{s:4:"__ZF";a:1:{s:14:"csrf_namespace";a:1:{s:3:"ENT";i:1686060245;}}s:14:"csrf_namespace";a:1:{s:9:"authtoken";s:40:"985a17a922c86a12f28c3ebd1abd09375e1dfde4";}s:9:"libretime";a:1:{s:7:"storage";O:8:"stdClass":13:{s:2:"id";i:1;s:5:"login";s:5:"admin";s:4:"pass";s:32:"21232f297a57a5a743894a0e4a801fc3";s:4:"type";s:1:"A";s:10:"first_name";s:0:"";s:9:"last_name";s:0:"";s:9:"lastlogin";N;s:8:"lastfail";N;s:13:"skype_contact";N;s:14:"jabber_contact";N;s:5:"email";N;s:10:"cell_phone";N;s:14:"login_attempts";i:0;}}}"""  # pylint: disable=line-too-long
TEST_SESSION_DATA = make_legacy_session_data(
    {
        "id": 1,
        "login": "admin",
        "pass": "21232f297a57a5a743894a0e4a801fc3",
        "type": "A",
        "first_name": "",
        "last_name": "",
        "lastlogin": None,
        "lastfail": None,
        "skype_contact": None,
        "jabber_contact": None,
        "email": None,
        "cell_phone": None,
        "login_attempts": 0,
    }
)


def test_legacy_session_decode():
    assert legacy_session_decode(TEST_SESSION_DATA_RAW) == TEST_SESSION_DATA


def test_legacy_session_encode():
    print(legacy_session_encode(TEST_SESSION_DATA))
    assert legacy_session_encode(TEST_SESSION_DATA) == TEST_SESSION_DATA_RAW


@pytest.fixture(name="old_session")
def old_session_fixture():
    return LegacySession.objects.create(
        id="jikjr9dlrjl9b0jn9r6tnci47a",
        modified=datetime.now().timestamp(),
        lifetime=LEGACY_SESSION_LIFETIME,
        data=legacy_session_encode(make_legacy_session_data()),
    )


@pytest.mark.django_db
def test_legacy_session_login(old_session: LegacySession):
    user = User.objects.create_user(
        role=Role.HOST,
        username="test",
        password="test",
        email="test@example.com",
        first_name="test",
        last_name="user",
    )

    new_session = LegacySession.login(old_session.id, user)
    assert new_session is not None
    assert not LegacySession.objects.filter(id=old_session.id).exists()
    assert old_session.id != new_session.id

    new_session_data = legacy_session_decode(new_session.data)
    assert new_session_data == make_legacy_session_data(
        {
            "id": user.id,
            "login": "test",
            "email": "test@example.com",
            "type": "H",
            "first_name": "test",
            "last_name": "user",
            "lastlogin": None,
            "lastfail": None,
            "login_attempts": None,
            "skype_contact": None,
            "jabber_contact": None,
            "cell_phone": None,
        },
    )


@pytest.mark.django_db
def test_legacy_session_login_expired():
    user = User.objects.create_user(
        role=Role.HOST,
        username="test",
        password="test",
        email="test@example.com",
        first_name="test",
        last_name="user",
    )

    old_session = LegacySession.objects.create(
        id="jikjr9dlrjl9b0jn9r6tnci47a",
        modified=datetime.now().timestamp() - LEGACY_SESSION_LIFETIME - 1,
        lifetime=LEGACY_SESSION_LIFETIME,
        data=legacy_session_encode(make_legacy_session_data()),
    )

    new_session = LegacySession.login(old_session.id, user)

    assert new_session is None
    assert not LegacySession.objects.filter(id=old_session.id).exists()


@pytest.mark.django_db
def test_legacy_session_logout(old_session: LegacySession):
    LegacySession.logout(old_session.id)
    assert not LegacySession.objects.filter(id=old_session.id).exists()
