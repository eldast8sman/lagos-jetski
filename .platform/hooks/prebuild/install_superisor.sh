#!/bin/sh

sudo apt-get update
sudo apt-get install python3 python3-venv python3-pip -y

sudo python3 -m venv /opt/supervisor
. /opt/supervisor/bin/activate
sudo /opt/supervisor/bin/pip install --upgrade pip
sudo /opt/supervisor/bin/pip install supervisor certbot
sudo ln -sf /opt/supervisor/bin/supervisord /usr/bin/supervisord
sudo ln -sf /opt/supervisor/bin/supervisorctl /usr/bin/supervisorctl
sudo cp .platform/files/supervisor.conf /etc/supervisord.conf
sudo cp .platform/files/supervisord.service /lib/systemd/system/supervisord.service
sudo systemctl start supervisord
sudo systemctl enable supervisord
sudo systemctl daemon-reload
