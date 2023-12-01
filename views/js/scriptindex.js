function cargarContenido(seccion) {
    fetch(seccion + ".php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("contenido").innerHTML = data;
        });
}

// Carga la página de inicio al cargar la página principal
window.onload = function () {
    cargarContenido('home');
};

