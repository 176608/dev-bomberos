<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Catálogo Biblioteca IMIP')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary: #1e7390;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('{{ asset('imagenes/fondo.png') }}') center/cover no-repeat fixed;
            color: #333;
            padding-top: 60px;
        }
        /* Sidebar izquierdo con logo */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow);
            z-index: 100;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            border-right: 1px solid var(--gray-300);
            overflow-y: auto;
        }
        .sidebar-logo {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 20px;
        }
        .sidebar-logo img {
            max-height: 80px;
            width: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto 12px;
        }
        .sidebar-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin: 0;
            line-height: 1.3;
            display: block;
            white-space: normal;
            word-break: break-word;
        }
        .sidebar-nav {
            flex: 1;
            padding: 0 15px;
        }
        .sidebar-nav a {
            display: block;
            padding: 12px 20px;
            color: #495057;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 6px;
            transition: var(--transition);
        }
        .sidebar-nav a:hover {
            background: var(--gray-200);
            color: var(--primary);
        }
        .sidebar-nav a.active {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }
        /* Header */
        .header {
            background: linear-gradient(135deg, #3a7d7c #2c5f5e);
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 90;
            box-shadow: var(--shadow);
            background-color: #3a7d7c;
        }
        .header-title {
            font-weight: 600;
            font-size: 1.3rem;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-menu span {
            font-size: 0.95rem;
        }
        .logout-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 6px 12px;
            border-radius: 4px;
            transition: var(--transition);
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
        }
        /* Contenido principal */
        .main-content {
            margin-left: 260px;
            padding: 24px;
            min-height: calc(100vh - 60px);
            background: transparent;
        }
        /* Botón Agregar */
        .add-book-btn {
            background: var(--success);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        .add-book-btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        /* Formulario */
        .form-section {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 24px;
            margin-bottom: 30px;
            display: none;
        }
        .form-section h3 {
            color: var(--primary);
            margin-top: 0;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 12px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #495057;
            width: 100%;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 1rem;
            transition: var(--transition);
            box-sizing: border-box;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 115, 144, 0.2);
        }
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: var(--transition);
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: #1a5d73;
        }
        .btn-secondary {
            background: var(--gray-200);
            color: #333;
        }
        .btn-secondary:hover {
            background: #ced4da;
        }
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        .btn-cancel:hover {
            background: #5a6268;
        }
        /* Lista de libros */
        .book-list {
            margin-top: 20px;
        }
        .book-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 16px;
            transition: var(--transition);
        }
        .book-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .book-header {
            display: flex;
            padding: 16px;
            background: var(--light);
            border-bottom: 1px solid var(--gray-300);
            align-items: center;
            justify-content: space-between;
        }
        .book-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }
        .book-meta {
            font-size: 0.9rem;
            color: #666;
        }
        .book-actions {
            display: flex;
            gap: 8px;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 4px;
        }
        .btn-view {
            background: #495057;
            color: white;
        }
        .btn-edit {
            background: var(--primary);
            color: white;
        }
        .btn-delete {
            background: var(--danger);
            color: white;
        }
        .book-body {
            padding: 16px;
            display: none;
            background: #f0f8ff;
            border-top: 1px solid var(--gray-300);
        }
        .book-details {
            background: #e6f7fb;
            border-left: 4px solid var(--primary);
            padding: 16px;
            border-radius:  6px 0;
            margin: 16px 0;
        }
        .details-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        .details-label {
            font-weight: 600;
            min-width: 120px;
            color: #495057;
        }
        .details-value {
            flex: 1;
            color: #333;
        }
        .portada-container {
            margin-top: 16px;
            text-align: center;
        }
        .portada-preview {
            width: 120px;
            height: 160px;
            background: #f8f9fa;
            border: 2px dashed var(--gray-300);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            position: relative;
            overflow: hidden;
        }
        .portada-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .camera-icon {
            font-size: 2.5rem;
            color: var(--gray-400);
        }
        .upload-portada {
            display: none;
        }
        /* Detalle de ficha */
        .detail-card {
            padding: 20px;
            margin: 10px 0;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            gap: 20px;
        }
        .detail-card h3 {
            margin: 0 0 8px;
            text-align: center;
            font-size: 1.1rem;
            color: var(--primary);
        }
        .detail-portada {
            width: 200px;
            flex-shrink: 0;
            text-align: center;
        }
        .detail-portada img {
            width: 100%;
            max-height: 160px;
            object-fit: contain;
            border-radius: 6px;
        }
        .detail-no-portada {
            width: 100%;
            height: 140px;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            color: #adb5bd;
            font-size: 2rem;
        }
        .detail-info {
            flex: 1;
            min-width: 0;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 6px 12px;
            font-size: 0.9rem;
            background: var(--gray-200);
            padding: 16px;
            border-radius: 10px;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            white-space: nowrap;
        }
        .detail-value {
            color: #333;
            overflow-wrap: break-word;
            word-break: break-word;
            min-width: 0;
        }
        @media (max-width: 768px) {
            .detail-card {
                flex-direction: column;
                padding: 12px;
                gap: 10px;
            }
            .detail-portada {
                width: 100%;
                max-width: 120px;
                margin: 0 auto;
            }
            .detail-portada img {
                max-height: 100px;
            }
            .detail-no-portada {
                height: 100px;
            }
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 3px;
                padding: 10px;
                font-size: 0.8rem;
            }
            .detail-label {
                white-space: normal;
                font-size: 0.75rem;
                color: #666;
                margin-top: 4px;
            }
            .detail-label:first-child {
                margin-top: 0;
            }
            .detail-card h3 {
                font-size: 0.9rem;
            }
        }
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                padding: 10px 0;
            }
            .sidebar-logo, .sidebar-title {
                display: none;
            }
            .sidebar-nav {
                padding: 0 5px;
            }
            .sidebar-nav a {
                padding: 12px 0;
                text-align: center;
                font-size: 0;
            }
            .sidebar-nav a i {
                font-size: 1.3rem;
            }
            .main-content {
                margin-left: 60px;
                padding: 12px;
            }
            .header {
                left: 60px;
                padding: 10px 12px;
            }
            .header-title {
                font-size: 0.95rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .book-header {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            .book-actions {
                justify-content: center;
            }
            .search-section {
                padding: 12px;
            }
            .details-row {
                flex-direction: column;
                gap: 4px;
            }
            .details-label {
                min-width: auto;
            }
            /* Stats cards */
            .stats .stat-card {
                min-width: 100% !important;
                flex-basis: auto !important;
            }
            /* Material stats */
            .material-stats-section {
                padding: 12px !important;
            }
            .material-stats-section > div[style*="min-width"] {
                min-width: 100% !important;
                flex: none !important;
                width: 100% !important;
            }
            .material-stats-section .btn {
                font-size: 0.8rem !important;
                padding: 8px 8px !important;
            }
            .material-stats-section h3 {
                font-size: 1rem !important;
            }
            .material-stats-section > div[style*="flex: 1"] > div[style*="grid"] {
                grid-template-columns: 1fr !important;
            }
            .material-stats-section > div[style*="flex: 1"]:last-child {
                padding: 10px !important;
            }
            /* Busqueda simple */
            #searchForm > div:first-child,
            #advancedSearchForm > div > div:first-child > div:first-child {
                flex-wrap: wrap !important;
            }
            #searchForm > div:first-child > div:first-child,
            #advancedSearchForm > div > div:first-child > div:first-child > div:first-child {
                flex: none !important;
                width: 100% !important;
            }
            #searchForm button[type="submit"],
            #advancedSearchForm button[type="submit"] {
                width: 100% !important;
                justify-content: center;
            }
            #searchForm input,
            #advancedSearchForm input {
                font-size: 16px !important;
            }
            /* Override inline grid de búsqueda avanzada */
            .search-section .form-grid {
                grid-template-columns: 1fr !important;
            }
            .search-section .form-grid > div[style*="grid-column"] {
                grid-column: 1 !important;
            }
            .search-section .form-grid > div[style*="grid-column"] select {
                width: 100% !important;
            }
            /* Tabla completa con scroll horizontal */
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                position: relative;
            }
            #booksTable {
                table-layout: auto !important;
                width: auto !important;
                min-width: 100%;
            }
            #booksTable th,
            #booksTable td {
                padding: 6px 5px !important;
                font-size: 0.75rem !important;
                white-space: nowrap;
            }
            #booksTable td:nth-child(2) {
                white-space: normal;
                max-width: 110px;
            }
            #booksTable td:nth-child(1) img,
            #booksTable td:nth-child(1) div {
                width: 28px !important;
                height: 38px !important;
            }
            #booksTable .btn-sm {
                padding: 3px 5px !important;
                font-size: 0.7rem !important;
            }
            #booksTable .badge {
                font-size: 0.65rem !important;
                padding: 2px 4px !important;
            }
            /* Forzar que DataTables no limite el ancho */
            #booksTable_wrapper {
                overflow: visible !important;
            }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
                margin-bottom: 6px;
            }
            .dataTables_wrapper .dataTables_filter input {
                max-width: 100%;
                width: 100%;
                box-sizing: border-box;
            }
            div.dataTables_wrapper div.dataTables_paginate {
                float: none;
                text-align: center;
                margin-top: 12px;
            }
            .dataTables_wrapper .dataTables_info {
                font-size: 0.75rem;
            }
            /* Indicador sutil de scroll */
            .table-responsive::after {
                content: '⇄ Desliza para ver más columnas';
                display: block;
                text-align: center;
                font-size: 0.75rem;
                color: #999;
                padding: 4px;
            }
        }
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        .search-section {
            border: 1px solid #e5e7eb;
            padding: 16px;
            border-radius: 8px;
            background: #fff;
        }
        .search-section-title {
            font-weight: 600;
            margin-bottom: 12px;
        }
        .search-section-title span {
            font-weight: normal;
            color: #666;
            font-size: 0.85rem;
        }
        .search-divider {
            text-align: center;
            margin: 16px 0;
            font-size: 0.85rem;
            color: #888;
            position: relative;
        }
        .search-divider::before,
        .search-divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #ddd;
        }
        .search-divider::before {
            left: 0;
        }
        .search-divider::after {
            right: 0;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos para DataTables (personalizados) */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 16px;
            display: flex;
            justify-content: center;
            gap: 4px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 10px;
            margin: 0;
            border-radius: 4px;
            background: white;
            border: 1px solid var(--gray-300);
            color: #495057;
            font-size: 0.875rem;
            min-width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-1px);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary);
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(30, 115, 144, 0.2);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 0.875rem;
            color: #666;
            margin-top: 12px;
            text-align: center;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            padding: 6px 12px;
            min-width: auto;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar izquierdo -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <a href="https://www.imip.org.mx/imip/" target="_blank" style="text-decoration: none; display: block;">
                <img src="{{ asset('img/logo/IMIP_logo01.png') }}" alt="Logo IMIP" onerror="this.style.display='none'; console.warn('Logo no encontrado');">
            </a>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('home') }}" 
               class="{{ request()->routeIs('home') || request()->routeIs('search.simple') ? 'active' : '' }}">
               <i class="fas fa-home"></i> Inicio
            </a>
            <a href="{{ route('search.advanced') }}" 
               class="{{ request()->routeIs('search.advanced') ? 'active' : '' }}">
               <i class="fas fa-search"></i> Búsqueda Avanzada
            </a>
            <a href="{{ route('about.library') }}" class="{{ request()->is('sobre-la-biblioteca') ? 'active' : '' }}">
                <i class="fas fa-info-circle"></i> Sobre la biblioteca
            </a>
        </nav>
    </div>

    <!-- Header superior -->
    <div class="header">
        <div class="header-title">
            📚 Catálogo General Biblioteca IMIP
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">
        @yield('content')
    </div>

    <div style="text-align:center; padding:20px; color:#666; font-size:0.9rem; margin-top:40px; background:rgba(255,255,255,0.8); position:relative;">
        Instituto Municipal de Investigación y Planeación<br>
        C. Benjamín Franklin No. 4185 Colonia Progresista C.P. 32310 Ciudad Juárez, Chih.<br>
        Tel. (656) 6136520 &nbsp;|&nbsp; © Derechos reservados 2026
    </div>

    @stack('scripts')
</body>
</html>