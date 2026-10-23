
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear cuenta | Custom Knits</title>

    <meta name="description" content="Crea tu cuenta en Custom Knits y descubre prendas hechas para ti.">

      <link rel="stylesheet" href="registro.css?v=2">
    <script src="registro.js" defer></script>
</head>

<body>
    <main class="registro-contenedor">

        <a class="volver" href="index.php">
            Volver a la tienda
        </a>

        <section class="registro-tarjeta">

            <div class="marca">CUSTOM <span>KNITS</span></div>

            <p class="etiqueta">TU ESTILO, TU HISTORIA</p>

            <h1>Crea tu cuenta</h1>

            <p class="descripcion">
                Registrate y descubre prendas hechas para ti.
            </p>

            <form id="formRegistro" autocomplete="on">

                <label for="nombre">Nombre completo</label>
                <input
                    id="nombre"
                    name="nombre"
                    type="text"
                    placeholder="Tu nombre completo"
                    maxlength="100"
                    autocomplete="name"
                    required
                >

                <label for="correo">Correo electrónico</label>
                <input
                    id="correo"
                    name="correo"
                    type="email"
                    placeholder="nombre@correo.com"
                    maxlength="190"
                    autocomplete="email"
                    required
                >

                <label for="contrasena">Contraseña</label>
                <input
                    id="contrasena"
                    name="contrasena"
                    type="password"
                    placeholder="Mínimo 8 caracteres"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

                <label for="confirmar">Confirmar contraseña</label>
                <input
                    id="confirmar"
                    name="confirmar"
                    type="password"
                    placeholder="Repite tu contraseña"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

                <button id="btnRegistro" type="submit">
                    Crear mi cuenta
                </button>

                <p
                    id="mensaje"
                    role="status"
                    aria-live="polite"
                ></p>

            </form>

            <p class="enlace-login">
                ¿Ya tienes una cuenta?
                <a href="login.php">Inicia Sesión</a>
            </p>

        </section>

        <footer>
            Â© 2026 Custom Knits Diseñado para ti
        </footer>

    </main>
</body>
</html>
