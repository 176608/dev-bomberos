@php
    if (!isset($books)) $books = collect();
@endphp

<div class="book-list" style="margin-top: 20px;">
    @if($books->isNotEmpty())
        <div class="table-responsive">
            <table id="booksTable" class="table table-striped table-hover align-middle" style="background:white; border-radius:8px; overflow:hidden; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px; text-align: center; padding: 16px 12px;">Portada</th>
                        <th style="text-align: center; padding: 16px 12px;">Título</th>
                        <th class="col-movil-ocultar" style="width: 20%; text-align: center; padding: 16px 12px;">Autor</th>
                        <th class="col-movil-ocultar" style="width: 15%; text-align: center; padding: 16px 12px;">Editorial</th>
                        <th class="col-movil-ocultar" style="width: 12%; text-align: center; padding: 16px 12px;">ISBN</th>
                        <th class="col-movil-ocultar" style="width: 10%; text-align: center; padding: 16px 12px;">Tipo</th>
                        <th style="width: 80px; text-align: center; padding: 16px 12px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                        <tr>
                            <td style="padding: 12px; text-align: center; vertical-align: middle;">
                                @if($book->portada)
                                    <img src="{{ asset('storage/portadas/' . $book->portada) }}" 
                                         alt="Portada" 
                                         style="width:50px; height:70px; object-fit:contain; border-radius:4px; box-shadow:0 2px 4px rgba(0,0,0,0.1);"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display:none; width:50px; height:70px; align-items:center; justify-content:center; background:#f8f9fa; border-radius:4px; color:#adb5bd;">
                                        <i class="fas fa-book"></i>
                                    </div>
                                @else
                                    <div style="width:50px; height:70px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; border-radius:4px; color:#adb5bd;">
                                        <i class="fas fa-book"></i>
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 12px; vertical-align: middle; font-size:0.95rem;">{{ Str::limit($book->titulo, 60) }}</td>
                            <td class="col-movil-ocultar" style="padding: 12px; vertical-align: middle; font-size:0.9rem; color:#555;">{{ $book->autor ?? 'N/A' }}</td>
                            <td class="col-movil-ocultar" style="padding: 12px; vertical-align: middle; font-size:0.9rem; color:#555;">{{ $book->editorial ?? 'N/A' }}</td>
                            <td class="col-movil-ocultar" style="padding: 12px; text-align: center; vertical-align: middle; font-size:0.85rem; color:#666;">{{ $book->isbn ?? 'N/A' }}</td>
                            <td class="col-movil-ocultar" style="padding: 12px; text-align: center; vertical-align: middle;">
                                <span class="badge" style="background: #e9ecef; color: #495057; font-size:0.8rem; padding:4px 8px; border-radius:4px;">{{ $book->tipo_material ?? 'Libro' }}</span>
                            </td>
                            <td style="padding: 12px; text-align: center; vertical-align: middle;">
                                <button class="btn btn-sm btn-view" data-book='@json($book)' onclick="toggleDetails(this)" title="Ver detalles" style="background:#495057; color:white; border:none; border-radius:4px; padding:6px 10px;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="background: white; padding: 40px; text-align: center; border-radius: 8px; border: 1px solid #e5e7eb; margin-top: 20px;">
            <i class="fas fa-search" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 15px;"></i>
            <h4 style="color: #334155; margin-bottom: 8px;">No hay resultados</h4>
            <p style="color: #64748b; margin: 0;">Prueba con otros términos o filtros.</p>
        </div>
    @endif
</div>

<!-- Modal de Detalles -->
<div id="bookDetailsModal" style="display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div style="background-color: #fefefe; margin: auto; padding: 0; border: 1px solid #888; width: 95%; max-width: 700px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); animation: animatetop 0.3s; display: flex; flex-direction: column; max-height: 90vh;">
        <div style="padding: 15px 20px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #1e7390 0%, #2c5f5e 100%); color: white; border-radius: 12px 12px 0 0;">
            <h5 style="margin: 0; font-weight: 600; font-size: 1.2rem;"><i class="fas fa-info-circle"></i> Detalles del Material</h5>
            <span onclick="closeBookDetailsModal()" style="color: white; font-size: 24px; font-weight: bold; cursor: pointer; line-height: 1;">&times;</span>
        </div>
        <div id="bookDetailsModalBody" style="padding: 20px; overflow-y: auto; flex: 1;">
            <!-- Contenido dinámico -->
        </div>
        <div style="padding: 15px 20px; border-top: 1px solid #e9ecef; text-align: right; background: #f8f9fa; border-radius: 0 0 12px 12px;">
            <button onclick="closeBookDetailsModal()" class="btn btn-secondary" style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">Cerrar</button>
        </div>
    </div>
</div>

<style>
@keyframes animatetop {
    from {top: -50px; opacity: 0}
    to {top: 0; opacity: 1}
}
/* Ocultar columnas en móvil (< 768px) */
@media (max-width: 767.98px) {
    table#booksTable th.col-movil-ocultar,
    table#booksTable td.col-movil-ocultar {
        display: none !important;
    }
}
@media (max-width: 576px) {
    .modal-details-grid {
        grid-template-columns: 1fr !important;
        gap: 5px !important;
    }
    .modal-details-grid > div:nth-child(odd) {
        font-weight: 600;
        margin-top: 8px;
    }
    .modal-details-grid > div:nth-child(even) {
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
    }
}
</style>