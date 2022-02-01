from pytest import mark

from tests.v1.conftest import API_KEY, BASE_URL

COMMON_DATA = {"hello": "world"}

COMMON_RESPONSE = {
    "status_code": 200,
    "text": '{"hello": "world"}',
    "headers": {"content-type": "application/json"},
}


def test_api_client_version(m, api_client):
    m.get(
        f"{BASE_URL}/version?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.version()


def test_api_client_register_component(m, api_client):
    m.get(
        f"{BASE_URL}/register-component?format=json&component=10.0.0.1",
        **COMMON_RESPONSE,
    )
    assert api_client.register_component(component="10.0.0.1")


def test_api_client_setup_media_monitor(m, api_client):
    m.get(
        f"{BASE_URL}/media-monitor-setup?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.setup_media_monitor()


def test_api_client_upload_recorded(m, api_client):
    m.get(
        f"{BASE_URL}/upload-recorded?format=json&fileid=3&showinstanceid=5",
        **COMMON_RESPONSE,
    )
    assert api_client.upload_recorded(fileid=3, showinstanceid=5)


def test_api_client_update_media(m, api_client):
    m.get(
        f"{BASE_URL}/reload-metadata?format=json&mode=hello",
        **COMMON_RESPONSE,
    )
    assert api_client.update_media(mode="hello")


def test_api_client_list_all_db_files(m, api_client):
    m.get(
        f"{BASE_URL}/list-all-files?format=json&dir_id=1&all=1",
        **COMMON_RESPONSE,
    )
    assert api_client.list_all_db_files(dir_id=1, all_="1")


def test_api_client_list_all_watched_dirs(m, api_client):
    m.get(
        f"{BASE_URL}/list-all-watched-dirs?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.list_all_watched_dirs()


def test_api_client_add_watched_dir(m, api_client):
    m.get(
        f"{BASE_URL}/add-watched-dir?format=json&path=L3BhdGg%3D",
        **COMMON_RESPONSE,
    )
    assert api_client.add_watched_dir(path="/path")


def test_api_client_remove_watched_dir(m, api_client):
    m.get(
        f"{BASE_URL}/remove-watched-dir?format=json&path=L3BhdGg%3D",
        **COMMON_RESPONSE,
    )
    assert api_client.remove_watched_dir(path="/path")


def test_api_client_set_storage_dir(m, api_client):
    m.get(
        f"{BASE_URL}/set-storage-dir?format=json&path=L3BhdGg%3D",
        **COMMON_RESPONSE,
    )
    assert api_client.set_storage_dir(path="/path")


def test_api_client_update_file_system_mount(m, api_client):
    m.get(
        f"{BASE_URL}/update-file-system-mount?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.update_file_system_mount()


def test_api_client_reload_metadata_group(m, api_client):
    m.post(
        f"{BASE_URL}/reload-metadata-group?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.reload_metadata_group(data=COMMON_DATA)


def test_api_client_handle_watched_dir_missing(m, api_client):
    m.get(
        f"{BASE_URL}/handle-watched-dir-missing?format=json&dir=%2Fpath",
        **COMMON_RESPONSE,
    )
    assert api_client.handle_watched_dir_missing(dir_="/path")


def test_api_client_show_schedule(m, api_client):
    m.get(
        f"{BASE_URL}/recorded-shows?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.show_schedule()


def test_api_client_upload_files(m, api_client):
    m.post(
        f"{BASE_URL}/rest/media?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.upload_files(files=COMMON_DATA)


def test_api_client_export(m, api_client):
    m.get(
        f"{BASE_URL}/schedule?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.export()


def test_api_client_get_media(m, api_client):
    m.get(
        f"{BASE_URL}/get-media?format=json&file=1",
        **COMMON_RESPONSE,
    )
    assert api_client.get_media(file=1)


def test_api_client_update_item(m, api_client):
    m.get(
        f"{BASE_URL}/notify-schedule-group-play?format=json&schedule_id=1",
        **COMMON_RESPONSE,
    )
    assert api_client.update_item(schedule_id=1)


def test_api_client_update_start_playing(m, api_client):
    m.get(
        f"{BASE_URL}/notify-media-item-start-play?format=json&media_id=2",
        **COMMON_RESPONSE,
    )
    assert api_client.update_start_playing(media_id=2)


def test_api_client_get_stream_setting(m, api_client):
    m.get(
        f"{BASE_URL}/get-stream-setting?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.get_stream_setting()


def test_api_client_update_liquidsoap_status(m, api_client):
    m.post(
        f"{BASE_URL}/update-liquidsoap-status?format=json&msg=hello&stream_id=2&boot_time=8",
        **COMMON_RESPONSE,
    )
    assert api_client.update_liquidsoap_status(
        data=COMMON_DATA,
        msg="hello",
        stream_id=2,
        boot_time=8,
    )


def test_api_client_update_source_status(m, api_client):
    m.get(
        f"{BASE_URL}/update-source-status?format=json&sourcename=icecast&status=ok",
        **COMMON_RESPONSE,
    )
    assert api_client.update_source_status(sourcename="icecast", status="ok")


def test_api_client_check_live_stream_auth(m, api_client):
    m.get(
        f"{BASE_URL}/check-live-stream-auth?format=json&username=u&password=p&djtype=t",
        **COMMON_RESPONSE,
    )
    assert api_client.check_live_stream_auth(username="u", password="p", djtype="t")


def test_api_client_get_bootstrap_info(m, api_client):
    m.get(
        f"{BASE_URL}/get-bootstrap-info?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.get_bootstrap_info()


def test_api_client_get_files_without_replay_gain(m, api_client):
    m.get(
        f"{BASE_URL}/get-files-without-replay-gain?format=json&dir_id=3",
        **COMMON_RESPONSE,
    )
    assert api_client.get_files_without_replay_gain(dir_id=3)


def test_api_client_update_replay_gain_value(m, api_client):
    m.post(
        f"{BASE_URL}/update-replay-gain-value?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.update_replay_gain_value(data=COMMON_DATA)


def test_api_client_notify_webstream_data(m, api_client):
    m.post(
        f"{BASE_URL}/notify-webstream-data?format=json&media_id=1",
        **COMMON_RESPONSE,
    )
    assert api_client.notify_webstream_data(media_id=1, data=COMMON_DATA)


def test_api_client_notify_liquidsoap_started(m, api_client):
    m.get(
        f"{BASE_URL}/rabbitmq-do-push?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.notify_liquidsoap_started()


def test_api_client_get_stream_parameters(m, api_client):
    m.get(
        f"{BASE_URL}/get-stream-parameters?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.get_stream_parameters()


def test_api_client_push_stream_stats(m, api_client):
    m.post(
        f"{BASE_URL}/push-stream-stats?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.push_stream_stats(data=COMMON_DATA)


def test_api_client_update_stream_setting_table(m, api_client):
    m.post(
        f"{BASE_URL}/update-stream-setting-table?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.update_stream_setting_table(data=COMMON_DATA)


def test_api_client_get_files_without_silan_value(m, api_client):
    m.get(
        f"{BASE_URL}/get-files-without-silan-value?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.get_files_without_silan_value()


def test_api_client_update_cue_values_by_silan(m, api_client):
    m.post(
        f"{BASE_URL}/update-cue-values-by-silan?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.update_cue_values_by_silan(data=COMMON_DATA)


def test_api_client_update_metadata_on_tunein(m, api_client):
    m.get(
        f"{BASE_URL}/update-metadata-on-tunein?format=json",
        **COMMON_RESPONSE,
    )
    assert api_client.update_metadata_on_tunein()
