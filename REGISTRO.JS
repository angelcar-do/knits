const formulario = document.getElementById("formRegistro");
const mensaje = document.getElementById("mensaje");
const boton = document.getElementById("btnRegistro");

formulario.addEventListener("submit", async (evento) => {
    evento.preventDefault();

    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const contrasena = document.getElementById("contrasena").value;
    const confirmar = document.getElementById("confirmar").value;

    mensaje.textContent = "";
    mensaje.className = "";

    if (contrasena.length < 8) {
        mensaje.textContent = "La contraseña debe tener al menos 8 caracteres.";
        mensaje.className = "error";
        return;
    }

    if (contrasena !== confirmar) {
        mensaje.textContent = "Las contraseñas no coinciden.";
        mensaje.className = "error";
        return;
    }

    boton.disabled = true;
    boton.textContent = "Creando cuenta...";

    try {
        const respuesta = await fetch("backend/usuarios/registro.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({ nombre, correo, contrasena })
        });

        const datos = await respuesta.json();

        if (!respuesta.ok || !datos.ok) {
            throw new Error(datos.mensaje || "No se pudo crear la cuenta.");
        }

        mensaje.textContent = "¡Cuenta creada! Ahora puedes iniciar sesión.";
        mensaje.className = "exito";
        formulario.reset();

        setTimeout(() => {
            window.location.href = "LOGIN.HTML";
        }, 1500);

    } catch (error) {
        mensaje.textContent =
            error.message === "Failed to fetch"
                ? "No se pudo conectar. Abre la página desde el servidor PHP."
                : error.message;
        mensaje.className = "error";
    } finally {
        boton.disabled = false;
        boton.textContent = "Crear mi cuenta";
    }
});