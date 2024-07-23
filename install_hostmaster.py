import subprocess
import sys
import time
from tqdm import tqdm
import mysql.connector
from mysql.connector import errorcode
import bcrypt

def install_required_packages():
    required_packages = ['tqdm', 'mysql-connector-python', 'bcrypt']
    installed_packages = subprocess.check_output([sys.executable, '-m', 'pip', 'freeze']).decode('utf-8').split('\n')
    installed_packages = [package.split('==')[0] for package in installed_packages]

    for package in required_packages:
        if package not in installed_packages:
            print(f"Installing {package}...")
            subprocess.check_call([sys.executable, '-m', 'pip', 'install', package])

def run_command(command):
    print(f"Running command: {command}")
    process = subprocess.Popen(command, shell=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    out, err = process.communicate()
    if process.returncode != 0:
        print(f"Error running command: {command}\n{err.decode('utf-8')}")
        return False
    return True

def hash_password(password):
    return bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')

def setup_database():
    db_config = {
        'user': 'hostmaster_panel',
        'password': 'erős_jelszó',
        'host': 'localhost',
        'database': 'hostmaster'
    }

    TABLES = {}
    TABLES['users'] = (
        "CREATE TABLE users ("
        "  id INT AUTO_INCREMENT PRIMARY KEY,"
        "  username VARCHAR(50) NOT NULL UNIQUE,"
        "  password VARCHAR(255) NOT NULL,"
        "  email VARCHAR(100) NOT NULL UNIQUE,"
        "  role ENUM('admin', 'user') NOT NULL,"
        "  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
        ") ENGINE=InnoDB")

    TABLES['domains'] = (
        "CREATE TABLE domains ("
        "  id INT AUTO_INCREMENT PRIMARY KEY,"
        "  user_id INT NOT NULL,"
        "  domain_name VARCHAR(255) NOT NULL,"
        "  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
        "  FOREIGN KEY (user_id) REFERENCES users(id)"
        ") ENGINE=InnoDB")

    TABLES['ftp_accounts'] = (
        "CREATE TABLE ftp_accounts ("
        "  id INT AUTO_INCREMENT PRIMARY KEY,"
        "  user_id INT NOT NULL,"
        "  username VARCHAR(50) NOT NULL,"
        "  password VARCHAR(255) NOT NULL,"
        "  home_directory VARCHAR(255) NOT NULL,"
        "  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
        "  FOREIGN KEY (user_id) REFERENCES users(id)"
        ") ENGINE=InnoDB")

    TABLES['emails'] = (
        "CREATE TABLE emails ("
        "  id INT AUTO_INCREMENT PRIMARY KEY,"
        "  user_id INT NOT NULL,"
        "  email_address VARCHAR(100) NOT NULL,"
        "  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
        "  FOREIGN KEY (user_id) REFERENCES users(id)"
        ") ENGINE=InnoDB")

    TABLES['dns_records'] = (
        "CREATE TABLE dns_records ("
        "  id INT AUTO_INCREMENT PRIMARY KEY,"
        "  domain_id INT NOT NULL,"
        "  record_type VARCHAR(10) NOT NULL,"
        "  name VARCHAR(255) NOT NULL,"
        "  content VARCHAR(255) NOT NULL,"
        "  ttl INT DEFAULT 3600,"
        "  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
        "  FOREIGN KEY (domain_id) REFERENCES domains(id)"
        ") ENGINE=InnoDB")

    try:
        cnx = mysql.connector.connect(**db_config)
        cursor = cnx.cursor()
        print("Successfully connected to the database.")

        for table_name in tqdm(TABLES, desc="Creating database tables"):
            table_description = TABLES[table_name]
            try:
                cursor.execute(table_description)
            except mysql.connector.Error as err:
                if err.errno == errorcode.ER_TABLE_EXISTS_ERROR:
                    print(f"Table {table_name} already exists.")
                else:
                    print(err.msg)
            else:
                print(f"Table {table_name} created successfully.")

        admin_username = 'panel_admin'
        admin_password = hash_password('Panel_Pass')
        admin_email = 'admin@hostfix.hu'
        add_admin = (
            "INSERT INTO users (username, password, email, role) "
            "VALUES (%s, %s, %s, 'admin')"
        )
        admin_data = (admin_username, admin_password, admin_email)
        cursor.execute(add_admin, admin_data)

        user_username = 'panel_user'
        user_password = hash_password('Panel_Code1123')
        user_email = 'user@hostfix.hu'
        add_user = (
            "INSERT INTO users (username, password, email, role) "
            "VALUES (%s, %s, %s, 'user')"
        )
        user_data = (user_username, user_password, user_email)
        cursor.execute(add_user, user_data)

        cnx.commit()

    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Invalid database username or password.")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist.")
        else:
            print(err)
    else:
        cursor.close()
        cnx.close()
        print("Database connection closed.")

