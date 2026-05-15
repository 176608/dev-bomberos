<!-- Estadísticas (visible para todos) -->
<div class="stats" style="display:flex; gap:20px; margin-bottom:24px; flex-wrap:wrap;">
    <div class="stat-card"
        style="background:white; padding:16px; border-radius:8px; box-shadow:var(--shadow); min-width:180px; text-align:center; flex:1;">
        <div class="stat-value" style="font-size:28px; font-weight:bold; color:var(--primary);">
            {{ $totalBooks ?? 0 }}
        </div>
        <div class="stat-label" style="color:#666;">Materiales registrados</div>
    </div>
    <div class="stat-card"
        style="background:white; padding:16px; border-radius:8px; box-shadow:var(--shadow); min-width:180px; text-align:center; flex:1;">
        <div class="stat-value" style="font-size:28px; font-weight:bold; color:var(--primary);">
            {{ $totalBibliotecas ?? 0 }}
        </div>
        <div class="stat-label" style="color:#666;">Biblioteca MPDU Abigail García Espinosa
</div>
    </div>
</div>