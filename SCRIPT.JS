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

// Carrusel automático de la imagen principal
document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelectorAll(
        ".hero-imagen .hero-slide"
    );

    if (slides.length === 0) return;

    let actual = 0;
    let temporizador;

    function mostrarSiguiente() {
        const anterior = slides[actual];

        anterior.classList.remove("activo");

        if (anterior.tagName === "VIDEO") {
            anterior.pause();
            anterior.currentTime = 0;
        }

        actual = (actual + 1) % slides.length;

        const siguiente = slides[actual];
        siguiente.classList.add("activo");

        if (siguiente.tagName === "VIDEO") {
            siguiente.currentTime = 0;

            const reproduccion = siguiente.play();

            if (reproduccion) {
                reproduccion.catch(() => {});
            }
        }

        programarSiguiente();
    }

    function programarSiguiente() {
        clearTimeout(temporizador);

        const slide = slides[actual];

        if (slide.tagName === "VIDEO") {
            if (slide.readyState >= 1) {
                temporizador = setTimeout(
                    mostrarSiguiente,
                    Math.max(1000, slide.duration * 1000)
                );
            } else {
                temporizador = setTimeout(
                    mostrarSiguiente,
                    10000
                );
            }
        } else {
            temporizador = setTimeout(
                mostrarSiguiente,
                5000
            );
        }
    }

    slides.forEach((slide, indice) => {
        if (indice !== 0) {
            slide.classList.remove("activo");
        }
    });

    programarSiguiente();
});