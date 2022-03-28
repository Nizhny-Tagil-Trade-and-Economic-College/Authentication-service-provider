#!/usr/bin/bash
docker exec nttek_auth rm -rf /var/www/html/*
docker cp ./html/. nttek_auth:/var/www/html/.
docker cp ./assets/. nttek_auth:/var/www/html/assets/.