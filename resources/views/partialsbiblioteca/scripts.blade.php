<!-- resources/views/books/partials/scripts.blade.php -->

<script>
    //  Variables globales (agregadas aquí, al inicio)
    window.isUserLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    window.userRole = "{{ auth()->check() ? auth()->user()->role : '' }}";
</script>

<!-- FUNCIONES JAVASCRIPT GLOBALES -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    let currentOpenRow = null;
    let currentMode = null;

    document.addEventListener('DOMContentLoaded', function () {
        //Limpiar input de la busqueda
        const clearBtn = document.getElementById('clearSearchBtn');
        const searchInput = document.getElementById('searchInput');

        if (clearBtn && searchInput) {
            clearBtn.addEventListener('click', () => {
                window.location.href = "{{ route('home') }}";
            });
        }

        if (document.getElementById('booksTable')) {
            $('#booksTable').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: false,
                info: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron materiales",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Sin registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    search: "Buscar:",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"me-2"l><"ms-auto"f>>tip'
            });
        }
    });

    function formatDetails(book) {
        return `
            <div style="
                padding:20px;
                margin:10px 0;
                background:white;
                border-radius:10px;
                box-shadow:0 4px 12px rgba(0,0,0,0.1);
                display:flex;
                gap:20px;
            ">
                <div style="width:250px; flex-shrink:0;">
                    <div style="display:flex; justify-content:center; margin: 15px 0"><strong>No. Ficha ${book.ficha_no ?? '-'}</strong></div>
                    ${book.portada
                ? `<img src="/storage/portadas/${book.portada}" 
                                style="width:100%; height:170px; object-fit:contain; border-radius:6px;">`
                : `<div style="width:100%; height:170px; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:6px;">
                            <i class="fas fa-book"></i>
                           </div>`
            }
                </div>
                <div style="flex:1; display:flex; flex-direction:column; gap:15px;">
                    <div style="display: flex; justify-content:center"> <h3>Informacion de la Ficha</h3> </div>
                    <div style="display:grid; grid-template-columns: 180px 1fr; row-gap:8px; column-gap:10px; font-size:0.95rem; background: var(--gray-200); padding: 20px; border-radius: 10px">
                        <div><strong>Título:</strong></div> <div>${book.titulo}</div>
                        <div><strong>Autor:</strong></div> <div>${book.autor}</div>
                        <div><strong>Editorial:</strong></div> <div>${book.editorial ?? '-'}</div>
                        <div><strong>ISBN:</strong></div> <div>${book.isbn ?? '-'}</div>
                        <div><strong>Clasificación:</strong></div> <div>${book.clasificacion ?? '-'}</div>
                        <div><strong>ID Biblioteca:</strong></div> <div>${book.idbiblioteca ?? '-'}</div>
                        <div><strong>Adquisición:</strong></div> <div>${book.numadqui ?? '-'}</div>
                        <div><strong>Fecha:</strong></div> <div>${book.fechaingreso ?? '-'}</div>
                    </div>
                </div>
            </div>
        `;
    }

    function toggleDetails(btn) {
        closeMainForm();
        const table = $('#booksTable').DataTable();
        const tr = $(btn).closest('tr');
        const row = table.row(tr);
        const book = JSON.parse(btn.dataset.book);

        if (currentOpenRow === tr[0] && currentMode === 'view') {
            row.child.hide();
            tr.removeClass('shown');
            currentOpenRow = null;
            currentMode = null;
            return;
        }

        closeAllRows();
        row.child(formatDetails(book)).show();
        tr.addClass('shown');
        currentOpenRow = tr[0];
        currentMode = 'view';
    }

    function closeAllRows() {
        const table = $('#booksTable').DataTable();
        table.rows().every(function () {
            this.child.hide();
        });
        document.querySelectorAll('#booksTable tr').forEach(tr => {
            tr.classList.remove('shown');
        });
        currentOpenRow = null;
        currentMode = null;
    }

    function closeMainForm() {
        const formSection = document.getElementById('formSection');
        if (formSection) {
            formSection.style.display = 'none';
        }
    }
</script>