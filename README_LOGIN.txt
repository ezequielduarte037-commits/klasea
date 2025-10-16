Proyecto: Panel Klase A — Sistema de Login

- Base de datos MySQL
  - Importar php/init_db.sql
  - Crear credenciales en php/config.php o mediante variables de entorno DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT

- Flujo
  - Abrir /login.php -> POST a /php/auth.php
  - Éxito -> /panel.php con contenido personalizado
  - Cerrar sesión -> /php/auth.php?action=logout
  - Administración básica -> /admin.php (requiere sesión)

- Estructura
  /panel operaciones/index.html (boceto original, no usado directamente en login)
  /panel.php (panel dinámico respetando look & feel)
  /login.php (pantalla de acceso minimalista)
  /admin.php (ABM de usuarios)
  /php/config.php (DB + sesión)
  /php/auth.php (login/logout)
  /php/init_db.sql (esquema DB)
  /img/models/* imágenes por modelo (agregar)
  /img/logo.png (agregar)
