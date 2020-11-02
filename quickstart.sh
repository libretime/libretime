# /bin/bash
# This script allows for a one-liner to download and install Libretime
# using the default settings. Assumes a clean server setup. Needs sudo.

git clone https://github.com/LibreTime/libretime.git

cd libretime

bash install -fiap