# UGB - Sistema de Inscripción de Nuevos Estudiantes

**Parcial Segundo Cómputo – Programación Computacional IV**  
Universidad Gerardo Barrios

Estudiantes
Juan Ramón Espinal Coto SMSS102323
Franklin Aldahir Portillo Flores SMSS011624

---

## Árbol de Archivos

```
ugb_inscripcion/
├── config/
│   └── db.php               # Conexión PDO a la BD
├── css/
│   └── style.css            # Estilos personalizados
├── sql/
│   └── database.sql         # Script de creación de BD e inserción de datos
├── index.php                # Página pública (vista de estudiantes)
├── login.php                # Formulario de inicio de sesión
├── logout.php               # Cierre de sesión
├── registro.php             # Formulario protegido de inscripción (solo admin)
├── crear_admin.php          # Script de un solo uso para crear usuario admin
└── README.md                # Este archivo
```

---

##  Instrucciones de Instalación (XAMPP)

1. Copiar la carpeta `ugb_inscripcion/` dentro de `C:\xampp\htdocs\`
2. Iniciar **Apache** y **MySQL** en XAMPP Control Panel
3. Abrir **phpMyAdmin**: `http://localhost/phpmyadmin`
4. Crear la base de datos ejecutando el archivo `sql/database.sql`
5. Visitar `http://localhost/ugb_inscripcion/crear_admin.php` para crear el usuario admin
6. **Eliminar `crear_admin.php`** después del paso anterior
7. Acceder en: `http://localhost/ugb_inscripcion/`

**Credenciales de acceso:**  
- Usuario: `admin`  
- Contraseña: `admin123`

---

## Preguntas de Análisis

### 1. ¿Cómo manejan la conexión a la BD y qué pasa si algunos datos son incorrectos? ¿Cómo validan la conexión?

La conexión se realiza usando **PDO (PHP Data Objects)** con el driver MySQL, configurado en `config/db.php`. Se utiliza `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` para que cualquier error lance una excepción que se captura con un bloque `try/catch`.

Si los datos de conexión son incorrectos (host, nombre de BD, usuario o contraseña), el `catch (PDOException $e)` intercepta el error y muestra un mensaje genérico al usuario sin revelar detalles técnicos, lo cual es importante por seguridad. En desarrollo puede mostrarse `$e->getMessage()` para depuración.

**Justificación:** PDO es preferible sobre `mysqli_connect()` directo porque permite prepared statements en cualquier motor de BD, manejo unificado de errores mediante excepciones, y mayor portabilidad si se cambia el motor de base de datos.

Para la validación de datos ingresados en formularios, se aplica validación en el servidor (`registro.php`):
- Campos obligatorios verificados con `empty()` y `strlen()`
- Teléfono validado con expresión regular `preg_match('/^\d{8}$/', ...)`
- Enum de turno verificado con `in_array()`
- Fecha validada con `strtotime()`
- Existencia de carrera verificada con consulta antes de insertar

---

### 2. ¿Cuál es la diferencia entre `$_GET` y `$_POST` en PHP? ¿Cuándo es más apropiado usar cada uno? Ejemplo real del proyecto.

| Característica | `$_GET` | `$_POST` |
|---|---|---|
| Datos enviados en | URL (query string) | Cuerpo de la petición HTTP |
| Visible para el usuario | Sí | No |
| Límite de tamaño | ~2000 caracteres | Sin límite práctico |
| Puede guardarse en historial | Sí | No |
| Uso recomendado | Búsquedas, filtros, paginación | Formularios con datos sensibles |

**Cuándo usar cada uno:**
- `$_GET`: Para peticiones que no modifican datos, como filtrar estudiantes por carrera o buscar un registro. Los parámetros en la URL permiten compartir el enlace.
- `$_POST`: Para formularios que insertan, modifican o eliminan datos, o cuando se manejan datos sensibles como contraseñas.

