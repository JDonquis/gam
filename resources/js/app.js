// import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Toastr from 'toastr';
import 'toastr/build/toastr.min.css';


const toastr = Toastr; // Crea una referencia constante
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: true,
};

// Asignación global REQUERIDA (usa window.toastr)
window.toastr = toastr;



// Inicializa Alpine.js + plugins
Alpine.plugin(collapse);
Alpine.start();

// Opcional: Hacer Alpine accesible globalmente (para debugging)
window.Alpine = Alpine;
