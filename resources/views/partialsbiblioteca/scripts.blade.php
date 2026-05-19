<!-- resources/views/books/partials/scripts.blade.php -->

<!-- Variable para rutas de imágenes -->
<script>
    window.storageUrl = "{{ asset('storage') }}";
</script>

<!-- FUNCIONES JAVASCRIPT GLOBALES -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    document.getElementById("clearSearchBtnAdvanced")?.addEventListener("click", function () {
        window.location.href = "{{ route('home') }}";
    });

    let currentOpenRow = null;
    let currentMode = null;

    document.addEventListener('submit', function (e) {
        const form = e.target;

        if (form.classList.contains('book-form')) {
            e.preventDefault();

            const formData = new FormData(form);
            const bookId = formData.get('id');

            // URL por defecto para envío de formularios (el backend valida permisos)
            const apiUrl = bookId ? `/books/${bookId}` : '/books';

            if (bookId) {
                formData.append('_method', 'PUT'); // Simular PUT si es edición
            }

            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(async res => {
                    if (!res.ok) {
                        let errorData;
                        try {
                            errorData = await res.json();
                        } catch (e) {
                            errorData = {
                                message: await res.text()
                            };
                        }
                        throw errorData;
                    }
                    return res.json();
                })
                .then(data => {
                    alert(data.message || 'Guardado correctamente');
                    location.reload();
                })
                .catch(err => {
                    if (err.errors) {
                        alert(Object.values(err.errors).join('\n'));
                    } else if (err.message) {
                        alert(err.message);
                    } else {
                        alert("Error desconocido:\n" + JSON.stringify(err, null, 2));
                    }
                });
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        //Limpiar input de la busqueda
        const clearBtn = document.getElementById('clearSearchBtn');
        const searchInput = document.getElementById('searchInput');

        if (clearBtn && searchInput) {
            clearBtn.addEventListener('click', () => {
                window.location.href = "{{ route('home') }}";
            });
        }

        const mainForm = document.getElementById('formSection');
        if (mainForm) {
            const uploadBtn = mainForm.querySelector('.btn-upload-portada');
            const inputFile = mainForm.querySelector('input[name="portada"]');
            const preview = mainForm.querySelector('#previewPortada');

            if (uploadBtn && inputFile) {
                uploadBtn.addEventListener('click', () => {
                    inputFile.click();
                });

                inputFile.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.innerHTML =
                            `<img src="${e.target.result}" style="max-width:100%; max-height:100%;">`;
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        const btnAdd = document.getElementById('btnAddBook');
        if (btnAdd) {
            btnAdd.addEventListener('click', () => {
                closeAllRows();
                currentOpenRow = null;
                currentMode = null;

                const formSection = document.getElementById('formSection');
                const form = document.getElementById('bookForm');

                if (formSection.style.display === 'block') {
                    formSection.style.display = 'none';
                    return;
                }

                formSection.style.display = 'block';
                form.reset();
                document.getElementById('formTitle').textContent = 'Agregar nuevo material';
                document.getElementById('bookId').value = '';
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
            <div style="display:flex; flex-wrap: wrap; gap:20px;">
                <div style="width:100%; max-width: 200px; margin: 0 auto; flex-shrink:0; text-align: center;">
                    ${book.portada
                ? `<img src="${window.storageUrl}/portadas/${book.portada}"  style="width:100%; max-height:250px; object-fit:contain; border-radius:6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">`
                : `<div style="width:100%; height:200px; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:6px;">
                            <i class="fas fa-book" style="font-size: 4rem; color: #ccc;"></i>
                           </div>`
            }
                </div>
                <div style="flex:1; min-width: 250px; display:flex; flex-direction:column; gap:10px;">
                    <div class="modal-details-grid" style="display:grid; grid-template-columns: 140px 1fr; row-gap:10px; column-gap:10px; font-size:0.95rem; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div><strong>Título:</strong></div> <div>${book.titulo}</div>
                        <div><strong>Autor:</strong></div> <div>${book.autor || 'N/A'}</div>
                        <div><strong>Editorial:</strong></div> <div>${book.editorial || 'N/A'}</div>
                        <div><strong>ISBN:</strong></div> <div>${book.isbn || 'N/A'}</div>
                        <div><strong>Clasificación:</strong></div> <div>${book.clasificacion || 'N/A'}</div>
                        <div><strong>ID Biblioteca:</strong></div> <div>${book.idbiblioteca || 'N/A'}</div>
                        <div><strong>Adquisición:</strong></div> <div>${book.numadqui || 'N/A'}</div>
                        <div><strong>Fecha:</strong></div> <div>${book.fechaingreso || 'N/A'}</div>
                    </div>
                </div>
            </div>
        `;
    }

    function toggleDetails(btn) {
        const book = JSON.parse(btn.dataset.book);

        // Cargar datos en el modal
        const modalBody = document.getElementById('bookDetailsModalBody');
        if (modalBody) {
            modalBody.innerHTML = formatDetails(book);
        }

        // Mostrar modal
        const modal = document.getElementById('bookDetailsModal');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeBookDetailsModal() {
        const modal = document.getElementById('bookDetailsModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Cerrar el modal al hacer clic fuera de él
    window.addEventListener('click', function (event) {
        const modal = document.getElementById('bookDetailsModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });

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