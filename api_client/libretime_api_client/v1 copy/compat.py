# pylint: disable=broad-except

import json

from loguru import logger

from .client import API_VERSION, ApiClient


class ApiClientCompat(ApiClient):
    """
    Compatibility layer class on top of ApiClient to provide additional logic.
    """

    # pylint: disable=unused-argument
    def is_server_compatible(self, verbose=True):
        try:
            payload = self.version()
            api_version = payload["api_version"]
        except Exception:
            logger.error("Unable to get API version.")
            return False

        logger.debug(f"Found API version {api_version}")

        if api_version[0:3] != API_VERSION[0:3]:
            logger.error(f"pypo is only compatible with API version {API_VERSION}")
            return False

        return True

    def get_schedule(self):
        # TODO: refactor, the return type is messed up for compatibility
        try:
            return (True, self.export())
        except Exception:
            return (False, None)

    def notify_liquidsoap_started(self):
        super().notify_liquidsoap_started(silent=True)

    def notify_media_item_start_playing(self, media_id):
        """
        This is a callback from liquidsoap, we use this to notify about the currently
        playing *song*. We get passed a JSON string which we handed to liquidsoap
        in get_liquidsoap_data().
        """
        return self.update_start_playing(media_id=media_id, silent=True)

    def get_shows_to_record(self):
        return super().show_schedule(silent=True)

    # pylint: disable=unused-argument
    def upload_recorded_show(self, files, show_id):
        # FIXME: We need to tell LibreTime that the uploaded track was recorded for a specific show

        # My issue here is that response does not yet have an id. The id gets generated at the point
        # where analyzer is done with it's work. We probably need to do what is below in analyzer
        # and also make sure that the show instance id is routed all the way through.

        # It already gets uploaded by this but the RestController does not seem to care about it. In
        # the end analyzer doesn't have the info in it's rabbitmq message and imports the show as a
        # regular track.
        return self.upload_files(files=files, retry_attempt=3, retry_interval=60)

    def check_live_stream_auth(self, username, password, dj_type):
        try:
            return super().check_live_stream_auth(
                username=username,
                password=password,
                djtype=dj_type,
            )
        except Exception as error:
            logger.error(error)
            return {}

    def send_media_monitor_requests(self, action_list, dry=False):
        """
        Send a gang of media monitor events at a time. actions_list is a
        list of dictionaries where every dictionary is representing an
        action. Every action dict must contain a 'mode' key that says
        what kind of action it is and an optional 'is_record' key that
        says whether the show was recorded or not. The value of this key
        does not matter, only if it's present or not.
        """
        # We are assuming that action_list is a list of dictionaries such
        # that every dictionary represents the metadata of a file along
        # with a special mode key that is the action to be executed by the
        # controller.
        valid_actions = []
        # We could get a list of valid_actions in a much shorter way using
        # filter but here we prefer a little more verbosity to help
        # debugging
        for action in action_list:
            if not "mode" in action:
                logger.warning("Trying to send a request element without a 'mode'")
                logger.debug("Here is the the request: '%s'" % str(action))
            else:
                # We alias the value of is_record to true or false no
                # matter what it is based on if it's absent in the action
                if "is_record" not in action:
                    action["is_record"] = 0
                valid_actions.append(action)
        # Note that we must prefix every key with: mdX where x is a number
        # Is there a way to format the next line a little better? The
        # parenthesis make the code almost unreadable
        md_list = {("md%d" % i): json.dumps(md) for i, md in enumerate(valid_actions)}
        # For testing we add the following "dry" parameter to tell the
        # controller not to actually do any changes
        if dry:
            md_list["dry"] = 1
        logger.info("Pumping out %d requests..." % len(valid_actions))
        return super().reload_metadata_group(data=md_list)

    # returns a list of all db files for a given directory in JSON format:
    # {"files":["path/to/file1", "path/to/file2"]}
    # Note that these are relative paths to the given directory. The full
    # path is not returned.
    def list_all_db_files(self, dir_id, all_files=True):
        response = super().list_all_db_files(
            dir_id=dir_id,
            all="1" if all_files else "0",
            silent=True,
        )

        if "files" in response:
            return response["files"]

        logger.error(f"Could not find index 'files' in dictionary: {response}")
        return []

    def notify_liquidsoap_status(self, msg, stream_id, time):
        return super().update_liquidsoap_status(
            data={"msg_post": msg},
            msg="dummy",
            stream_id=stream_id,
            boot_time=time,
            retry_attempt=5,
            retry_interval=5,
            silent=True,
        )

    def notify_source_status(self, sourcename, status):
        return super().update_source_status(
            sourcename=sourcename,
            status=status,
            retry_attempt=5,
            retry_interval=5,
            silent=True,
        )

    def get_files_without_replay_gain_value(self, dir_id):
        """
        Download a list of files that need to have their ReplayGain value
        calculated. This list of files is downloaded into a file and the path
        to this file is the return value.
        """
        try:
            return self.get_files_without_replay_gain(dir_id=dir_id)
        except Exception:
            return []

    def get_files_without_silan_value(self):
        """
        Download a list of files that need to have their cue in/out value
        calculated. This list of files is downloaded into a file and the path
        to this file is the return value.
        """
        try:
            return super().get_files_without_silan_value()
        except Exception:
            return []

    def update_replay_gain_values(self, pairs):
        """
        'pairs' is a list of pairs in (x, y), where x is the file's database
        row id and y is the file's replay_gain value in dB
        """
        response = self.update_replay_gain_value(data=pairs)
        logger.debug(response)

    def update_cue_values_by_silan(self, pairs):
        """
        'pairs' is a list of pairs in (x, y), where x is the file's database
        row id and y is the file's cue values in dB
        """
        return super().update_cue_values_by_silan(data=pairs)

    def notify_webstream_data(self, data, media_id):
        """
        Update the server with the latest metadata we've received from the
        external webstream
        """
        resp = super().notify_webstream_data(
            media_id=str(media_id),
            data=data,
            retry_attempt=5,
        )
        logger.info(resp)

    def update_stream_setting_table(self, data):
        return super().update_stream_setting_table(data=data, silent=True)
