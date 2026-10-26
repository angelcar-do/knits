
document.addEventListener("DOMContentLoaded", () => {
    const formulario = document.getElementById("formLogin");
    const mensaje = document.getElementById("mensaje");
    const boton = document.getElementById("btnLogin");
    const campoCorreo = document.getElementById("correo");
    const campoContrasena = document.getElementById("contrasena");

    if (!formulario || !mensaje || !boton ||
        !campoCorreo || !campoContrasena) {
        console.error("No se encontraron los elementos del formulario de inicio de sesión.");
        return;
    }

    formulario.addEventListener("submit", async (evento) => {
        evento.preventDefault();

        const correo = campoCorreo.value.trim().toLowerCase();
        const contrasena = campoContrasena.value;

        mensaje.textContent = "";
        mensaje.className = "";

        if (!correo || !contrasena) {
            mensaje.textContent = "Completa todos los campos.";
            mensaje.className = "error";
            return;
        }

        boton.disabled = true;
        boton.textContent = "Verificando...";

        try {
            const respuesta = await fetch(
                "backend/usuarios/login.php",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        correo: correo,
                        contrasena: contrasena
                    })
                }
            );

            let datos;

            try {
                datos = await respuesta.json();
            } catch {
                throw new Error(
                    "El servidor devolvió una respuesta inválida. Verifica el backend."
                );
            }

            if (!respuesta.ok || !datos.ok) {
                throw new Error(
                    datos.mensaje || "No se pudo iniciar sesión."
                );
            }

            mensaje.textContent = "¡Bienvenido a Custom Knits!";
            mensaje.className = "exito";

            boton.textContent = "Sesión iniciada";

            
            window.setTimeout(() => {
            const parametros = new URLSearchParams(window.location.search);
            const destino = parametros.get("redirect");

           if (destino === "finalizar-compra.php") {
          window.location.href = "finalizar-compra.php";
        } else {
           window.location.href = "index.php";
    }
}, 1000);


        } catch (error) {
            console.error("Error al iniciar sesión:", error);

            mensaje.textContent =
                error.message === "Failed to fetch"
                    ? "No se pudo conectar con el servidor. Abre la página desde PHP."
                    : error.message;

            mensaje.className = "error";

            boton.disabled = false;
            boton.textContent = "Iniciar sesión";
        }
    });
});