<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Custom Knits</title>

    <link rel="stylesheet" href="frontend/css/estil.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <header class="header">

        <div class="logo">
            <img src="img/logo.jpg" alt="Logo Custom Knits">
            <div>
                <h1>Custom Knits</h1>
                <span>TEJIDOS CON AMOR</span>
            </div>
        </div>

        <nav class="menu">
            <a href="#inicio" class="activo">Inicio</a>
            <a href="#productos">Productos</a>
            <a href="#personalizados">Personalizados</a>
            <a href="#nosotros">Sobre nosotros</a>
            <a href="#contacto">Contacto</a>
        </nav>
        <a href="backend/usuarios/logout.php">Cerrar sesiÃ³n</a>

        <div class="iconos">
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-regular fa-user"></i>

            <div class="carrito">
                <i class="fa-solid fa-cart-shopping"></i>
                <span id="contador">0</span>
            </div>
        </div>

    </header>


    <section class="hero" id="inicio">

        <div class="hero-texto">

            <p class="bienvenida">
                BIENVENIDO A CUSTOM KNITS
            </p>

            <h2>
                Prendas tejidas a mano
                <br>
                que cuentan historias
            </h2>

            <p>
                Descubre nuestra colecciÃ³n de prendas y accesorios tejidos
                con amor, diseÃ±ados para brindarte comodidad, estilo y un
                toque Ãºnico.
            </p>

            <button onclick="verProductos()">
                Ver productos
                <i class="fa-solid fa-arrow-right"></i>
            </button>

        </div>

        <div class="hero-imagen">

    <img
        class="hero-slide activo"
        src="img/tejido.jpg"
        alt="Prendas tejidas a mano">

    <img
        class="hero-slide"
        src="img/gorro.jpg"
        alt="Tejidos personalizados">

    <video
        class="hero-slide"
        muted
        playsinline
        preload="auto">
        <source src="img/vdtejido.mp4" type="video/mp4">
    </video>

</div>

    </section>


    <section class="categorias">

        <div class="categoria">
            <i class="fa-solid fa-shirt"></i>
            <p>SuÃ©teres</p>
        </div>

        <div class="categoria">
            <i class="fa-solid fa-hat-cowboy"></i>
            <p>Gorros</p>
        </div>

        <div class="categoria">
            <i class="fa-solid fa-mitten"></i>
            <p>Bufandas</p>
        </div>

        <div class="categoria">
            <i class="fa-solid fa-bag-shopping"></i>
            <p>Bolsos</p>
        </div>

        <div class="categoria">
            <i class="fa-solid fa-child-reaching"></i>
            <p>Ropa para bebÃ©</p>
        </div>

        <div class="categoria">
            <i class="fa-solid fa-gift"></i>
            <p>Personalizados</p>
        </div>

    </section>


    <section class="productos" id="productos">

        <div class="titulo-seccion">
            <span></span>
            <h2>Productos destacados</h2>
            <span></span>
        </div>

        <p class="subtitulo">
            DiseÃ±os Ãºnicos, hechos para ti
        </p>


        <div class="productos-grid">

    
            <article class="producto">

                <div class="imagen-producto">
                    <img src="img/monedero.jpg" alt="SuÃ©ter tejido">
                    <button class="favorito">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </div>

                <div class="info-producto">
                    <h3>Monedero tejido</h3>
                    <p class="precio">$45.000</p>

                    <button onclick="agregarCarrito('monedero tejido')">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Agregar al carrito
                    </button>
                </div>

            </article>

            <article class="producto">

                <div class="imagen-producto">
                    <img src="img/vestido.jpg" alt="Gorro de lana">
                    <button class="favorito">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </div>

                <div class="info-producto">
                    <h3>Vestido tejido</h3>
                    <p class="precio">$200.000</p>

                    <button onclick="agregarCarrito('Vestido tejido')">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Agregar al carrito
                    </button>
                </div>

            </article>


            <article class="producto">

                <div class="imagen-producto">
                    <img src="img/gorro.jpg" alt="Gorro tejido">
                    <button class="favorito">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </div>

                <div class="info-producto">
                    <h3>Gorro tejido</h3>
                    <p class="precio">$25.000</p>

                    <button onclick="agregarCarrito('Gorro tejido')">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Agregar al carrito
                    </button>
                </div>

            </article>


            <article class="producto">

                <div class="imagen-producto">
                    <img src="img/mochila.jpg" alt="Mochila tejido">
                    <button class="favorito">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </div>

                <div class="info-producto">
                    <h3>Mochila tejida</h3>
                    <p class="precio">$125.000</p>

                    <button onclick="agregarCarrito('Mochila tejido')">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Agregar al carrito
                    </button>
                </div>

            </article>

        </div>

    </section>


    <section class="personalizacion" id="personalizados">

        <div class="personalizacion-texto">

            <p>HAZLO ÃšNICO</p>

            <h2>Personaliza tu pedido</h2>

            <p>
                Elige colores, diseÃ±os y tallas.
                Creamos la prenda perfecta segÃºn tus gustos y necesidades.
            </p>

            <button onclick="solicitarPersonalizado()">
                Solicitar personalizado
                <i class="fa-solid fa-arrow-right"></i>
            </button>

        </div>

        <div class="personalizacion-imagenes">

            <img src="img/personalizado.png" alt="Tejido personalizado">
            <img src="img/personal.png" alt="Productos tejidos">

        </div>

    </section>



    <section class="sobre-nosotros" id="nosotros">

        <div class="titulo-seccion">
            <span></span>
            <h2>Sobre nosotros</h2>
            <span></span>
        </div>

        <p>
            En Custom Knits tejemos cada prenda a mano, con cariÃ±o y atenciÃ³n
            al detalle. Desde Cartagena creamos piezas Ãºnicas, pensadas para
            ti y hechas a tu medida.
        </p>

    </section>


    <footer id="contacto">

        <div class="footer-contenido">

            <div class="footer-logo">
                <img src="img/logo.jpg" alt="Logo Custom Knits">
                <h2>Custom Knits</h2>
                <p>TEJIDOS CON AMOR</p>
            </div>

            <div>
                <h3>Enlaces rÃ¡pidos</h3>
                <a href="#inicio">Inicio</a>
                <a href="#productos">Productos</a>
                <a href="#personalizados">Personalizados</a>
                <a href="#contacto">Contacto</a>
            </div>

            <div>
                <h3>ContÃ¡ctanos</h3>
                <p>customknits@gmail.com</p>
                <p>+57 3019577903</p>
                <p>Cartagena, BolÃ­var</p>
            </div>

            <div>
                <h3>SuscrÃ­bete</h3>
                <p>Recibe nuestras novedades y ofertas especiales.</p>

                <div class="suscripcion">
                    <input
                        type="email"
                        placeholder="Tu correo electrÃ³nico">

                    <button>Suscribirme</button>
                </div>
            </div>

        </div>

        <div class="footer-final">
            <p>Â© 2026 Custom Knits. Todos los derechos reservados.</p>
        </div>

    </footer>


    <script src="frontend/js/script.js"></script>

</body>
</html>
