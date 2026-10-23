
"use strict";

const CLAVE_CARRITO = "customKnitsCarrito";

const PRECIOS = {
    "monedero tejido": 45000,
    "vestido tejido": 200000,
    "gorro tejido": 25000,
    "mochila tejido": 125000
};

let carrito = cargarCarrito();

function cargarCarrito() {
    try {
        const datos = JSON.parse(localStorage.getItem(CLAVE_CARRITO) || "[]");
        if (!Array.isArray(datos)) return [];

        return datos.filter(p =>
            p &&
            typeof p.nombre === "string" &&
            Number.isFinite(p.precio) &&
            Number.isInteger(p.cantidad) &&
            p.cantidad > 0
        );
    } catch (error) {
        return [];
    }
}

function guardarCarrito() {
    try {
        localStorage.setItem(CLAVE_CARRITO, JSON.stringify(carrito));
    } catch (error) {
        console.warn("No se pudo guardar el carrito.", error);
    }
}

function formatoPrecio(valor) {
    return new Intl.NumberFormat("es-CO", {
        style: "currency",
        currency: "COP",
        maximumFractionDigits: 0
    }).format(valor);
}

function actualizarContador() {
    const contador = document.getElementById("contador");

    if (contador) {
        contador.textContent = carrito.reduce(
            (total, producto) => total + producto.cantidad,
            0
        );
    }
}

function agregarCarrito(producto, precio) {
    if (typeof producto !== "string" || !producto.trim()) {
        alert("No se pudo identificar el producto.");
        return;
    }

    const nombre = producto.trim();
    const clave = nombre.toLowerCase();

    const precioFinal = precio === undefined
        ? PRECIOS[clave]
        : Number(precio);

    if (!Number.isFinite(precioFinal) || precioFinal < 0) {
        alert("No se encontró un precio válido para " + nombre + ".");
        return;
    }

    const existente = carrito.find(
        p => p.nombre.toLowerCase() === clave
    );

    if (existente) {
        existente.cantidad++;
    } else {
        carrito.push({
            nombre: nombre,
            precio: precioFinal,
            cantidad: 1
        });
    }

    guardarCarrito();
    actualizarContador();

    mostrarAviso(nombre + " agregado al carrito");
}

function mostrarAviso(texto) {
    let aviso = document.getElementById("avisoCarrito");

    if (!aviso) {
        aviso = document.createElement("div");
        aviso.id = "avisoCarrito";

        Object.assign(aviso.style, {
            position: "fixed",
            bottom: "24px",
            right: "24px",
            zIndex: "10001",
            background: "#694b45",
            color: "#fff",
            padding: "14px 22px",
            borderRadius: "10px",
            boxShadow: "0 4px 16px #0003",
            fontSize: "14px",
            maxWidth: "calc(100% - 48px)"
        });

        document.body.appendChild(aviso);
    }

    aviso.textContent = "✓ " + texto;
    aviso.style.display = "block";

    clearTimeout(aviso._temporizador);
    aviso._temporizador = setTimeout(() => {
        aviso.style.display = "none";
    }, 2200);
}


