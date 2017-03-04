#! /bin/sh

echo "Updating Apt."
apt-get update > /dev/null
echo "Ensuring Pip is installed."
DEBIAN_FRONTEND=noninteractive apt-get install -y -qq python-pip > /dev/null
echo "Updating Pip."
pip install pip -q -q --upgrade > /dev/null
echo "Ensuring Mkdocs is installed."
pip install -q mkdocs > /dev/null
