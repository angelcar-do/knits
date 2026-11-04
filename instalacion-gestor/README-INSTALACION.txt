CUSTOM KNITS — GESTOR VISUAL DE PRODUCTOS

Contenido del paquete:
- admin.php (reemplaza el admin.php de la raíz)
- backend/productos/admin_listar.php
- backend/productos/guardar.php
- backend/productos/accion.php
- uploads/productos/.htaccess

INSTALACIÓN SEGURA
1. Haz una copia de seguridad de tu admin.php actual.
2. Copia el admin.php de este paquete a la raíz del proyecto, reemplazando el anterior.
3. Copia los tres PHP de backend/productos/ a la carpeta backend/productos/ existente. No reemplaces listar.php.
4. Copia uploads/productos/.htaccess y asegúrate de que el servidor pueda escribir en uploads/productos. En Windows con XAMPP normalmente basta con crear la carpeta.
5. Comprueba que ya existen backend/config/admin.php, backend/config/csrf.php, backend/conexion_mongodb.php, backend/config/auth.php y backend/config/sesion.php.
6. Inicia sesión con un usuario cuyo rol sea exactamente admin y abre admin.php.

USO
- Entra en la pestaña Productos.
- Agregar producto: nombre, precio, categoría y fotografía (JPG/PNG/WEBP, máximo 5 MB).
- Editar permite conservar la imagen actual o seleccionar otra.
- Ocultar/Eliminar no borra físicamente el documento: marca activo=false para conservar referencias históricas de pedidos.

IMPORTANTE
- La tienda pública debe seguir leyendo productos con activo=true mediante backend/productos/listar.php.
- Este paquete usa los campos existentes: codigo, nombre, descripcion, precio, imagen, categoria, activo.
- La ruta de imagen se guarda como uploads/productos/nombre-aleatorio.ext.
- El paquete no incluye credenciales ni cambia la configuración de MongoDB.
- Verifica límites PHP upload_max_filesize y post_max_size si subes archivos mayores.
