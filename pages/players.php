<?php
/**
 * Página de jugadores del Club Deportivo Andes
 * Muestra el plantel completo con estadísticas
 */
require_once __DIR__ . '/../includes/header.php';
?>

<main class="players-main">
    <div class="container">
        <h1 class="page-title">Nuestro Plantel</h1>
        
        <!-- Filtros por posición -->
        <div class="players-filters">
            <button class="filter-btn active" data-position="all">Todos</button>
            <button class="filter-btn" data-position="Portero">Porteros</button>
            <button class="filter-btn" data-position="Defensa">Defensas</button>
            <button class="filter-btn" data-position="Mediocampo">Mediocampistas</button>
            <button class="filter-btn" data-position="Delantero">Delanteros</button>
        </div>

        <!-- Grid de jugadores -->
        <div id="players-grid" class="players-grid">
            <div class="loading">Cargando jugadores...</div>
        </div>
    </div>
</main>

<script>
let currentPosition = 'all';

// Cargar jugadores según el filtro
async function loadPlayers(position = 'all') {
    currentPosition = position;
    
    try {
        let url = '/api/jugadores.php';
        if (position !== 'all') {
            url += `?posicion=${encodeURIComponent(position)}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success && data.data && data.data.length > 0) {
            const playersHTML = data.data.map(player => `
                <div class="player-card" data-position="${player.posicion}">
                    <div class="player-photo">
                        <div class="player-placeholder">${player.nombre.charAt(0)}</div>
                    </div>
                    <div class="player-info">
                        <h3 class="player-name">${player.nombre}</h3>
                        <p class="player-number">#${player.numero_camiseta || '-'}</p>
                        <p class="player-position">${player.posicion}</p>
                        <div class="player-stats">
                            <div class="stat-item">
                                <span class="stat-label">Partidos</span>
                                <span class="stat-value">${player.partidos_jugados || 0}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Goles</span>
                                <span class="stat-value">${player.goles || 0}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Asistencias</span>
                                <span class="stat-value">${player.asistencias || 0}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('players-grid').innerHTML = playersHTML;
        } else {
            document.getElementById('players-grid').innerHTML = `
                <div class="no-players">
                    <p>No hay jugadores ${position !== 'all' ? 'en la posición ' + position : ''} registrados.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar jugadores:', error);
        document.getElementById('players-grid').innerHTML = `
            <div class="error">
                <p>Error al cargar los jugadores.</p>
            </div>
        `;
    }
}

// Manejar clic en filtros
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        loadPlayers(this.dataset.position);
    });
});

// Cargar jugadores al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadPlayers('all');
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
