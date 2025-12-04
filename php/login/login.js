document.getElementById("loginForm").addEventListener('submit', async function (e) {
    e.preventDefault();

    const usuario = document.getElementById("usuario").value.trim();
    const contrasenna = document.getElementById("contrasenna").value.trim();

    if (usuario.length == 0) {
        Swal.fire({ icon: 'error', title: 'Datos faltantes', text: 'Debe ingresar un usuario válido.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        return;
    }
    if (contrasenna.length == 0) {
        Swal.fire({ icon: 'error', title: 'Datos faltantes', text: 'Debe ingresar una contraseña válida.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        return;
    }

    try {
        const respuesta = await fetch('php/login/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ usuario, contrasenna })
        });

        const texto = await respuesta.text();
        let data = JSON.parse(texto);

        if (data.status === 'ok') {
            Swal.fire({ icon: 'success', title: 'Éxito', text: 'Inicio de sesión exitoso. Bienvenido: ' + data.nombre, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
            setTimeout(() => { window.location.href = "home.php"; }, 1000);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.mensaje, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        }
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se logró contactar al servidor. Error: ' + error, toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
    }
});
