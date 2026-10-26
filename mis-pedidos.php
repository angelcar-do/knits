<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/config/sesion.php';

if (!isset($_SESSION['usuario']) ||
    !is_array($_SESSION['usuario']) ||
    empty($_SESSION['usuario']['id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis pedidos | Custom Knits</title>
    <link rel="stylesheet" href="frontend/css/estil.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .historial {
            max-width: 1000px;
            margin: 60px auto;
            padding: 30px 20px;
            min-height: 55vh;
        }
        .historial h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        .historial-intro {
            text-align: center;
            margin-bottom: 30px;
        }
        .pedido {
            background: #fff;
            border: 1px solid #e8dfd7;
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }
        .pedido-cabecera {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
            border-bottom: 1px solid #eee;
            padding-bottom: 14px;
            margin-bottom: 14px;
        }
        .pedido-producto {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 9px 0;
        }
        .pedido-total {
            text-align: right;
            font-size: 19px;
            font-weight: bold;
            margin-top: 15px;
        }
        .pedido-estado {
            display: inline-block;
            background: #f4eadc;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
        }
        .volver-tienda {
            display: block;
            width: fit-content;
            margin: 25px auto;
            padding: 12px 22px;
            background: #76523b;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        #mensajeHistorial {
            text-align: center;
            padding: 25px 10px;
        }
        @media(max-width: 600px) {
            .historial { margin: 25px auto; padding: 20px 12px; }
            .pedido { padding: 16px; }
        }
    </style>
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
    <a href="index.php">Volver a la tienda</a>
</header>

<main class="historial">
    <h2><i class="fa-solid fa-box-open"></i> Mis pedidos</h2>
    <p class="historial-intro">
        Aquí puedes consultar tus compras y el estado de cada pedido.
    </p>

    <div id="mensajeHistorial">Cargando tus pedidos...</div>
    <div id="listaPedidos"></div>

    <a class="volver-tienda" href="index.php">
        <i class="fa-solid fa-store"></i> Seguir comprando
    </a>
</main>

<script>
const mensaje = document.getElementById('mensajeHistorial');
const lista = document.getElementById('listaPedidos');

const formatoPrecio = valor =>
    new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(Number(valor) || 0);

function crearElemento(etiqueta, texto, clase) {
    const elemento = document.createElement(etiqueta);
    if (clase) elemento.className = clase;
    elemento.textContent = texto;
    return elemento;
}

async function cargarPedidos() {
    try {
        const respuesta = await fetch('backend/pedidos/listar.php', {
            headers: { 'Accept': 'application/json' }
        });

        const datos = await respuesta.json();

        if (!respuesta.ok || !datos.ok) {
            throw new Error(datos.mensaje || 'No se pudieron cargar los pedidos.');
        }

        mensaje.textContent = '';

        if (!datos.pedidos || datos.pedidos.length === 0) {
            mensaje.textContent = 'Todavía no tienes pedidos registrados.';
            return;
        }

        datos.pedidos.forEach(pedido => {
            const tarjeta = document.createElement('article');
            tarjeta.className = 'pedido';

            const cabecera = document.createElement('div');
            cabecera.className = 'pedido-cabecera';

            const fecha = pedido.fecha
                ? new Date(pedido.fecha.replace(' ', 'T')).toLocaleString('es-CO')
                : 'Fecha no disponible';

            cabecera.appendChild(crearElemento('strong', 'Pedido #' + pedido.id));
            cabecera.appendChild(crearElemento('span', fecha));
            tarjeta.appendChild(cabecera);

            tarjeta.appendChild(
                crearElemento('span', 'Estado: ' + pedido.estado, 'pedido-estado')
            );

            (pedido.productos || []).forEach(producto => {
                const fila = document.createElement('div');
                fila.className = 'pedido-producto';

                const nombre = (producto.nombre || 'Producto')
                    + ' × ' + (Number(producto.cantidad) || 0);

                fila.appendChild(crearElemento('span', nombre));
                fila.appendChild(crearElemento(
                    'span',
                    formatoPrecio(producto.subtotal)
                ));

                tarjeta.appendChild(fila);
            });

            tarjeta.appendChild(crearElemento(
                'div',
                'Total: ' + formatoPrecio(pedido.total),
                'pedido-total'
            ));

            lista.appendChild(tarjeta);
        });
    } catch (error) {
        console.error('Error cargando historial:', error);
        mensaje.textContent =
            'No pudimos cargar tus pedidos. Comprueba tu conexión e inténtalo de nuevo.';
    }
}

cargarPedidos();
</script>

</body>
</html>
