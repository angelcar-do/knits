const formulario = document.getElementById("formLogin");
const mensaje = document.getElementById("mensaje");
const boton = document.getElementById("btnLogin");

formulario.addEventListener("submit", async (evento) => {
    evento.preventDefault();

    const correo = document.getElementById("correo").value.trim();
    const contrasena = document.getElementById("contrasena").value;

    mensaje.textContent = "";
    mensaje.className = "";
    boton.disabled = true;
    boton.textContent = "Verificando...";

    try {
        const respuesta = await fetch("backend/usuarios/login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({ correo, contrasena })
        });

        const datos = await respuesta.json();

        if (!respuesta.ok || !datos.ok) {
            throw new Error(datos.mensaje || "No se pudo iniciar sesión.");
        }

        mensaje.textContent = "¡Bienvenido a Custom Knits!";
        mensaje.className = "exito";

        setTimeout(() => {
            window.location.href = "INDEX.HTML";
        }, 1000);

    } catch (error) {
        mensaje.textContent = error.message === "Failed to fetch"
            ? "No se pudo conectar con el servidor PHP."
            : error.message;
        mensaje.className = "error";
    } finally {
        boton.disabled = false;
        boton.textContent = "Iniciar sesión";
    }
});