**Ejemplo real del proyecto:**
- En `login.php` se usa `$_POST` porque se envía la contraseña del usuario. Si se usara `$_GET`, la contraseña quedaría visible en la URL (`?username=admin&password=admin123`), en el historial del navegador y en los logs del servidor.
- En `registro.php` también se usa `$_POST` para enviar los datos del estudiante, ya que se trata de una operación de escritura en la base de datos.

---

### 3. Tu app va a usarse en una empresa de la zona oriental. ¿Qué riesgos de seguridad identificas y cómo los mitigarían?

**Riesgos identificados:**

**a) SQL Injection**  
Sin prepared statements, un atacante podría ingresar `' OR '1'='1` en el login para bypassearlo.  
*Mitigación:* Usamos `$pdo->prepare()` con `execute([$param])` en todas las consultas. Nunca se concatenan variables directamente en el SQL.

**b) XSS (Cross-Site Scripting)**  
Un usuario podría ingresar `<script>alert('hack')</script>` como nombre.  
*Mitigación:* Se usa `htmlspecialchars()` en toda salida a HTML, convirtiendo caracteres especiales en entidades HTML inofensivas.

**c) Contraseñas en texto plano**  
Guardar contraseñas sin cifrar expone a todos los usuarios si la BD es comprometida.  
*Mitigación:* Se usa `password_hash()` con `PASSWORD_BCRYPT` para almacenar y `password_verify()` para comparar.

**d) Acceso no autorizado a páginas protegidas**  
Alguien podría navegar directamente a `registro.php`.  
*Mitigación:* Cada página protegida verifica `$_SESSION['usuario_id']` al inicio y redirige al login si no está activo.

**e) Exposición de errores en producción**  
Los mensajes de error de PHP pueden revelar rutas, versiones o estructuras internas.  
*Mitigación:* En producción configurar `display_errors = Off` en `php.ini` y registrar errores solo en logs internos.

**f) Fuerza bruta en el login**  
Un atacante podría intentar miles de combinaciones de contraseñas.  
*Mejora propuesta:* Implementar un contador de intentos fallidos en sesión o BD y bloquear temporalmente tras N intentos.

---

## Diccionario de Datos

### Tabla: `carreras`

| Columna | Tipo de dato | Límite de caracteres | ¿Es nulo? | Descripción |
|---|---|---|---|---|
| id | INT (AUTO_INCREMENT) | — | NO | Identificador único de la carrera (PK) |
| nombre | VARCHAR | 100 | NO | Nombre de la carrera universitaria |
| facultad | VARCHAR | 100 | NO | Facultad a la que pertenece la carrera |
| duracion_anios | INT | — | NO | Duración de la carrera en años |
| descripcion | TEXT | 65,535 | **SÍ** | Descripción opcional de la carrera |

---

### Tabla: `estudiantes`

| Columna | Tipo de dato | Límite de caracteres | ¿Es nulo? | Descripción |
|---|---|---|---|---|
| id | INT (AUTO_INCREMENT) | — | NO | Identificador único del estudiante (PK) |
| nombre | VARCHAR | 80 | NO | Primer nombre del estudiante |
| apellido | VARCHAR | 80 | NO | Apellido del estudiante |
| carrera_id | INT | — | NO | Referencia a la carrera inscrita (FK → carreras.id) |
| turno | ENUM | — | NO | Turno de estudio: Matutino, Vespertino o Nocturno |
| telefono | VARCHAR | 15 | **SÍ** | Número de teléfono (opcional) |
| fecha_inscripcion | DATE | — | NO | Fecha en que el estudiante fue inscrito |

---

### Tabla: `usuarios`

| Columna | Tipo de dato | Límite de caracteres | ¿Es nulo? | Descripción |
|---|---|---|---|---|
| id | INT (AUTO_INCREMENT) | — | NO | Identificador único del usuario (PK) |
| username | VARCHAR | 50 | NO | Nombre de usuario único para el login |
| password | VARCHAR | 255 | NO | Contraseña cifrada con bcrypt |

---

*Sistema desarrollado para el Parcial Segundo Cómputo – Semana 13*  
*Programación Computacional IV – Universidad Gerardo Barrios*
