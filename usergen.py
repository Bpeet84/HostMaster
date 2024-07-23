import mysql.connector
from mysql.connector import errorcode
import bcrypt

# Adatbázis kapcsolat beállításai
db_config = {
    'user': 'hostmaster_panel',
    'password': 'erős_jelszó',
    'host': 'localhost',
    'database': 'hostmaster'
}

# Jelszavak
admin_password = 'Panel_Pass'
user_password = 'Panel_Code1123'

# Jelszavak hashelése
hashed_admin_password = bcrypt.hashpw(admin_password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
hashed_user_password = bcrypt.hashpw(user_password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')

# Csatlakozás az adatbázishoz
try:
    cnx = mysql.connector.connect(**db_config)
    cursor = cnx.cursor()
    print("Sikeresen csatlakoztál az adatbázishoz.")

    # Admin felhasználó hozzáadása
    add_admin = (
        "INSERT INTO users (username, password, email, role) "
        "VALUES (%s, %s, %s, 'admin')"
    )
    admin_data = ('panel_admin', hashed_admin_password, 'admin@hostfix.hu')
    cursor.execute(add_admin, admin_data)

    # Felhasználó hozzáadása
    add_user = (
        "INSERT INTO users (username, password, email, role) "
        "VALUES (%s, %s, %s, 'user')"
    )
    user_data = ('panel_user', hashed_user_password, 'user@hostfix.hu')
    cursor.execute(add_user, user_data)

    # Módosítások mentése
    cnx.commit()

except mysql.connector.Error as err:
    if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
        print("Hibás felhasználónév vagy jelszó.")
    elif err.errno == errorcode.ER_BAD_DB_ERROR:
        print("Az adatbázis nem létezik.")
    else:
        print(err)
else:
    cursor.close()
    cnx.close()
    print("Adatbázis kapcsolat bezárva.")
