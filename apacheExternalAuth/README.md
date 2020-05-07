# **ITNSA Note - Apache2 External Script Authentication**

## # **0x00, Install package**
* **1. apache2**
```=
# apt install apache2 -y
# apt install libapache2-mod-authnz-external -y
```
***<font color="#ff0000" style="font-size: 13px;">Note: Make sure authnz_external module is enabled, if not enabled, please use `a2enmod authnz_external` to enable it.</font>***

* **2. perl**
```=
# apt install libio-handle-util-perl -y
# apt install libcrypt-passwdmd5-perl -y
```
***<font color="#ff0000" style="font-size: 13px;">Note: Used in IO :: File and Crypt :: PasswdMD5</font>***

## # **0x01, Create directory and move file to this path**
```=
# mkdir -p /etc/apache2/script
# mkdir -p /etc/apache2/script/logs
# chown www-data:wwww-data -R /etc/apache2/script/
# chmod 770 -R /etc/apache2/script/
# chmod +x myauth.pl
# chmod +x dellock.pl
```

## # **0x02, Create authentication user information**
```=
# cd /etc/apache2/script/
# htpasswd -cb .htpasswd user001 user001
```
***<font color="#ff0000" style="font-size: 13px;">Note: You can create your own favorite user information</font>***

## # **0x03, Configuration apache2**
* **1. configuration file of your virtual host the following:**
```=
addexternalauth myauth /etc/apache2/script/myauth.pl
setexternalauthmethod myauth pipe
<location />
        authtype basic
        authname "Script Auth"
        authbasicprovider external
        authexternal myauth
        require valid-user
</location>
```

* **2. reload apache2**
```=
# systemctl restart apache2
```