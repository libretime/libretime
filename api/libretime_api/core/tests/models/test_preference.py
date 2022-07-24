import pytest

from ...models import StreamSetting


# pylint: disable=invalid-name,unused-argument
@pytest.mark.parametrize(
    "type_name, value",
    [
        ("boolean", True),
        ("integer", 1),
        ("string", "hello"),
    ],
)
def test_stream_setting_value(db, type_name, value):
    setting = StreamSetting.objects.create(
        key=f"some_{type_name}",
        type=type_name,
        raw_value=str(value),
    )
    assert isinstance(setting.value, type(value))

    empty_setting = StreamSetting.objects.create(
        key=f"some_empty_{type_name}",
        type=type_name,
        raw_value="",
    )
    assert empty_setting.value is None
