CREATE DATABASE IF NOT EXISTS tarzi_signage
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

DROP USER IF EXISTS 'tarzi'@'localhost';
DROP USER IF EXISTS 'tarzi'@'127.0.0.1';

CREATE USER 'tarzi'@'localhost' IDENTIFIED BY 'Tarzi_Signage_2026!';
CREATE USER 'tarzi'@'127.0.0.1' IDENTIFIED BY 'Tarzi_Signage_2026!';

GRANT ALL PRIVILEGES ON tarzi_signage.* TO 'tarzi'@'localhost';
GRANT ALL PRIVILEGES ON tarzi_signage.* TO 'tarzi'@'127.0.0.1';

FLUSH PRIVILEGES;
