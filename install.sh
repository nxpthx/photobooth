#!/bin/sh

IMAGEPATH=/home/pi/pictures


apt install gphoto2

cp -f shell/postProcessCameraDownload /usr/local/bin
chmod a+x /usr/local/bin/postProcessCameraDownload

touch ${IMAGEPATH}/currentImage.txt

mkdir -p /home/pi/.config/PhotoBooth
echo <<<EOF > /home/pi/.config/PhotoBooth/config.json
{
    "imagePath": "${IMAGEPATH}"
}
EOF

cp systemd/gphotodownloader.service /etc/systemd/system/
systemctl reload
systemctl enable gphotodownloader
systemctl start gphotodownloader