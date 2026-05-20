<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Catálogo Biblioteca IMIP')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        html, body {
            overflow-x: hidden !important;
            width: 100%;
            max-width: 100%;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('{{ asset('imagenes/fondo.png') }}') center/cover no-repeat fixed;
            color: #333;
            padding-top: 60px;
        }
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
        .main-content {
            margin-left: 260px;
            padding: 24px;
            min-height: calc(100vh - 60px);
            background: transparent;
        }
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
        @media (max-width: 768px) {
            body {
                padding-bottom: 70px;
                padding-top: 56px;
            }
            .sidebar {
                width: 100%;
                height: 65px;
                top: auto;
                bottom: 0;
                left: 0;
                padding: 0;
                flex-direction: row;
                border-right: none;
                border-top: 1px solid var(--gray-300);
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                background: rgba(255, 255, 255, 0.98);
            }
            .sidebar-logo,
            .sidebar-title {
                display: none;
            }
            .sidebar-nav {
                display: flex;
                flex-direction: row;
                width: 100%;
                justify-content: space-around;
                align-items: center;
                padding: 0;
            }
            .sidebar-nav a {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                flex: 1;
                margin: 4px;
                padding: 6px 2px;
                font-size: 0.65rem;
                text-align: center;
                height: calc(100% - 8px);
                gap: 4px;
                background: transparent !important;
                color: #495057 !important;
                border-radius: 8px;
                line-height: 1.1;
                font-weight: normal !important;
            }
            .sidebar-nav a i {
                font-size: 1.4rem;
                margin-bottom: 2px;
            }
            .sidebar-nav a.active {
                background: var(--light) !important;
                color: var(--primary) !important;
                font-weight: 600 !important;
                box-shadow: inset 0 -3px 0 var(--primary);
                border-radius: 8px 8px 0 0;
            }
            .main-content {
                margin-left: 0;
                padding: 12px;
            }
            .header {
                left: 0;
                width: 100%;
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
            .stats .stat-card {
                min-width: 100% !important;
                flex-basis: auto !important;
            }
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
            #searchForm > div:first-child {
                flex-wrap: wrap !important;
            }
            #searchForm > div:first-child > div:first-child {
                flex: none !important;
                width: 100% !important;
            }
            #searchForm button[type="submit"] {
                width: 100% !important;
                justify-content: center;
            }
            #searchForm input {
                font-size: 16px !important;
            }
            .search-section .form-grid {
                grid-template-columns: 1fr !important;
            }
            .search-section .form-grid > div[style*="grid-column"] {
                grid-column: 1 !important;
            }
            .search-section .form-grid > div[style*="grid-column"] select {
                width: 100% !important;
            }
            #advancedSearchForm .form-grid > div[style*="grid-column: 1 / span 3"] {
                grid-column: 1 !important;
                flex-direction: column !important;
                gap: 10px !important;
            }
            #advancedSearchForm .form-grid > div[style*="grid-column: 1 / span 3"] button {
                width: 100% !important;
            }
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
            .table-responsive::after {
                content: '⇄ Desliza para ver más columnas';
                display: block;
                text-align: center;
                font-size: 0.75rem;
                color: #999;
                padding: 4px;
            }
            #formSection {
                padding: 15px !important;
            }
            #formSection .form-grid {
                grid-template-columns: 1fr !important;
            }
            #formSection .form-actions {
                flex-direction: column !important;
                gap: 8px !important;
            }
            #formSection .form-actions button {
                width: 100% !important;
            }
            .stats {
                gap: 12px !important;
            }
        }
        @media (max-width: 480px) {
            .sidebar {
                height: 75px !important;
            }
            .sidebar-logo {
                display: block !important;
                margin-bottom: 5px !important;
                padding: 0 5px !important;
                text-align: center;
            }
            .sidebar-logo img {
                max-height: 30px !important;
                width: auto !important;
            }
            .sidebar-nav {
                padding: 0 !important;
            }
            .sidebar-nav a {
                padding: 4px 0 !important;
                font-size: 0.6rem !important;
            }
            .sidebar-nav a i {
                font-size: 1.2rem !important;
            }
            .header {
                height: 44px !important;
                padding: 0 8px !important;
            }
            .header-title {
                font-size: 0.85rem !important;
            }
            .main-content {
                padding: 56px 8px 8px !important;
            }
            .stats {
                flex-direction: column !important;
                gap: 10px !important;
            }
            .stats .stat-card {
                min-width: auto !important;
                padding: 12px !important;
            }
            .stats .stat-value {
                font-size: 24px !important;
            }
            .material-stats-section > div[style*="flex: 1"] > div[style*="grid"] {
                grid-template-columns: 1fr !important;
            }
            .material-stats-section .btn {
                font-size: 0.75rem !important;
                padding: 6px 8px !important;
            }
            #booksTable th,
            #booksTable td {
                padding: 4px 3px !important;
                font-size: 0.65rem !important;
            }
            #booksTable td:nth-child(2) {
                max-width: 80px;
                font-size: 0.7rem !important;
            }
            #booksTable td:nth-child(1) img,
            #booksTable td:nth-child(1) div {
                width: 22px !important;
                height: 32px !important;
            }
            #booksTable .btn-sm {
                padding: 2px 4px !important;
                font-size: 0.6rem !important;
            }
            #booksTable .badge {
                font-size: 0.55rem !important;
                padding: 1px 3px !important;
            }
            .table-responsive::after {
                font-size: 0.65rem !important;
                padding: 2px !important;
            }
            #searchForm > div:first-child {
                gap: 6px !important;
            }
            #searchForm input {
                padding: 10px 40px 10px 10px !important;
                font-size: 14px !important;
            }
            #searchForm button[type="submit"] {
                padding: 10px 16px !important;
                font-size: 0.85rem !important;
            }
            #clearSearchBtn {
                font-size: 1rem !important;
                right: 8px !important;
            }
            .search-section .form-grid {
                gap: 8px !important;
            }
            .search-section .form-grid .form-group label {
                font-size: 0.8rem !important;
            }
            .search-section .form-grid input,
            .search-section .form-grid select {
                padding: 6px !important;
                font-size: 0.8rem !important;
            }
            #advancedSearchForm .form-grid > div[style*="grid-column: 1 / span 3"] {
                margin-top: 15px !important;
                padding-top: 15px !important;
            }
            #advancedSearchForm .form-grid > div[style*="grid-column: 1 / span 3"] button {
                padding: 10px 20px !important;
                font-size: 0.85rem !important;
            }
            .material-stats-section {
                padding: 10px !important;
                gap: 12px !important;
            }
            .material-stats-section h3 {
                font-size: 0.9rem !important;
                padding-bottom: 6px !important;
            }
            .material-stats-section p {
                font-size: 0.8rem !important;
            }
            .material-stats-section > div[style*="flex: 1"]:last-child {
                padding: 8px !important;
            }
            .material-stats-section > div[style*="flex: 1"]:last-child > div[style*="color"] {
                font-size: 0.8rem !important;
            }
            .dataTables_wrapper .dataTables_info {
                font-size: 0.65rem !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 4px 7px !important;
                font-size: 0.75rem !important;
                min-width: 26px !important;
                height: 26px !important;
            }
            .dataTables_wrapper .dataTables_length label,
            .dataTables_wrapper .dataTables_filter label {
                font-size: 0.75rem !important;
            }
            #formSection {
                padding: 10px !important;
            }
            #formSection h3 {
                font-size: 1rem !important;
            }
            #formSection .form-group input,
            #formSection .form-group select,
            #formSection .form-group textarea {
                padding: 7px 8px !important;
                font-size: 0.85rem !important;
            }
            .add-book-btn {
                padding: 10px 18px !important;
                font-size: 0.85rem !important;
                width: 100% !important;
                justify-content: center !important;
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
    <div class="sidebar">
        <div class="sidebar-logo">
            <a href="https://www.imip.org.mx/imip/" target="_blank" style="text-decoration: none; display: block;">
                <img src="{{ asset('img/logo/IMIP_logo01.png') }}" alt="Logo IMIP" onerror="this.style.display='none'; console.warn('Logo no encontrado');">
            </a>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') || request()->routeIs('search.simple') ? 'active' : '' }}">
               <i class="fas fa-home"></i> Inicio
            </a>
            <a href="{{ route('search.advanced') }}" class="{{ request()->routeIs('search.advanced') ? 'active' : '' }}">
               <i class="fas fa-search"></i> Búsqueda Avanzada
            </a>
            <a href="{{ route('about.library') }}" class="{{ request()->is('sobre-la-biblioteca') ? 'active' : '' }}">
                <i class="fas fa-info-circle"></i> Sobre la biblioteca
            </a>
        </nav>
    </div>

    <div class="header">
        <div class="header-title">Catálogo General Biblioteca IMIP</div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <div style="text-align:center; padding:20px; color:#666; font-size:0.9rem; background:rgba(255,255,255,0.8);">
        Instituto Municipal de Investigación y Planeación<br>
        C. Benjamín Franklin No. 4185 Colonia Progresista C.P. 32310 Ciudad Juárez, Chih.<br>
        Tel. (656) 6136520 &nbsp;|&nbsp; © Derechos reservados 2026
    </div>

    @stack('scripts')
</body>
</html>