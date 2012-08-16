#export PYTHONPATH="/home/rudi/Airtime/python_apps/:/home/rudi/Airtime/python_apps/media-monitor2/"
PYTHONPATH='/home/rudi/Airtime/python_apps/:/home/rudi/Airtime/python_apps/media-monitor2/'
export PYTHONPATH
python ./mm2.py --config="/etc/airtime/media-monitor.cfg" --apiclient="/etc/airtime/api_client.cfg" --log="/home/rudi/Airtime/python_apps/media-monitor/logging.cfg"
