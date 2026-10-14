
"use strict";

document.addEventListener("DOMContentLoaded", () => {
    const formulario = document.getElementById("formRegistro");
    const mensaje = document.getElementById("mensaje");
    const boton = document.getElementById("btnRegistro");

    if (!formulario || !mensaje || !boton) {
        console.error("No se encontraron los elementos del formulario de registro.");
        return;
    }

    const mostrarMensaje = (texto, tipo) => {
        mensaje.textContent = texto;
        mensaje.className = tipo;
    };

    formulario.addEventListener("submit", async (evento) => {
        evento.preventDefault();

        if (boton.disabled) return;

        const nombre = document.getElementById("nombre").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const contrasena = document.getElementById("contrasena").value;
        const confirmar = document.getElementById("confirmar").value;

        mostrarMensaje("", "");

        if (!nombre || !correo || !contrasena || !confirmar) {
            mostrarMensaje("Completa todos los campos.", "error");
            return;
        }

        if (nombre.length > 100) {
            mostrarMensaje("El nombre no puede superar los 100 caracteres.", "error");
            return;
        }

        if (correo.length > 190) {
            mostrarMensaje("El correo es demasiado largo.", "error");
            return;
        }

        if (contrasena.length < 8) {
            mostrarMensaje("La contraseña debe tener al menos 8 caracteres.", "error");
            return;
        }

        if (contrasena !== confirmar) {
            mostrarMensaje("Las contraseñas no coinciden.", "error");
            return;
        }

        boton.disabled = true;
        boton.textContent = "Creando cuenta...";

        try {
            const respuesta = await fetch("backend/usuarios/registro.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                credentials: "same-origin",
                body: JSON.stringify({
                    nombre,
                    correo,
                    contrasena
                })
            });

            let datos;

            try {
                datos = await respuesta.json();
            } catch {
                throw new Error(
                    "El servidor no devolvió una respuesta válida. Revisa el archivo PHP."
                );
            }

            if (!respuesta.ok || !datos.ok) {
                throw new Error(
                    datos.mensaje || "No se pudo crear la cuenta."
                );
            }

            mostrarMensaje(
                "¡Cuenta creada correctamente! Redirigiendo al inicio de sesión...",
                "exito"
            );

            formulario.reset();

            setTimeout(() => {
                window.location.href = "login.php";
            }, 1500);

        } catch (error) {
            console.error("Error en el registro:", error);

            mostrarMensaje(
                error.message === "Failed to fetch"
                    ? "No se pudo conectar. Abre la página desde el servidor PHP."
                    : error.message,
                "error"
            );

        } finally {
            boton.disabled = false;
            boton.textContent = "Crear mi cuenta";
        }
    });
});