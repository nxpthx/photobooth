#!/bin/sh

IMAGEPATH=/home/pi/Pictures


apt install gphoto2

cp -f shell/postProcessCameraDownload /usr/local/bin
chmod a+x /usr/local/bin/postProcessCameraDownload

touch ${IMAGEPATH}/currentImage.txt

mkdir -p /home/pi/.config/Photobooth
cat <<EOF > /home/pi/.config/Photobooth/config.json
{
    "imagePath": "${IMAGEPATH}"
}
EOF

chown -R pi:pi /home/pi/.config/Photobooth
chown pi:pi ${IMAGEPATH}/currentImage.txt

cp -f systemd/gphotodownloader.service /etc/systemd/system/
systemctl reload
systemctl enable gphotodownloader
systemctl start gphotodownloader

cp -r previewscreen/dist/Photobooth-linux-armv7l /opt/photobooth
