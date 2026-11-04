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

        .tabs-admin{display:flex;gap:10px;flex-wrap:wrap;margin:0 0 18px}
        .tabs-admin button[aria-pressed="true"]{background:#493631}
        .panel-admin[hidden]{display:none!important}
        .productos-toolbar{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;margin-bottom:16px}
        .productos-toolbar input{flex:1;min-width:220px;padding:12px;border:1px solid var(--borde);border-radius:8px}
        .productos-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:16px}
        .producto-admin{background:#fff;border:1px solid var(--borde);border-radius:12px;overflow:hidden;display:flex;flex-direction:column}
        .producto-admin img{width:100%;height:190px;object-fit:cover;background:#f5eeea}
        .producto-admin-contenido{padding:14px;display:grid;gap:8px}
        .producto-admin-acciones{display:flex;gap:8px;flex-wrap:wrap}
        .producto-admin-acciones button{padding:9px 11px;font-size:13px}
        .btn-peligro{background:#a63c36}
        .estado-producto{font-size:12px;color:#216e39;font-weight:bold}
        .estado-producto.inactivo{color:#98621b}
        .modal-producto{position:fixed;inset:0;z-index:1000;background:#20171399;display:flex;align-items:center;justify-content:center;padding:16px}
        .modal-producto[hidden]{display:none}
        .modal-producto-contenido{background:#fffaf7;width:min(680px,100%);max-height:92vh;overflow:auto;border-radius:16px;padding:22px}
        .form-producto{display:grid;grid-template-columns:1fr 1fr;gap:13px}
        .form-producto label{display:grid;gap:6px;font-weight:bold;font-size:14px}
        .form-producto input,.form-producto textarea,.form-producto select{width:100%;padding:11px;border:1px solid var(--borde);border-radius:8px;font:inherit;background:white;color:var(--texto)}
        .form-producto .ancho{grid-column:1/-1}
        .preview-producto{width:100%;max-height:230px;object-fit:contain;background:#f5eeea;border-radius:10px}
        .ayuda{font-size:12px;color:#79665f;font-weight:normal}
        .modal-acciones{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;margin-top:18px}
        @media(max-width:600px){.form-producto{grid-template-columns:1fr}.form-producto .ancho{grid-column:auto}.modal-producto-contenido{padding:16px}}
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


    <nav class="tabs-admin" aria-label="Secciones de administración">
        <button type="button" id="tabPedidos" aria-pressed="true">Pedidos</button>
        <button type="button" id="tabProductos" class="secundario" aria-pressed="false">Productos</button>
    </nav>

    <div id="panelPedidos" class="panel-admin">
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
    </div>

    <section id="panelProductos" class="panel-admin" hidden>
        <div class="productos-toolbar">
            <input type="search" id="buscarProducto" placeholder="Buscar por nombre o categoría…" aria-label="Buscar productos">
            <button type="button" id="nuevoProducto"><span aria-hidden="true">＋</span> Agregar producto</button>
        </div>
        <p id="mensajeProductos" class="mensaje" role="status" aria-live="polite">Cargando productos…</p>
        <div id="listaProductosAdmin" class="productos-grid"></div>
    </section>

    <div id="modalProducto" class="modal-producto" hidden>
      <div class="modal-producto-contenido" role="dialog" aria-modal="true" aria-labelledby="tituloModalProducto">
        <header><div><h2 id="tituloModalProducto">Agregar producto</h2><p class="subtitulo">Completa los datos y publica cuando esté listo.</p></div><button type="button" id="cerrarModalProducto" class="secundario" aria-label="Cerrar">✕</button></header>
        <form id="formProducto" enctype="multipart/form-data">
          <input type="hidden" name="id" id="productoId">
          <div class="form-producto">
            <label>Nombre del producto<input name="nombre" id="productoNombre" maxlength="100" required placeholder="Ej. Camiseta oversize"></label>
            <label>Precio (COP)<input name="precio" id="productoPrecio" type="number" min="0" step="1" required placeholder="65000"></label>
            <label>Categoría<input name="categoria" id="productoCategoria" maxlength="60" required placeholder="Camisetas"></label>
            <label>Disponibilidad<select name="activo" id="productoActivo"><option value="1">Publicado</option><option value="0">Oculto</option></select></label>
            <label class="ancho">Descripción<textarea name="descripcion" id="productoDescripcion" rows="3" maxlength="1000" placeholder="Describe el producto…"></textarea></label>
            <label class="ancho">Fotografía principal <span class="ayuda">JPG, PNG o WEBP. Máximo 5 MB.</span><input type="file" name="imagen" id="productoImagen" accept="image/jpeg,image/png,image/webp"><img id="previewProducto" class="preview-producto" alt="Vista previa de la fotografía" hidden></label>
          </div>
          <div class="modal-acciones"><button type="button" id="cancelarProducto" class="secundario">Cancelar</button><button type="submit" id="guardarProducto">Guardar producto</button></div>
        </form>
      </div>
    </div>
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


// Gestor visual de productos: usa los endpoints PHP protegidos del administrador.
const csrfAdmin = document.querySelector('meta[name="csrf-token"]')?.content || '';
const panelPedidos = document.getElementById('panelPedidos');
const panelProductos = document.getElementById('panelProductos');
const tabPedidos = document.getElementById('tabPedidos');
const tabProductos = document.getElementById('tabProductos');
const modalProducto = document.getElementById('modalProducto');
const formProducto = document.getElementById('formProducto');
const listaProductosAdmin = document.getElementById('listaProductosAdmin');
const mensajeProductos = document.getElementById('mensajeProductos');
let productosAdmin = [];
let urlPreviewProducto = null;

function mostrarPanelAdmin(panel) {
    const verProductos = panel === 'productos';
    panelPedidos.hidden = verProductos;
    panelProductos.hidden = !verProductos;
    tabPedidos.setAttribute('aria-pressed', String(!verProductos));
    tabProductos.setAttribute('aria-pressed', String(verProductos));
    tabPedidos.classList.toggle('secundario', verProductos);
    tabProductos.classList.toggle('secundario', !verProductos);
    if (verProductos) cargarProductosAdmin();
}
tabPedidos.addEventListener('click', () => mostrarPanelAdmin('pedidos'));
tabProductos.addEventListener('click', () => mostrarPanelAdmin('productos'));

function textoSeguro(tag, text, className) {
    const el = document.createElement(tag);
    el.textContent = text == null ? '' : String(text);
    if (className) el.className = className;
    return el;
}
function imagenProducto(src) {
    const img = document.createElement('img');
    img.alt = 'Fotografía del producto';
    img.loading = 'lazy';
    img.src = src || '';
    img.onerror = () => { img.removeAttribute('src'); img.alt = 'Sin fotografía'; };
    return img;
}
function renderProductosAdmin() {
    const filtro = document.getElementById('buscarProducto').value.trim().toLocaleLowerCase('es');
    listaProductosAdmin.replaceChildren();
    const filtrados = productosAdmin.filter(p => `${p.nombre} ${p.categoria}`.toLocaleLowerCase('es').includes(filtro));
    if (!filtrados.length) {
        listaProductosAdmin.appendChild(textoSeguro('p', 'No hay productos que coincidan. Agrega uno nuevo para comenzar.'));
        return;
    }
    filtrados.forEach(producto => {
        const card = document.createElement('article'); card.className = 'producto-admin';
        card.appendChild(imagenProducto(producto.imagen));
        const contenido = document.createElement('div'); contenido.className = 'producto-admin-contenido';
        contenido.appendChild(textoSeguro('strong', producto.nombre));
        contenido.appendChild(textoSeguro('span', formatoPrecio(producto.precio)));
        contenido.appendChild(textoSeguro('small', producto.categoria || 'Sin categoría'));
        contenido.appendChild(textoSeguro('span', producto.activo ? 'Publicado' : 'Oculto', 'estado-producto' + (producto.activo ? '' : ' inactivo')));
        const acciones = document.createElement('div'); acciones.className = 'producto-admin-acciones';
        const editar = textoSeguro('button', 'Editar'); editar.type = 'button'; editar.addEventListener('click', () => abrirEditorProducto(producto));
        const visibilidad = textoSeguro('button', producto.activo ? 'Ocultar' : 'Publicar'); visibilidad.type = 'button'; visibilidad.className = 'secundario'; visibilidad.addEventListener('click', () => cambiarVisibilidadProducto(producto));
        const eliminar = textoSeguro('button', 'Eliminar'); eliminar.type = 'button'; eliminar.className = 'btn-peligro'; eliminar.addEventListener('click', () => eliminarProductoAdmin(producto));
        acciones.append(editar, visibilidad, eliminar); contenido.appendChild(acciones); card.appendChild(contenido); listaProductosAdmin.appendChild(card);
    });
}
async function cargarProductosAdmin() {
    mensajeProductos.textContent = 'Cargando productos…';
    try {
        const response = await fetch('backend/productos/admin_listar.php', {credentials:'same-origin', headers:{Accept:'application/json'}});
        const data = await response.json();
        if (!response.ok || !data.ok) throw new Error(data.mensaje || 'No se pudieron cargar los productos.');
        productosAdmin = data.productos || []; renderProductosAdmin();
        mensajeProductos.textContent = `${productosAdmin.length} producto(s) en el catálogo.`;
    } catch (error) { mensajeProductos.textContent = error.message; mensajeProductos.className = 'mensaje error'; }
}
document.getElementById('buscarProducto').addEventListener('input', renderProductosAdmin);
function limpiarFormularioProducto() {
    formProducto.reset(); document.getElementById('productoId').value = '';
    document.getElementById('previewProducto').hidden = true;
    document.getElementById('previewProducto').removeAttribute('src');
    document.getElementById('tituloModalProducto').textContent = 'Agregar producto';
}
function abrirEditorProducto(producto = null) {
    limpiarFormularioProducto();
    if (producto) {
        document.getElementById('tituloModalProducto').textContent = 'Editar producto';
        document.getElementById('productoId').value = producto.id;
        document.getElementById('productoNombre').value = producto.nombre || '';
        document.getElementById('productoPrecio').value = producto.precio ?? '';
        document.getElementById('productoCategoria').value = producto.categoria || '';
        document.getElementById('productoDescripcion').value = producto.descripcion || '';
        document.getElementById('productoActivo').value = producto.activo ? '1' : '0';
        if (producto.imagen) { const img = document.getElementById('previewProducto'); img.src = producto.imagen; img.hidden = false; }
    }
    modalProducto.hidden = false; document.getElementById('productoNombre').focus();
}
document.getElementById('nuevoProducto').addEventListener('click', () => abrirEditorProducto());
function cerrarEditorProducto() { modalProducto.hidden = true; if (urlPreviewProducto) URL.revokeObjectURL(urlPreviewProducto); urlPreviewProducto = null; }
document.getElementById('cerrarModalProducto').addEventListener('click', cerrarEditorProducto);
document.getElementById('cancelarProducto').addEventListener('click', cerrarEditorProducto);
modalProducto.addEventListener('click', e => { if (e.target === modalProducto) cerrarEditorProducto(); });
document.getElementById('productoImagen').addEventListener('change', e => {
    const file = e.target.files?.[0]; const preview = document.getElementById('previewProducto');
    if (!file) return;
    if (urlPreviewProducto) URL.revokeObjectURL(urlPreviewProducto);
    urlPreviewProducto = URL.createObjectURL(file); preview.src = urlPreviewProducto; preview.hidden = false;
});
formProducto.addEventListener('submit', async e => {
    e.preventDefault(); const boton = document.getElementById('guardarProducto'); boton.disabled = true; boton.textContent = 'Guardando…';
    try {
        if (!csrfAdmin) throw new Error('No se encontró el token de seguridad. Recarga la página.');
        const fd = new FormData(formProducto);
        const response = await fetch('backend/productos/guardar.php', {method:'POST', credentials:'same-origin', headers:{'X-CSRF-Token':csrfAdmin, Accept:'application/json'}, body:fd});
        const data = await response.json(); if (!response.ok || !data.ok) throw new Error(data.mensaje || 'No se pudo guardar el producto.');
        cerrarEditorProducto(); await cargarProductosAdmin(); mensajeProductos.className = 'mensaje correcto'; mensajeProductos.textContent = data.mensaje || 'Producto guardado correctamente.';
    } catch (error) { alert(error.message); }
    finally { boton.disabled = false; boton.textContent = 'Guardar producto'; }
});
async function cambiarVisibilidadProducto(producto) {
    const fd = new FormData(); fd.append('id', producto.id); fd.append('accion', producto.activo ? 'ocultar' : 'publicar');
    await enviarAccionProducto(fd, 'No se pudo cambiar la visibilidad.');
}
async function eliminarProductoAdmin(producto) {
    if (!confirm(`¿Quieres retirar «${producto.nombre}» del catálogo? Esta acción lo ocultará sin borrar sus pedidos anteriores.`)) return;
    const fd = new FormData(); fd.append('id', producto.id); fd.append('accion', 'eliminar');
    await enviarAccionProducto(fd, 'No se pudo retirar el producto.');
}
async function enviarAccionProducto(fd, fallback) {
    try {
        const response = await fetch('backend/productos/accion.php', {method:'POST', credentials:'same-origin', headers:{'X-CSRF-Token':csrfAdmin, Accept:'application/json'}, body:fd});
        const data = await response.json(); if (!response.ok || !data.ok) throw new Error(data.mensaje || fallback);
        await cargarProductosAdmin(); mensajeProductos.className = 'mensaje correcto'; mensajeProductos.textContent = data.mensaje;
    } catch (error) { alert(error.message); }
}

document.getElementById("recargar").addEventListener("click", cargarPedidos);
cargarPedidos();
</script>
</body>
</html>
