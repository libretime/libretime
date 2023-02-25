from libretime_playout.player.events import EventKind, FileEvent
from libretime_playout.player.liquidsoap_gateway import create_liquidsoap_annotation


def test_create_liquidsoap_annotation():
    file_event: FileEvent = {
        "type": EventKind.FILE,
        "row_id": 1,
        "start": "2022-09-05-11-00-00",
        "end": "2022-09-05-11-05-02",
        "uri": None,
        "id": 2,
        "show_name": "Show 1",
        "fade_in": 500.0,
        "fade_out": 500.0,
        "cue_in": 13.7008,
        "cue_out": 315.845,
        "metadata": {
            "track_title": 'My Friend the "Forest"',
            "artist_name": "Nils Frahm",
            "mime": "audio/flac",
        },
        "replay_gain": "11.46",
        "filesize": 10000,
        "dst": "fake/path.flac",
    }

    assert create_liquidsoap_annotation(file_event) == (
        """annotate:media_id="2",liq_start_next="0",liq_fade_in="0.5","""
        """liq_fade_out="0.5",liq_cue_in="13.7008",liq_cue_out="315.845","""
        """schedule_table_id="1",replay_gain="11.46 dB",artist="Nils Frahm","""
        """title="My Friend the \\"Forest\\"":fake/path.flac"""
    )
