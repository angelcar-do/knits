
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#687653">
    <meta name="description" content="Inicia sesión en Custom Knits y descubre prendas hechas para ti.">

    <title>Iniciar sesión | Custom Knits</title>

    <link rel="stylesheet" href="login.css
    ">
    <script src="login.css" defer></script>
</head>

<body>
    <main class="contenedor">

        <a class="volver" href="index.php">
            ← Volver a la tienda
        </a>

        <section class="tarjeta" aria-labelledby="tituloLogin">

            <div class="marca">
                CUSTOM <span>KNITS</span>
            </div>

            <p class="etiqueta">BIENVENIDO DE NUEVO</p>

            <h1 id="tituloLogin">Inicia sesión</h1>

            <p class="descripcion">
                Entra a tu cuenta y sigue descubriendo tu estilo.
            </p>

            <form id="formLogin" method="post">

                <label for="correo">Correo electrónico</label>

                <input
                    id="correo"
                    name="correo"
                    type="email"
                    placeholder="nombre@correo.com"
                    maxlength="190"
                    autocomplete="email"
                    autocapitalize="none"
                    spellcheck="false"
                    required
                >

                <label for="contrasena">Contraseña</label>

                <input
                    id="contrasena"
                    name="contrasena"
                    type="password"
                    placeholder="Tu contraseña"
                    autocomplete="current-password"
                    required
                >

                <button id="btnLogin" type="submit">
                    Iniciar sesión
                </button>

                <p
                    id="mensaje"
                    role="status"
                    aria-live="polite"
                ></p>

            </form>

            <p class="enlace">
                ¿Todavía no tienes cuenta?
                <a href="registro.php">Regístrate aquí</a>
            </p>

        </section>

        <footer>
            © 2026 Custom Knits · Diseñado para ti
        </footer>

    </main>
</body>
</html>