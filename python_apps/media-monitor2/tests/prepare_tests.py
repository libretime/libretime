import shutil
import subprocess
# The tests rely on a lot of absolute paths and other garbage so this file
# configures all of that
music_folder = u'/home/rudi/music'
o_path = u'/home/rudi/throwaway/ACDC_-_Back_In_Black-sample-64kbps.ogg'
watch_path = u'/home/rudi/throwaway/fucking_around/watch/',
real_path1 = u'/home/rudi/throwaway/fucking_around/watch/unknown/unknown/ACDC_-_Back_In_Black-sample-64kbps-64kbps.ogg'
opath = u"/home/rudi/Airtime/python_apps/media-monitor2/tests/"
ppath = u"/home/rudi/Airtime/python_apps/media-monitor2/media/"
sample_config = u'/home/rudi/Airtime/python_apps/media-monitor2/tests/api_client.cfg'
real_config = u'/home/rudi/Airtime/python_apps/media-monitor2/tests/live_client.cfg'
api_client_path = '/etc/airtime/api_client.cfg'

if __name__ == "__main__":
    shutil.copy(api_client_path, real_config)
    # TODO : fix this to use liberal permissions
    subprocess.call(["chown","rudi",real_config])