if __name__ == "__main__":
    # Telepítsd a szükséges csomagokat
    install_required_packages()

    # A telepítéshez és konfiguráláshoz szükséges parancsok listája
    commands = [
        "sudo dnf install httpd -y",
        "sudo systemctl start httpd",
        "sudo systemctl enable httpd",
        "sudo dnf install epel-release -y",
        "sudo dnf install https://rpms.remirepo.net/enterprise/remi-release-9.rpm -y",
        "sudo dnf module reset php -y",
        "sudo dnf module enable php:remi-8.1 -y",
        "sudo dnf install php php-cli php-fpm php-mysqlnd php-opcache php-gd php-xml php-mbstring php-json php-curl -y",
        "sudo systemctl start php-fpm",
        "sudo systemctl enable php-fpm",
        "sudo dnf install mariadb-server mariadb -y",
        "sudo systemctl start mariadb",
        "sudo systemctl enable mariadb",
        "sudo mysql_secure_installation <<EOF\nn\ny\ny\ny\ny\nEOF",
        "sudo mysql -u root -e \"CREATE DATABASE hostmaster;\"",
        "sudo mysql -u root -e \"CREATE USER 'hostmaster_panel'@'localhost' IDENTIFIED BY 'erős_jelszó';\"",
        "sudo mysql -u root -e \"GRANT ALL PRIVILEGES ON hostmaster.* TO 'hostmaster_panel'@'localhost';\"",
        "sudo mysql -u root -e \"FLUSH PRIVILEGES;\"",
        "sudo dnf install phpMyAdmin -y",
        "echo -e '<VirtualHost *:8085>\\n    ServerAdmin admin@s8.hostfix.hu\\n    DocumentRoot /var/www/HostMaster/user\\n    ServerName s8.hostfix.hu\\n    <Directory /var/www/HostMaster/user>\\n        Options Indexes FollowSymLinks\\n        AllowOverride All\\n        Require all granted\\n    </Directory>\\n    ErrorLog /var/www/HostMaster/logs/error_user.log\\n    CustomLog /var/www/HostMaster/logs/access_user.log combined\\n</VirtualHost>\\n\\n<VirtualHost *:8086>\\n    ServerAdmin admin@s8.hostfix.hu\\n    DocumentRoot /var/www/HostMaster/admin\\n    ServerName admin.s8.hostfix.hu\\n    <Directory /var/www/HostMaster/admin>\\n        Options Indexes FollowSymLinks\\n        AllowOverride All\\n        Require all granted\\n    </Directory>\\n    ErrorLog /var/www/HostMaster/logs/error_admin.log\\n    CustomLog /var/www/HostMaster/logs/access_admin.log combined\\n</VirtualHost>' | sudo tee /etc/httpd/conf.d/hostmaster.conf",
        "sudo firewall-cmd --permanent --add-port=8085/tcp",
        "sudo firewall-cmd --permanent --add-port=8086/tcp",
        "sudo firewall-cmd --reload",
        "sudo systemctl restart httpd",
        "sudo chown -R apache:apache /var/www/HostMaster",
        "sudo chmod -R 755 /var/www/HostMaster",
        "sudo dnf install pure-ftpd -y",
        "sudo systemctl start pure-ftpd",
        "sudo systemctl enable pure-ftpd",
        "sudo sed -i 's/.*ChrootEveryone.*/ChrootEveryone              yes/' /etc/pure-ftpd/pure-ftpd.conf",
        "sudo sed -i 's/.*MaxClientsNumber.*/MaxClientsNumber            50/' /etc/pure-ftpd/pure-ftpd.conf",
        "sudo sed -i 's/.*VerboseLog.*/VerboseLog                  yes/' /etc/pure-ftpd/pure-ftpd.conf",
        "sudo sed -i 's/.*DisplayDotFiles.*/DisplayDotFiles             yes/' /etc/pure-ftpd/pure-ftpd.conf",
        "sudo systemctl restart pure-ftpd"
    ]

    # Futtassuk a parancsokat állapotjelző sávval
    for command in tqdm(commands, desc="Installation and configuration"):
        if not run_command(command):
            print(f"Command failed: {command}")
            sys.exit(1)  # Ha hiba történik, lépjünk ki a scriptből
        time.sleep(0.1)  # Csak a progress bar jobb megjelenítése érdekében

    # Adatbázis beállítása
    setup_database()
