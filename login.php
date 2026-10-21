
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#687653">
    <meta name="description" content="Inicia sesiÃ³n en Custom Knits y descubre prendas hechas para ti.">

    <title>Iniciar sesiÃ³n | Custom Knits</title>

    <link rel="stylesheet" href="login.css
    ">
    <link rel="stylesheet" href="frontend/css/login.css">
</head>

<body>
    <main class="contenedor">

        <a class="volver" href="index.php">
            â† Volver a la tienda
        </a>

        <section class="tarjeta" aria-labelledby="tituloLogin">

            <div class="marca">
                CUSTOM <span>KNITS</span>
            </div>

            <p class="etiqueta">BIENVENIDO DE NUEVO</p>

            <h1 id="tituloLogin">Inicia sesiÃ³n</h1>

            <p class="descripcion">
                Entra a tu cuenta y sigue descubriendo tu estilo.
            </p>

            <form id="formLogin" method="post">

                <label for="correo">Correo electrÃ³nico</label>

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

                <label for="contrasena">ContraseÃ±a</label>

                <input
                    id="contrasena"
                    name="contrasena"
                    type="password"
                    placeholder="Tu contraseÃ±a"
                    autocomplete="current-password"
                    required
                >

                <button id="btnLogin" type="submit">
                    Iniciar sesiÃ³n
                </button>

                <p
                    id="mensaje"
                    role="status"
                    aria-live="polite"
                ></p>

            </form>

            <p class="enlace">
                Â¿TodavÃ­a no tienes cuenta?
                <a href="registro.php">RegÃ­strate aquÃ­</a>
            </p>

        </section>

        <footer>
            Â© 2026 Custom Knits Â· DiseÃ±ado para ti
        </footer>

    </main>
<script src="frontend/js/login.js" defer></script>
</body>
</html>
