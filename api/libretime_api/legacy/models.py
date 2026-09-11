import logging
from datetime import datetime
from typing import Optional

from django.db import models
from django.utils.crypto import get_random_string

from ..core.models import User
from .vendor import phpserialize

logger = logging.getLogger(__name__)


LEGACY_SESSION_LIFETIME = 1440


def legacy_session_decode(value: Optional[str]) -> dict:
    if not value:
        return {}
    return phpserialize.loads(value, object_hook=phpserialize.phpobject)


def legacy_session_encode(data: Optional[dict]) -> str:
    if not data:
        return ""
    return phpserialize.dumps(data, object_hook=phpserialize.phpobject)


class LegacySession(models.Model):
    id = models.CharField(primary_key=True, max_length=32)
    modified = models.IntegerField(blank=True, null=True)
    lifetime = models.IntegerField(blank=True, null=True)
    data = models.TextField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = "sessions"

    @classmethod
    def login(cls, old_session_id: str, user: User) -> Optional["LegacySession"]:
        try:
            old_session = cls.objects.get(id=old_session_id)
        except cls.DoesNotExist:
            return None

        # Check session expiration time
        old_session_expires = (old_session.modified or 0) + (old_session.lifetime or 0)
        if old_session_expires < datetime.now().timestamp():
            old_session.delete()
            return None

        session_data = legacy_session_decode(old_session.data)

        def _datetime_format(value: Optional[datetime]) -> Optional[str]:
            return value.strftime("%Y-%m-%d %H:%M:%S.%f") if value else None

        user_data = phpserialize.phpobject(
            name="stdClass",
            d={
                "id": user.id,
                "login": user.username,
                "email": user.email,
                "type": user.role,
                "first_name": user.first_name,
                "last_name": user.last_name,
                "lastlogin": _datetime_format(user.last_login),
                "lastfail": _datetime_format(user.last_failed_login),
                "login_attempts": user.login_attempts,
                "skype_contact": user.skype,
                "jabber_contact": user.jabber,
                "cell_phone": user.phone,
            },
        )
        if session_data["libretime"]["storage"] is not None:
            logger.warning("overwriting data in legacy session")

        session_data["libretime"]["storage"] = user_data

        # https://github.com/zf1s/zend-session/blob/f33edeaabeda8def65e18cfb13be2c12664f03c3/library/Zend/Session.php#L532-L541
        new_session_id = get_random_string(
            length=26,
            allowed_chars="0123456789abcdef",
        )
        new_session = cls(
            id=new_session_id,
            modified=datetime.now().timestamp(),
            lifetime=LEGACY_SESSION_LIFETIME,
            data=legacy_session_encode(session_data),
        )

        old_session.delete()
        new_session.save()

        return new_session

    @classmethod
    def logout(cls, old_session_id: str) -> None:
        cls.objects.filter(id=old_session_id).delete()
