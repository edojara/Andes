<?php
/**
 * Página de partidos del Club Deportivo Andes
 * Muestra el calendario de partidos y resultados
 */
require_once __DIR__ . '/../includes/header.php';
?>

<main class="events-main">
    <div class="container">
        <h1 class="page-title">Partidos</h1>
        
        <!-- Filtros -->
        <div class="events-filters">
            <button class="filter-btn active" data-filter="all">Todos</button>
            <button class="filter-btn" data-filter="proximos">Próximos</button>
            <button class="filter-btn" data-filter="pasados">Resultados</button>
        </div>

        <!-- Lista de partidos -->
        <div id="events-list" class="events-list">
            <div class="loading">Cargando partidos...</div>
        </div>
    </div>
</main>

<script>
let currentFilter = 'all';

// Cargar partidos según el filtro
async function loadEvents(filter = 'all') {
    currentFilter = filter;
    
    try {
        let url = '/api/partidos.php';
        if (filter === 'proximos') {
            url += '?proximos=true';
        } else if (filter === 'pasados') {
            url += '?pasados=true';
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success && data.data && data.data.length > 0) {
            const eventsHTML = data.data.map(match => {
                const matchDate = new Date(match.fecha);
                const formattedDate = matchDate.toLocaleDateString('es-CL', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                const formattedTime = matchDate.toLocaleTimeString('es-CL', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                const isPast = matchDate < new Date();
                
                return `
                    <div class="event-card ${isPast ? 'past' : 'upcoming'}">
                        <div class="event-date">
                            <span class="date-day">${matchDate.getDate()}</span>
                            <span class="date-month">${matchDate.toLocaleDateString('es-CL', { month: 'short' })}</span>
                            <span class="date-year">${matchDate.getFullYear()}</span>
                        </div>
                        <div class="event-details">
                            <div class="event-teams">
                                <div class="team home">
                                    <div class="team-logo">🏠</div>
                                    <div class="team-name">Andes</div>
                                </div>
                                <div class="event-score">
                                    ${isPast 
                                        ? `<span class="score">${match.goles_local} - ${match.goles_visitante}</span>`
                                        : `<span class="vs">VS</span>`
                                    }
                                </div>
                                <div class="team away">
                                    <div class="team-logo">⚽</div>
                                    <div class="team-name">${match.rival}</div>
                                </div>
                            </div>
                            <div class="event-info">
                                <div class="event-time">${formattedTime}</div>
                                <div class="event-location">${match.lugar}</div>
                                <div class="event-competition">${match.competicion || 'Amistoso'}</div>
                            </div>
                            <div class="event-date-full">${formattedDate}</div>
                        </div>
                    </div>
                `;
            }).join('');
            
            document.getElementById('events-list').innerHTML = eventsHTML;
        } else {
            document.getElementById('events-list').innerHTML = `
                <div class="no-events">
                    <p>No hay partidos ${filter === 'proximos' ? 'programados' : filter === 'pasados' ? 'registrados' : ''} por el momento.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar partidos:', error);
        document.getElementById('events-list').innerHTML = `
            <div class="error">
                <p>Error al cargar los partidos.</p>
            </div>
        `;
    }
}

// Manejar clic en filtros
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        loadEvents(this.dataset.filter);
    });
});

// Cargar partidos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadEvents('all');
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
