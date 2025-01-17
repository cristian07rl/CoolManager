const newusers = async () => {


    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (password !== confirmPassword) {
        document.getElementById('message').textContent = 'Las contraseñas no coinciden';
        return;
    }

    const response = await fetch('api/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, email, password })
    });

    const result = await response.json();

    if (result.success) {
        document.getElementById('message').textContent = 'Registro exitoso. Redirigiendo al inicio de sesión...';
        setTimeout(() => {
            window.location.href = '/login.html';
        }, 2000);
    } else {
        document.getElementById('message').textContent = result.message;
    }
};

const nuevoequipo = async () => {


    const placa = document.getElementById('placa').value;
    const modelo = document.getElementById('modelo').value;
    const cliente = document.getElementById('cliente').value;

    const response = await fetch('api/nuevoequipo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ placa, modelo, cliente })
    });

    const result = await response.json();

    if (result.success) {
        document.getElementById('message').textContent = 'Registro exitoso.';
        setTimeout(() => {
            window.location.href = '/dashboard/equipos.html';
        }, 2000);
    } else {
        console.log(result.message)
        document.getElementById('message').textContent = result.message;
    }
};