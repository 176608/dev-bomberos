@php
    if (!isset($books)) $books = collect();
@endphp

<div class="book-list" style="margin-top: 20px;">
    @if($books->isNotEmpty())
        <div class="table-responsive">
            <table id="booksTable" class="table table-striped table-hover align-middle" style="background:white; border-radius:8px; overflow:hidden; table-layout: fixed; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px; text-align: center; padding: 16px 12px;">Portada</th>
                        <th style="width: 30%; text-align: center; padding: 16px 12px;">Título</th>
                        <th style="width: 20%; text-align: center; padding: 16px 12px;">Autor</th>
                        <th style="width: 15%; text-align: center; padding: 16px 12px;">Editorial</th>
                        <th style="width: 12%; text-align: center; padding: 16px 12px;">ISBN</th>
                        <th style="width: 10%; text-align: center; padding: 16px 12px;">Tipo</th>
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
                            <td style="padding: 12px; vertical-align: middle; font-size:0.9rem; color:#555;">{{ $book->autor ?? 'N/A' }}</td>
                            <td style="padding: 12px; vertical-align: middle; font-size:0.9rem; color:#555;">{{ $book->editorial ?? 'N/A' }}</td>
                            <td style="padding: 12px; text-align: center; vertical-align: middle; font-size:0.85rem; color:#666;">{{ $book->isbn ?? 'N/A' }}</td>
                            <td style="padding: 12px; text-align: center; vertical-align: middle;">
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