function crearVentanaCarrito() {
    if (document.getElementById("ventanaCarrito")) return;

    const ventana = document.createElement("div");
    ventana.id = "ventanaCarrito";

    ventana.innerHTML = `
        <div class="carrito-panel"
             role="dialog"
             aria-modal="true"
             aria-labelledby="tituloCarrito">

            <div class="carrito-encabezado">
                <h2 id="tituloCarrito" class="carrito-titulo">
                    <span class="carrito-icono-titulo">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </span>
                    Tu carrito
                </h2>

                <button id="cerrarCarrito"
                        type="button"
                        aria-label="Cerrar carrito">
                    &times;
                </button>
            </div>

            <div id="listaCarrito"></div>

            <div class="carrito-resumen">
                <div class="carrito-resumen-linea">
                    <span>Resumen de tu compra</span>
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div id="totalCarrito">Total: $0</div>
            </div>

            <div class="carrito-acciones">
                <button id="vaciarCarrito"
                        class="carrito-boton carrito-boton-vaciar"
                        type="button">
                    <i class="fa-regular fa-trash-can"></i>
                    Vaciar carrito
                </button>

                <button id="seguirComprando"
                        class="carrito-boton carrito-boton-seguir"
                        type="button">
                    Seguir comprando
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(ventana);

    document.getElementById("cerrarCarrito").addEventListener(
        "click", cerrarVentanaCarrito
    );

    document.getElementById("seguirComprando").addEventListener(
        "click", cerrarVentanaCarrito
    );

    ventana.addEventListener("click", evento => {
        if (evento.target === ventana) {
            cerrarVentanaCarrito();
        }
    });

    document.getElementById("vaciarCarrito").addEventListener("click", () => {
        if (carrito.length === 0) {
            alert("Tu carrito ya está vacío.");
            return;
        }

        if (confirm("¿Seguro que deseas vaciar el carrito?")) {
            carrito = [];
            guardarCarrito();
            actualizarContador();
            renderizarCarrito();
        }
    });

    document.getElementById("listaCarrito").addEventListener(
        "click", evento => {
            const boton = evento.target.closest("button[data-accion]");
            if (!boton) return;

            const indice = Number(boton.dataset.indice);
            const producto = carrito[indice];

            if (!producto) return;

            if (boton.dataset.accion === "sumar") {
                producto.cantidad++;
            } else if (boton.dataset.accion === "restar") {
                producto.cantidad--;

                if (producto.cantidad <= 0) {
                    carrito.splice(indice, 1);
                }
            } else if (boton.dataset.accion === "eliminar") {
                carrito.splice(indice, 1);
            }

            guardarCarrito();
            actualizarContador();
            renderizarCarrito();
        }
    );
}


function cerrarVentanaCarrito() {
    const ventana = document.getElementById("ventanaCarrito");
    if (ventana) ventana.style.display = "none";
}

function abrirCarrito() {
    crearVentanaCarrito();
    renderizarCarrito();

    document.getElementById("ventanaCarrito").style.display = "flex";
}


function renderizarCarrito() {
    const lista = document.getElementById("listaCarrito");
    const totalElemento = document.getElementById("totalCarrito");

    if (!lista || !totalElemento) return;

    if (carrito.length === 0) {
        lista.innerHTML = `
            <div class="carrito-vacio">
                <i class="fa-solid fa-bag-shopping"></i>
                <strong>Tu carrito está vacío</strong>
                ¡Agrega tus tejidos favoritos y vuelve aquí!
            </div>
        `;

        totalElemento.textContent = "Total: " + formatoPrecio(0);
        return;
    }

    lista.replaceChildren();

    carrito.forEach((producto, indice) => {
        const fila = document.createElement("div");
        fila.className = "carrito-producto";

        const informacion = document.createElement("div");
        informacion.className = "carrito-producto-info";

        const detalle = document.createElement("div");
        detalle.className = "carrito-producto-detalle";

        if (producto.nombre.trim().toLowerCase() === "monedero tejido") {
            const imagen = document.createElement("img");
            imagen.className = "carrito-producto-imagen";
            imagen.src = "img/Monedero.jpg";
            imagen.alt = "Monedero tejido";
            detalle.appendChild(imagen);
        }

        
        if (producto.nombre.trim().toLowerCase() === "mochila tejida") {
            const imagen = document.createElement("img");
            imagen.className = "carrito-producto-imagen";
            imagen.src = "img/Mochila.jpg";
            imagen.alt = "Mochila tejida";
            detalle.appendChild(imagen);
        }

         if (producto.nombre.trim().toLowerCase() === "gorro tejido") {
            const imagen = document.createElement("img");
            imagen.className = "carrito-producto-imagen";
            imagen.src = "img/Gorro.jpg";
            imagen.alt = "Gorro tejido";
            detalle.appendChild(imagen);
        }

         if (producto.nombre.trim().toLowerCase() === "vestido tejido") {
            const imagen = document.createElement("img");
            imagen.className = "carrito-producto-imagen";
            imagen.src = "img/Vestido.jpg";
            imagen.alt = "Vestido tejido";
            detalle.appendChild(imagen);
        }

        const nombre = document.createElement("strong");
        nombre.className = "carrito-producto-nombre";
        nombre.textContent = producto.nombre;

        const precio = document.createElement("p");
        precio.className = "carrito-producto-precio";
        precio.textContent = formatoPrecio(producto.precio) + " por unidad";

        const subtotal = document.createElement("p");
        subtotal.className = "carrito-producto-subtotal";
        subtotal.textContent =
            "Subtotal: " + formatoPrecio(producto.precio * producto.cantidad);

        informacion.append(nombre, precio, subtotal);
        detalle.appendChild(informacion);

        const controles = document.createElement("div");
        controles.className = "carrito-producto-controles";

        const cantidad = document.createElement("div");
        cantidad.className = "carrito-cantidad";

        const botonRestar = document.createElement("button");
        botonRestar.type = "button";
        botonRestar.textContent = "−";
        botonRestar.dataset.accion = "restar";
        botonRestar.dataset.indice = indice;
        botonRestar.setAttribute("aria-label", "Disminuir cantidad");

        const numeroCantidad = document.createElement("span");
        numeroCantidad.textContent = producto.cantidad;

        const botonSumar = document.createElement("button");
        botonSumar.type = "button";
        botonSumar.textContent = "+";
        botonSumar.dataset.accion = "sumar";
        botonSumar.dataset.indice = indice;
        botonSumar.setAttribute("aria-label", "Aumentar cantidad");

        cantidad.append(botonRestar, numeroCantidad, botonSumar);

        const botonEliminar = document.createElement("button");
        botonEliminar.type = "button";
        botonEliminar.className = "carrito-eliminar";
        botonEliminar.dataset.accion = "eliminar";
        botonEliminar.dataset.indice = indice;
        botonEliminar.innerHTML =
            '<i class="fa-regular fa-trash-can"></i> Eliminar';

        controles.append(cantidad, botonEliminar);
        fila.append(detalle, controles);
        lista.appendChild(fila);
    });

    const total = carrito.reduce(
        (suma, producto) => suma + producto.precio * producto.cantidad,
        0
    );

    totalElemento.textContent = "Total: " + formatoPrecio(total);
}


function obtenerCarrito() {
    return carrito.map(producto => ({ ...producto }));
}

function calcularTotalCarrito() {
    return carrito.reduce(
        (total, producto) => total + producto.precio * producto.cantidad,
        0
    );
}

function vaciarCarrito() {
    carrito = [];
    guardarCarrito();
    actualizarContador();
    renderizarCarrito();
}

function verProductos() {
    const productos = document.getElementById("productos");

    if (productos) {
        productos.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }
}

function solicitarPersonalizado() {
    alert(
        "¡Gracias por tu interés en Custom Knits! " +
        "Próximamente podrás solicitar tus prendas personalizadas."
    );
}

document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelectorAll(
        ".hero-imagen .hero-slide"
    );

    if (slides.length === 0) return;

    let actual = 0;
    let temporizador = null;

    const DURACION_IMAGEN = 5000;

    function limpiarTemporizador() {
        if (temporizador !== null) {
            clearTimeout(temporizador);
            temporizador = null;
        }
    }

    function mostrarSlide(indice) {
        limpiarTemporizador();
        slides.forEach(slide => {
            slide.classList.remove("activo");

            if (slide.tagName === "VIDEO") {
                slide.pause();
                slide.onended = null;
                slide.onerror = null;
            }
        });

        actual = indice;
        const slideActual = slides[actual];

        slideActual.classList.add("activo");


        if (slideActual.tagName !== "VIDEO") {
            temporizador = setTimeout(() => {
                siguienteSlide();
            }, DURACION_IMAGEN);

            return;
        }

        slideActual.muted = true;
        slideActual.playsInline = true;
        slideActual.currentTime = 0;

        slideActual.onended = () => {
            siguienteSlide();
        };

        slideActual.onerror = () => {
            console.error(
                "No se pudo cargar el video:",
                slideActual.querySelector("source")?.src
            );

            siguienteSlide();
        };

        const reproduccion = slideActual.play();

        if (reproduccion !== undefined) {
            reproduccion.catch(error => {
                console.error(
                    "No se pudo reproducir el video:",
                    error
                );

                if (slideActual.classList.contains("activo")) {
                    temporizador = setTimeout(() => {
                        siguienteSlide();
                    }, DURACION_IMAGEN);
                }
            });
        }
    }

    function siguienteSlide() {
        const siguiente = (actual + 1) % slides.length;
        mostrarSlide(siguiente);
    }

    mostrarSlide(0);
});

async function cargarProductosDesdeMongoDB() {
    const contenedor = document.getElementById("productosGrid");

    if (!contenedor) return;

    try {
        const respuesta = await fetch("backend/productos/listar.php");

        if (!respuesta.ok) {
            throw new Error("No se pudieron consultar los productos.");
        }

        const datos = await respuesta.json();

        if (!datos.ok || !Array.isArray(datos.productos)) {
            throw new Error("La respuesta del catálogo no es válida.");
        }

        if (datos.productos.length === 0) {
            contenedor.textContent = "Todavía no hay productos disponibles.";
            return;
        }

        contenedor.replaceChildren();

        datos.productos.forEach(producto => {
            const tarjeta = document.createElement("article");
            tarjeta.className = "producto";

            const imagenContenedor = document.createElement("div");
            imagenContenedor.className = "imagen-producto";

            const imagen = document.createElement("img");
            imagen.src = producto.imagen;
            imagen.alt = producto.nombre;

            const favorito = document.createElement("button");
            favorito.className = "favorito";
            favorito.type = "button";
            favorito.setAttribute("aria-label", "Marcar como favorito");

            const corazon = document.createElement("i");
            corazon.className = "fa-regular fa-heart";
            favorito.appendChild(corazon);

            imagenContenedor.append(imagen, favorito);

            const informacion = document.createElement("div");
            informacion.className = "info-producto";

            const nombre = document.createElement("h3");
            nombre.textContent = producto.nombre;

            const precio = document.createElement("p");
            precio.className = "precio";
            precio.textContent = formatoPrecio(Number(producto.precio));

            const boton = document.createElement("button");
            boton.type = "button";
            boton.innerHTML =
                '<i class="fa-solid fa-cart-shopping"></i> Agregar al carrito';

            boton.addEventListener("click", () => {
                agregarCarrito(producto.nombre, Number(producto.precio));
            });

            informacion.append(nombre, precio, boton);
            tarjeta.append(imagenContenedor, informacion);
            contenedor.appendChild(tarjeta);
        });

    } catch (error) {
        console.error("Error cargando el catálogo:", error);
        contenedor.textContent =
            "No pudimos cargar los productos. Recarga la página para intentarlo de nuevo.";
    }
}

document.addEventListener("DOMContentLoaded", cargarProductosDesdeMongoDB);
