let searchInput;
// Creamos un temporizador
let typingTimer;
const typingDelay = 500; // Tiempo de espera (en milisegundos)

let select;
let tbody;
let search;
document.addEventListener('DOMContentLoaded', async () => {
    select = document.getElementById('pages');
    tbody = document.querySelector('table tbody');
    searchInput = document.getElementById('search-input');
    const limit = 10;
    const page = 1;
});

function datatable(data, tabla) {
    tbody.innerHTML = "";
    select.innerHTML = "";
    if (tabla == "equipos"){
        data.results.forEach((item) => {
            const row = `
            <tr>
                <td>${item.placa}</td>
                <td>${item.modelo}</td>
                <td>${item.codigo}</td>
            </tr>
        `;
            tbody.innerHTML += row;
        });
    }
    if (tabla == "clientes"){
        data.results.forEach((item) => {
            const row = `
            <tr>
                <td>${item.codigo}</td>
                <td>${item.nombre}</td> 
                <td>${item.municipio}</td>
                <td>${item.barrio}</td>
                <td>${item.ruta}</td>
            </tr>
        `;
            tbody.innerHTML += row;
        });
    }
    if (tabla == "usuarios"){
        data.results.forEach((item) => {
            const row = `
            <tr>
                <td>${item.id}</td>
                <td>${item.username}</td>
                <td>${item.email}</td>  
            </tr>
        `;
            tbody.innerHTML += row;
        });
    }
    console.log(data)
    for (var i = 0; i < data.totalPages; i++) {
        const option = `<option value="${i + 1}">${i + 1}</option>`
        select.innerHTML += option;
    }
    select.value = data.currentPage;
    const length = select.options.length;
    const buttonNext = document.getElementById('next');
    const buttonBack = document.getElementById('back');
    console.log(length)
    buttonNext.disabled = false;
    buttonBack.disabled = false;
    if (data.currentPage >= length) buttonNext.disabled = true;
    if (data.currentPage <= 1) buttonBack.disabled = true; 
};

function busqueda(tabla) {
    clearTimeout(typingTimer); // Reiniciar el temporizador si el usuario sigue escribiendo
    console.log("funcionbusqueda")
    typingTimer = setTimeout(() => {
        search = searchInput.value.trim(); // Obtener el valor del input
        llamarTabla(tabla,1); // Llamar a la función de búsqueda
    }, typingDelay);
};

function llamarTabla(tabla, page) {
    let query = ''
    if ((page == isNaN) || (page == undefined)) page = 1;

    if (search) {
        query = `http://localhost:3000/api/${tabla}.php?q=${search}&limit=10&page=${page}`
    }
    else {
        query = `http://localhost:3000/api/${tabla}.php?limit=10&page=${page}`
    }
    fetch(query)
        .then(response => response.json())
        .then(data => {
            datatable(data, tabla)
        })
        .catch(error => console.error('Error al cargar los datos:', error));
}

const button_next = (tabla) => {
    select = document.getElementById('pages');
    const page = parseInt(select.value) + 1;
    llamarTabla(tabla,page)
    select.value = page;

};

const button_back = (tabla) => {
    select = document.getElementById('pages');
    const page = parseInt(select.value) - 1;
    llamarTabla(tabla, page)
    select.value = page;
};
