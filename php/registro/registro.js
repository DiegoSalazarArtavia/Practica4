document.getElementById("frmRegistro")?.addEventListener("submit", async function (e) {
    e.preventDefault();

    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const usuario = document.getElementById("usuario").value.trim();
    const clave = document.getElementById("clave").value.trim();
    const confirmar = document.getElementById("confirmar").value.trim();
    const fecha = document.getElementById("fecha").value.trim();
    const genero = document.querySelector('input[name="genero"]:checked')?.value;

    const Toast = Swal.mixin({ toast: true, position: "top-end", showConfirmButton: false, timer: 3000 });

    if (!nombre || !correo || !usuario || !clave || !confirmar || !fecha || !genero) {
        Toast.fire({ icon: "warning", title: "Debe completar todos los campos." });
        return;
    }
    if (clave !== confirmar) {
        Toast.fire({ icon: "error", title: "Las contraseñas no coinciden." });
        return;
    }

    const datos = new FormData();
    datos.append("nombre", nombre);
    datos.append("correo", correo);
    datos.append("usuario", usuario);
    datos.append("clave", clave);
    datos.append("confirmar", confirmar);
    datos.append("fecha", fecha);
    datos.append("genero", genero);

    try {
        const response = await fetch("registro.php", { method: "POST", body: datos });
        const result = await response.text();

        if (result.includes("ok")) {
            Toast.fire({ icon: "success", title: "Usuario registrado con éxito." });
            setTimeout(() => {
                window.location.href = "../../index.php"; }, 1500);
        } else if (result.includes("error:")) {
            Toast.fire({ icon: "error", title: result.replace("error:", "").trim() });
        } else {
            Toast.fire({ icon: "error", title: "Ocurrió un error inesperado al registrar al usuario." });
        }
    } catch (error) {
        Toast.fire({ icon: "error", title: "Error de conexión con el servidor." });
    }
});
