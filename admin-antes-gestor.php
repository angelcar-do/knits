<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/config/admin.php';
require_once __DIR__ . '/backend/config/csrf.php';
$csrfToken = obtenerTokenCsrf();

$usuario = exigirAdministrador();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Custom Knits</title>
    <style>
        :root {
            --fondo: #faf5f1;
            --panel: #ffffff;
            --texto: #493631;
            --principal: #815b50;
            --borde: #eadbd6;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px;
            background: var(--fondo);
            color: var(--texto);
            font-family: Arial, sans-serif;
        }

        main { max-width: 1200px; margin: auto; }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        h1 { margin: 0 0 8px; }
        .subtitulo { margin: 0; color: #79665f; }

        .acciones { display: flex; gap: 10px; flex-wrap: wrap; }

        button, .boton {
            border: 0;
            border-radius: 8px;
            padding: 11px 15px;
            background: var(--principal);
            color: white;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .secundario { background: #e9d8d0; color: var(--texto); }

        .resumen {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .tarjeta, .tabla-contenedor {
            background: var(--panel);
            border: 1px solid var(--borde);
            border-radius: 12px;
            padding: 18px;
        }

        .tarjeta span { display: block; color: #79665f; margin-bottom: 8px; }
        .tarjeta strong { font-size: 25px; }

        .tabla-contenedor { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; min-width: 850px; }
        th, td { text-align: left; padding: 13px 10px; border-bottom: 1px solid var(--borde); }
        th { background: #f7eee9; }

        select {
            max-width: 180px;
            padding: 9px;
            border: 1px solid var(--borde);
            border-radius: 7px;
            color: var(--texto);
            background: white;
        }

        .estado-form { display: flex; gap: 8px; align-items: center; }
        .mensaje { margin: 14px 0; }
        .error { color: #a32222; }
        .correcto { color: #216e39; }
        .sin-pedidos { text-align: center; padding: 30px; }

        @media (max-width: 600px) {
            body { padding: 14px; }
            header { align-items: flex-start; }
        }
    </style>
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<main>
    <header>
        <div>
            <h1>Panel de administración</h1>
            <p class="subtitulo">Custom Knits · Gestión de pedidos</p>
        </div>
        <div class="acciones">
            <a class="boton secundario" href="index.php">Volver a la tienda</a>
            <button type="button" id="recargar">Actualizar</button>
        </div>
    </header>

    <section class="resumen">
        <article class="tarjeta">
            <span>Total de pedidos</span>
            <strong id="totalPedidos">—</strong>
        </article>
        <article class="tarjeta">
            <span>Pendientes</span>
            <strong id="pendientes">—</strong>
        </article>
        <article class="tarjeta">
            <span>En preparación</span>
            <strong id="preparacion">—</strong>
        </article>
        <article class="tarjeta">
            <span>Entregados</span>
            <strong id="entregados">—</strong>
        </article>
    </section>

    <p id="mensaje" class="mensaje" role="status" aria-live="polite"></p>

    <section class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Usuario</th>
                    <th>Productos</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody id="listaPedidos">
                <tr><td colspan="6">Cargando pedidos...</td></tr>
            </tbody>
        </table>
    </section>
</main>

<script>
"use strict";

const lista = document.getElementById("listaPedidos");
const mensaje = document.getElementById("mensaje");

const estados = [
    ["pendiente", "Pendiente"],
    ["en_preparacion", "En preparación"],
    ["enviado", "Enviado"],
    ["entregado", "Entregado"],
    ["cancelado", "Cancelado"]
];

function crearElemento(etiqueta, texto) {
    const elemento = document.createElement(etiqueta);
    elemento.textContent = texto;
    return elemento;
}

function formatoPrecio(valor, moneda = "COP") {
    return new Intl.NumberFormat("es-CO", {
        style: "currency",
        currency: moneda
    }).format(Number(valor) || 0);
}

function fechaLegible(fecha) {
    if (!fecha) return "Sin fecha";
    const fechaValida = new Date(fecha.replace(" ", "T") + "Z");
    return Number.isNaN(fechaValida.getTime())
        ? fecha
        : fechaValida.toLocaleString("es-CO");
}

async function cargarPedidos() {
    mensaje.textContent = "Cargando pedidos...";
    mensaje.className = "mensaje";
    lista.replaceChildren(crearElemento("tr", ""));
    try {
        const respuesta = await fetch("backend/pedidos/admin_listar.php", {
            credentials: "same-origin",
            headers: { "Accept": "application/json" }
        });
        const datos = await respuesta.json();

        if (!respuesta.ok || !datos.ok) {
            throw new Error(datos.mensaje || "No se pudieron cargar los pedidos.");
        }

        const pedidos = datos.pedidos || [];
        document.getElementById("totalPedidos").textContent = pedidos.length;
        document.getElementById("pendientes").textContent =
            pedidos.filter(p => p.estado === "pendiente").length;
        document.getElementById("preparacion").textContent =
            pedidos.filter(p => p.estado === "en_preparacion").length;
        document.getElementById("entregados").textContent =
            pedidos.filter(p => p.estado === "entregado").length;

        lista.replaceChildren();

        if (!pedidos.length) {
            const fila = document.createElement("tr");
            const celda = crearElemento("td", "Todavía no hay pedidos.");
            celda.colSpan = 6;
            celda.className = "sin-pedidos";
            fila.appendChild(celda);
            lista.appendChild(fila);
        }

        pedidos.forEach(pedido => {
            const fila = document.createElement("tr");
            const productos = (pedido.productos || []).map(producto =>
                `${producto.nombre || producto.codigo || "Producto"} × ${producto.cantidad || 1}`
            ).join(", ");

            [
                String(pedido.id || "").slice(-8).toUpperCase(),
                pedido.usuario_id || "Sin usuario",
                productos || "Sin productos",
                formatoPrecio(pedido.total, pedido.moneda || "COP"),
                fechaLegible(pedido.creado_en)
            ].forEach(texto => fila.appendChild(crearElemento("td", texto)));

            const celdaEstado = document.createElement("td");
            const formulario = document.createElement("form");
            formulario.className = "estado-form";

            const selector = document.createElement("select");
            selector.setAttribute("aria-label", "Estado del pedido");

            estados.forEach(([valor, etiqueta]) => {
                const opcion = document.createElement("option");
                opcion.value = valor;
                opcion.textContent = etiqueta;
                opcion.selected = valor === pedido.estado;
                selector.appendChild(opcion);
            });

            const boton = crearElemento("button", "Guardar");
            boton.type = "submit";
            formulario.append(selector, boton);

            formulario.addEventListener("submit", async evento => {
                evento.preventDefault();
                boton.disabled = true;
                mensaje.className = "mensaje";
                mensaje.textContent = "Actualizando pedido...";

                try {
                   const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

if (!csrfToken) {
    throw new Error("No se pudo validar la seguridad. Recarga la página.");
}

const respuesta = await fetch("backend/pedidos/admin_estado.php", {
    method: "POST",
    credentials: "same-origin",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": csrfToken
    },
    body: JSON.stringify({
        pedido_id: pedido.id,
        estado: selector.value
    })
});

                    const resultado = await respuesta.json();

                    if (!respuesta.ok || !resultado.ok) {
                        throw new Error(resultado.mensaje || "No se pudo actualizar el estado.");
                    }

                    mensaje.className = "mensaje correcto";
                    mensaje.textContent = "Estado actualizado correctamente.";
                    await cargarPedidos();
                } catch (error) {
                    mensaje.className = "mensaje error";
                    mensaje.textContent = error.message;
                } finally {
                    boton.disabled = false;
                }
            });

            celdaEstado.appendChild(formulario);
            fila.appendChild(celdaEstado);
            lista.appendChild(fila);
        });

        if (!mensaje.classList.contains("correcto")) {
            mensaje.textContent = pedidos.length
                ? "Pedidos cargados correctamente."
                : "No hay pedidos registrados.";
        }
    } catch (error) {
        lista.replaceChildren();
        const fila = document.createElement("tr");
        const celda = crearElemento("td", error.message);
        celda.colSpan = 6;
        fila.appendChild(celda);
        lista.appendChild(fila);
        mensaje.className = "mensaje error";
        mensaje.textContent = error.message;
    }
}

document.getElementById("recargar").addEventListener("click", cargarPedidos);
cargarPedidos();
</script>
</body>
</html>
