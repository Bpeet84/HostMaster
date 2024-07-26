#!/bin/bash

LOGFILE="/var/www/HostMaster/logs/add_user.log"

log() {
    echo "$(date +'%Y-%m-%d %H:%M:%S') - $1" >> $LOGFILE
}

# Ellenőrizzük, hogy a scriptet root-ként futtatják-e
if [ "$(id -u)" -ne 0 ];then
    log "Ezt a scriptet root jogosultságokkal kell futtatni."
    exit 1
fi

# Paraméterek ellenőrzése
if [ "$#" -ne 3 ];then
    log "Használat: $0 felhasználónév jelszó domain"
    exit 1
fi

# Paraméterek
USERNAME=$1
PASSWORD=$2
DOMAIN=$3
HOMEDIR="/home/$USERNAME"
WEBROOT="$HOMEDIR/domains/$DOMAIN/public_html"
LOGDIR="$HOMEDIR/domains/$DOMAIN/logs"
VHOST_CONF="/etc/httpd/sites-available/$DOMAIN.conf"
VHOST_ENABLED="/etc/httpd/sites-enabled/$DOMAIN.conf"

# Ellenőrizzük, hogy a felhasználó már létezik-e
if id "$USERNAME" &>/dev/null; then
    log "Hiba történt a felhasználó létrehozása során: useradd: user '$USERNAME' already exists"
    exit 1
fi

# Létrehozzuk a szükséges könyvtárakat, ha nem léteznek
mkdir -p /etc/httpd/sites-available /etc/httpd/sites-enabled
log "Létrehozva a szükséges könyvtárak: /etc/httpd/sites-available és /etc/httpd/sites-enabled"

# Felhasználó létrehozása a rendszerben
useradd -m -d $HOMEDIR -s /bin/bash $USERNAME
if [ $? -ne 0 ];then
    log "Hiba történt a felhasználó létrehozása során."
    exit 1
fi
log "Felhasználó létrehozva: $USERNAME"

echo "$USERNAME:$PASSWORD" | chpasswd
if [ $? -ne 0 ];then
    log "Hiba történt a jelszó beállítása során."
    exit 1
fi
log "Jelszó beállítva a felhasználónak: $USERNAME"

# Home könyvtár jogosultságok beállítása
chown -R $USERNAME:$USERNAME $HOMEDIR
log "Home könyvtár jogosultságai beállítva: $HOMEDIR"

# Szükséges könyvtárak létrehozása
mkdir -p $WEBROOT $LOGDIR $HOMEDIR/mail $HOMEDIR/backup $HOMEDIR/tmp $HOMEDIR/private $HOMEDIR/config
log "Szükséges könyvtárak létrehozva: $WEBROOT, $LOGDIR, $HOMEDIR/mail, $HOMEDIR/backup, $HOMEDIR/tmp, $HOMEDIR/private, $HOMEDIR/config"

# Jogosultságok beállítása
chown -R $USERNAME:$USERNAME $WEBROOT $LOGDIR $HOMEDIR/mail $HOMEDIR/backup $HOMEDIR/tmp $HOMEDIR/private $HOMEDIR/config
log "Könyvtárak jogosultságai beállítva"

# Virtuális Host konfiguráció létrehozása
cat <<EOL > $VHOST_CONF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $WEBROOT

    <Directory $WEBROOT>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog $LOGDIR/error.log
    CustomLog $LOGDIR/access.log combined
</VirtualHost>
EOL
log "Virtuális Host konfiguráció létrehozva: $VHOST_CONF"

# Szimbolikus link létrehozása a sites-enabled könyvtárban
start_time=$(date +%s)
ln -s $VHOST_CONF $VHOST_ENABLED
if [ $? -ne 0 ];then
    log "Hiba történt a szimbolikus link létrehozása során."
    exit 1
fi
end_time=$(date +%s)
elapsed_time=$(( end_time - start_time ))
log "Szimbolikus link létrehozva: $VHOST_ENABLED (időtartam: $elapsed_time másodperc)"

# Apache újraindítása
systemctl restart httpd
if [ $? -ne 0 ];then
    log "Hiba történt az Apache újraindítása során."
    exit 1
fi
log "Apache újraindítva"

# Alapértelmezett index.html létrehozása
cat <<EOL > $WEBROOT/index.html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to $DOMAIN!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #007BFF;
        }
        p {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to $DOMAIN!</h1>
        <p>This is the default index page for your website. Please replace this file with your own index.html or index.php file.</p>
    </div>
</body>
</html>
EOL

# Jogosultságok beállítása az index.html fájlhoz
chown $USERNAME:$USERNAME $WEBROOT/index.html
log "Alapértelmezett index.html létrehozva és jogosultságok beállítva: $WEBROOT/index.html"

log "User $USERNAME has been created successfully with domain $DOMAIN."
log "Access the default index page at http://$DOMAIN/index.html"
log "Lefutott add_user.sh script"
