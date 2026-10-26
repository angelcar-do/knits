
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
        const datos = JSON.parse(
            localStorage.getItem(CLAVE_CARRITO) || "[]"
        );

        if (!Array.isArray(datos)) return [];

        const CODIGOS_PRODUCTOS = {
            "monedero tejido": "monedero-tejido",
            "vestido tejido": "vestido-tejido",
            "gorro tejido": "gorro-tejido",
            "mochila tejida": "mochila-tejida",
            "mochila tejido": "mochila-tejida"
        };

        return datos
            .filter(p =>
                p &&
                typeof p.nombre === "string" &&
                Number.isFinite(p.precio) &&
                Number.isInteger(p.cantidad) &&
                p.cantidad > 0
            )
            .map(p => ({
                ...p,
                codigo: CODIGOS_PRODUCTOS[
                    p.nombre.trim().toLowerCase()
                ] || p.codigo
            }))
            .filter(p => typeof p.codigo === "string");
    } catch (error) {
        console.error("Error cargando el carrito:", error);
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

  const CODIGOS_PRODUCTOS = {
    "monedero tejido": "monedero-tejido",
    "vestido tejido": "vestido-tejido",
    "gorro tejido": "gorro-tejido",
    "mochila tejida": "mochila-tejida",
    "mochila tejido": "mochila-tejida"
};

const codigo = CODIGOS_PRODUCTOS[clave];

if (!codigo) {
    alert("No se pudo identificar el código del producto.");
    return;
}


if (existente) {
    existente.codigo = codigo;
    existente.cantidad++;
} else {
    carrito.push({
        codigo: codigo,
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

    Object.assign(ventana.style, {
        display: "none",
        position: "fixed",
        inset: "0",
        zIndex: "10000",
        background: "rgba(0,0,0,0.5)",
        justifyContent: "center",
        alignItems: "center",
        padding: "16px"
    });

    ventana.innerHTML = `
        <div role="dialog" aria-modal="true"
             aria-labelledby="tituloCarrito"
             style="background:#fffaf7; color:#493631;
             padding:24px; border-radius:14px; width:100%;
             max-width:650px; max-height:85vh; overflow:auto;
             box-sizing:border-box; font-family:Arial,sans-serif;">

            <div style="display:flex; justify-content:space-between;
                 align-items:center; gap:12px;">
                <h2 id="tituloCarrito" style="margin:0;">
                    <i class="fa-solid fa-cart-shopping"></i>
                    Tu carrito
                </h2>
                <button id="cerrarCarrito" type="button"
                    aria-label="Cerrar carrito"
                    style="border:0; background:none; font-size:26px;
                    cursor:pointer; color:#694b45;">&times;</button>
            </div>

            <div id="listaCarrito" style="margin-top:20px;"></div>

            <div id="totalCarrito"
                 style="font-size:20px; font-weight:bold;
                 text-align:right; margin-top:20px;"></div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;
                 justify-content:flex-end; margin-top:20px;">
                <button id="vaciarCarrito" type="button"
                    style="border:1px solid #694b45; color:#694b45;
                    background:transparent; padding:11px 16px;
                    border-radius:8px; cursor:pointer;">
                    Vaciar carrito
                </button>
                <button id="seguirComprando" type="button"
                    style="border:0; color:white; background:#694b45;
                    padding:11px 16px; border-radius:8px;
                    cursor:pointer;">
                    Seguir comprando
                </button>
                <button id="finalizarCompra" type="button"
    style="border:0; color:white; background:#80634f;
    padding:11px 16px; border-radius:8px;
    cursor:pointer;">
    Finalizar compra
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
    document.getElementById("finalizarCompra").addEventListener(
    "click", finalizarCompra
);

    ventana.addEventListener("click", evento => {
        if (evento.target === ventana) cerrarVentanaCarrito();
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
        lista.textContent = "Tu carrito está vacío. ¡Agrega tus productos favoritos!";
        totalElemento.textContent = "Total: " + formatoPrecio(0);
        return;
    }

    lista.innerHTML = "";

    carrito.forEach((producto, indice) => {
        const fila = document.createElement("div");

        Object.assign(fila.style, {
            display: "grid",
            gridTemplateColumns: "minmax(0,1fr) auto",
            gap: "12px",
            padding: "15px 0",
            borderBottom: "1px solid #eadbd6"
        });

        const informacion = document.createElement("div");
        const nombre = document.createElement("strong");
        nombre.textContent = producto.nombre;

        const precio = document.createElement("p");
        precio.style.margin = "6px 0";
        precio.textContent = formatoPrecio(producto.precio) + " por unidad";

        const subtotal = document.createElement("p");
        subtotal.style.margin = "6px 0";
        subtotal.textContent =
            "Subtotal: " + formatoPrecio(producto.precio * producto.cantidad);

        informacion.append(nombre, precio, subtotal);

        const controles = document.createElement("div");
        controles.style.textAlign = "right";

        const cantidad = document.createElement("p");
        cantidad.textContent = "Cantidad: " + producto.cantidad;

        const botones = document.createElement("div");
        botones.style.display = "flex";
        botones.style.gap = "5px";
        botones.style.flexWrap = "wrap";
        botones.style.justifyContent = "flex-end";

        [
            ["−", "restar"],
            ["+", "sumar"],
            ["Eliminar", "eliminar"]
        ].forEach(([texto, accion]) => {
            const boton = document.createElement("button");
            boton.type = "button";
            boton.textContent = texto;
            boton.dataset.accion = accion;
            boton.dataset.indice = indice;

            Object.assign(boton.style, {
                border: "1px solid #d8b8ae",
                borderRadius: "6px",
                background: "#f4e7e1",
                color: "#493631",
                padding: "7px 10px",
                cursor: "pointer"
            });

            botones.appendChild(boton);
        });

        controles.append(cantidad, botones);
        fila.append(informacion, controles);
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

async function finalizarCompra() {
    if (carrito.length === 0) {
        alert("Tu carrito está vacío.");
        return;
    }

    const confirmar = confirm(
        "¿Deseas registrar el pedido por " +
        formatoPrecio(calcularTotalCarrito()) + "?"
    );

    if (!confirmar) return;

    try {
   const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

if (!csrfToken) {
    alert("No se pudo validar la seguridad. Recarga la página.");
    return;
}

const respuesta = await fetch("backend/pedidos/crear.php", {
    method: "POST",
    credentials: "same-origin",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": csrfToken
    },
    body: JSON.stringify({
        productos: carrito.map(producto => ({
            codigo: producto.codigo,
            cantidad: producto.cantidad
        }))
    })
});

        const datos = await respuesta.json();

        if (!respuesta.ok || !datos.ok) {
            alert(datos.mensaje || "No se pudo registrar el pedido.");
            return;
        }

        alert(
            "¡Pedido registrado correctamente!\n" +
            "Número: " + datos.pedido_id + "\n" +
            "Total: " + formatoPrecio(datos.total)
        );

        vaciarCarrito();
        cerrarVentanaCarrito();

    } catch (error) {
        console.error("Error registrando el pedido:", error);
        alert("No se pudo conectar con el servidor. Inténtalo de nuevo.");
    }
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
