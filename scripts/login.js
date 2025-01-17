const login = async () => {

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    // Validación en el frontend
    if (!username || !password) {
        document.getElementById('message').textContent = "Por favor, completa todos los campos.";
        return;
    }

    try {
        const response = await fetch('http://localhost:3000/api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        if (!response.ok) {
            throw new Error('Error al conectar con el servidor');
        }

        console.log(response)
        const result = await response.json();
        if (result.success) {
            // Almacena el token en localStorage o cookies
            console.log(result.token)
            localStorage.setItem('authToken', result.token);
            window.location.href = '/dashboard/index.html';
        } else {
            document.getElementById('message').textContent = result.message;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('message').textContent = "Ocurrió un error inesperado. Inténtalo de nuevo.";
    }
};