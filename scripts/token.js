const authToken = async () => {
    const token = localStorage.getItem('authToken');
    if (!token) {
        // Si no hay token, redirige al login
        window.location.href = '/login.html';
        return;
    }

    // Verificar el token con el servidor
    try {
        const response = await fetch('/api/validate_token.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}` // Envía el token en el encabezado
            }
        });

        const result = await response.json();

        if (!result.success) {
            // Token inválido o expirado
            localStorage.removeItem('authToken'); // Elimina el token almacenado
            window.location.href = '/login.html'; // Redirige al login
        }
        console.log('Usuario autenticado:', result.user.username);
        // Si el token es válido, muestra el contenido del dashboard

    } catch (error) {
        console.error('Error al verificar el token:', error);
        localStorage.removeItem('authToken');
        window.location.href = '/login.html';
    }
}

export default authToken