import os
from configobj import ConfigObj
import traceback

upgrades_config = {
        '/etc/airtime/media-monitor.cfg' :
        {
            'check_filesystem_events' : 5,
            'check_airtime_events' : 30,
            'touch_interval' : 5,
            'chunking_number' : 450,
            'request_max_wait' : 3.0,
            'rmq_event_wait' : 0.1,
            'logpath' : '/var/log/airtime/media-monitor/media-monitor.log',
            'index_path' : '/var/tmp/airtime/media-monitor/last_index',
        },
        '/etc/airtime/api_client.cfg' :
        {
            'reload_metadata_group' :
                'reload-metadata-group/format/json/api_key/%%api_key%%',
        }
}

def upgrade(upgrade_data):
    for f, values in upgrade_data:
        if not os.path.exists(f):
            print("Cannot upgrade '%s'. Skipping this file" % f)
            continue
        try:
            cfg = ConfigObj(f)
            for k,v in values:
                if k not in cfg: cfg[k] = v
        except Exception:
            print("Error upgrading")
            print( traceback.format_exc() )

if __name__ == "__main__": upgrade(upgrades_config)
