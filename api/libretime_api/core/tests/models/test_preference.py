from libretime_api.core.models.preference import Preference


# pylint: disable=invalid-name,unused-argument
def test_preference_get_site_preferences(db):
    result = Preference.get_site_preferences()
    assert result.model_dump() == {
        "station_name": "LibreTime",
    }


# pylint: disable=invalid-name,unused-argument
def test_preference_get_stream_preferences(db):
    result = Preference.get_stream_preferences()
    assert result.model_dump() == {
        "input_fade_transition": 0.0,
        "message_format": 0,
        "message_offline": "LibreTime - offline",
        "replay_gain_enabled": True,
        "replay_gain_offset": 0.0,
    }


# pylint: disable=invalid-name,unused-argument
def test_preference_get_stream_state(db):
    result = Preference.get_stream_state()
    assert result.model_dump() == {
        "input_main_connected": False,
        "input_main_streaming": False,
        "input_show_connected": False,
        "input_show_streaming": False,
        "schedule_streaming": True,
    }
