let cantidadCarrito = 0;

function agregarCarrito(producto) {

    cantidadCarrito++;

    document.getElementById("contador").textContent = cantidadCarrito;

    alert(producto + " agregado al carrito");
}


function verProductos() {

    document
        .getElementById("productos")
        .scrollIntoView({
            behavior: "smooth"
        });
}


function solicitarPersonalizado() {

    alert("Próximamente podrás realizar tu pedido personalizado.");
}