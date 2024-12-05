document.addEventListener('DOMContentLoaded', async () => {

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
        const limit = 10;
        const page = 1;
        fetch(`http://localhost:3000/api/equipos.php?limit=${limit}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('table tbody');
                const select = document.getElementById('pages');
                tbody.innerHTML = "";
                data.results.forEach((item) => {
                    const row = `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.placa}</td>
                        <td>${item.modelo}</td>
                        <td>${item.ruta}</td>
                        <td>${item.nombre}</td>
                        <td>${item.codigo}</td>
                        <td>${item.municipio}</td>
                        <td>${item.barrio}</td>
                        <td>${item.fechaIn}</td>
                        <td>${item.novedad}</td>
                    </tr>
                `;
                    tbody.innerHTML += row;

                });
                for (var i = 0; i < data.totalPages; i++) {
                    const option = `<option value="${i + 1}">${i + 1}</option>`
                    select.innerHTML += option;
                }

            })
            .catch(error => console.error('Error al cargar los datos:', error));
    } catch (error) {
        console.error('Error al verificar el token:', error);
        //localStorage.removeItem('authToken');
        //window.location.href = '/login.html';
    }


});

// Seleccionamos el input
const searchInput = document.getElementById('search-input');

// Creamos un temporizador
let typingTimer;
const typingDelay = 500; // Tiempo de espera (en milisegundos)


// Escuchar eventos en el input
function busqueda() {
    clearTimeout(typingTimer); // Reiniciar el temporizador si el usuario sigue escribiendo
    console.log("funcionbusqueda")
    typingTimer = setTimeout(() => {
        const query = searchInput.value.trim(); // Obtener el valor del input
        if (query) {
            llamarTabla(1, query); // Llamar a la función de búsqueda
        }
        else llamarTabla(1)
    }, typingDelay);
}


function llamarTabla(page, searchinput) {
    let query = ''
    if (searchinput) {
        query = `http://localhost:3000/api/equipos.php?q=${searchinput}&limit=10&page=${page}`
    }
    else {
        query = `http://localhost:3000/api/equipos.php?limit=10&page=${page}`
    }
    fetch(query)
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('table tbody');
            tbody.innerHTML = "";
            data.results.forEach(item => {
                const row = `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.placa}</td>
                        <td>${item.modelo}</td>
                        <td>${item.ruta}</td>
                        <td>${item.nombre}</td>
                        <td>${item.codigo}</td>
                        <td>${item.municipio}</td>
                        <td>${item.barrio}</td>
                        <td>${item.fechaIn}</td>
                        <td>${item.novedad}</td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            const elemetpage = document.getElementById('pages');

            console.log(data.currentPage)
            elemetpage.innerHTML = " ";
            for (var i = 0; i < data.totalPages; i++) {
                const option = `<option value="${i + 1}">${i + 1}</option>`
                elemetpage.innerHTML += option;
            }
            elemetpage.value = data.currentPage;
        })
        .catch(error => console.error('Error al cargar los datos:', error));
}

function button_next() {
    const select = document.getElementById('pages');
    const page = parseInt(select.value) + 1;
    llamarTabla(page, searchInput.value)
    select.value = page;

}

function button_back() {
    const select = document.getElementById('pages');
    const page = parseInt(select.value) - 1;
    llamarTabla(page, searchInput.value)
    select.value = page;

}