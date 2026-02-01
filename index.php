<?php
/**
 * Página principal del sitio web del Club Deportivo Andes
 * Muestra información del próximo partido y destacados
 */
require_once __DIR__ . '/includes/header.php';
?>

<main class="home-main">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Club Deportivo Andes</h1>
            <p class="hero-subtitle">Pasión, esfuerzo y compromiso en cada partido</p>
            <a href="/pages/events.php" class="btn btn-primary">Ver Próximo Partido</a>
        </div>
    </section>

    <!-- Próximo Partido -->
    <section class="next-match-section">
        <div class="container">
            <h2 class="section-title">Próximo Partido</h2>
            <div id="next-match" class="next-match-card">
                <div class="loading">Cargando información del partido...</div>
            </div>
        </div>
    </section>

    <!-- Últimos Resultados -->
    <section class="recent-results-section">
        <div class="container">
            <h2 class="section-title">Últimos Resultados</h2>
            <div id="recent-results" class="results-grid">
                <div class="loading">Cargando resultados...</div>
            </div>
        </div>
    </section>

    <!-- Jugadores Destacados -->
    <section class="featured-players-section">
        <div class="container">
            <h2 class="section-title">Jugadores Destacados</h2>
            <div id="featured-players" class="players-grid">
                <div class="loading">Cargando jugadores...</div>
            </div>
        </div>
    </section>

    <!-- Galería Reciente -->
    <section class="recent-gallery-section">
        <div class="container">
            <h2 class="section-title">Galería Reciente</h2>
            <div id="recent-gallery" class="gallery-grid">
                <div class="loading">Cargando galería...</div>
            </div>
            <div class="gallery-cta">
                <a href="/pages/gallery.php" class="btn btn-secondary">Ver Galería Completa</a>
            </div>
        </div>
    </section>
</main>

<script>
// Cargar información del próximo partido
async function loadNextMatch() {
    try {
        const response = await fetch('/api/partidos.php?proximo=true');
        const data = await response.json();
        
        if (data.success && data.data) {
            const match = data.data;
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

            document.getElementById('next-match').innerHTML = `
                <div class="match-card">
                    <div class="match-date">
                        <span class="date-day">${matchDate.getDate()}</span>
                        <span class="date-month">${matchDate.toLocaleDateString('es-CL', { month: 'short' })}</span>
                    </div>
                    <div class="match-teams">
                        <div class="team home">
                            <div class="team-logo">🏠</div>
                            <div class="team-name">Andes</div>
                        </div>
                        <div class="match-vs">VS</div>
                        <div class="team away">
                            <div class="team-logo">⚽</div>
                            <div class="team-name">${match.rival}</div>
                        </div>
                    </div>
                    <div class="match-info">
                        <div class="match-time">${formattedTime}</div>
                        <div class="match-location">${match.lugar}</div>
                        <div class="match-date-full">${formattedDate}</div>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('next-match').innerHTML = `
                <div class="no-match">
                    <p>No hay partidos programados por el momento.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar próximo partido:', error);
        document.getElementById('next-match').innerHTML = `
            <div class="error">
                <p>Error al cargar la información del partido.</p>
            </div>
        `;
    }
}

// Cargar últimos resultados
async function loadRecentResults() {
    try {
        const response = await fetch('/api/partidos.php?pasados=true&limit=3');
        const data = await response.json();
        
        if (data.success && data.data && data.data.length > 0) {
            const resultsHTML = data.data.map(match => {
                const matchDate = new Date(match.fecha);
                const formattedDate = matchDate.toLocaleDateString('es-CL', {
                    day: '2-digit',
                    month: 'short'
                });
                
                return `
                    <div class="result-card">
                        <div class="result-date">${formattedDate}</div>
                        <div class="result-teams">
                            <span class="team-name">Andes</span>
                            <span class="result-score">${match.goles_local} - ${match.goles_visitante}</span>
                            <span class="team-name">${match.rival}</span>
                        </div>
                        <div class="result-competition">${match.competicion || 'Amistoso'}</div>
                    </div>
                `;
            }).join('');
            
            document.getElementById('recent-results').innerHTML = resultsHTML;
        } else {
            document.getElementById('recent-results').innerHTML = `
                <div class="no-results">
                    <p>No hay resultados registrados aún.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar resultados:', error);
        document.getElementById('recent-results').innerHTML = `
            <div class="error">
                <p>Error al cargar los resultados.</p>
            </div>
        `;
    }
}

// Cargar jugadores destacados
async function loadFeaturedPlayers() {
    try {
        const response = await fetch('/api/jugadores.php?destacados=true&limit=4');
        const data = await response.json();
        
        if (data.success && data.data && data.data.length > 0) {
            const playersHTML = data.data.map(player => `
                <div class="player-card">
                    <div class="player-photo">
                        <div class="player-placeholder">${player.nombre.charAt(0)}</div>
                    </div>
                    <div class="player-info">
                        <h3 class="player-name">${player.nombre}</h3>
                        <p class="player-position">${player.posicion}</p>
                        <div class="player-stats">
                            <span class="stat">🎯 ${player.goles || 0} Goles</span>
                            <span class="stat">🏃 ${player.partidos_jugados || 0} Partidos</span>
                        </div>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('featured-players').innerHTML = playersHTML;
        } else {
            document.getElementById('featured-players').innerHTML = `
                <div class="no-players">
                    <p>No hay jugadores registrados aún.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar jugadores:', error);
        document.getElementById('featured-players').innerHTML = `
            <div class="error">
                <p>Error al cargar los jugadores.</p>
            </div>
        `;
    }
}

// Cargar galería reciente
async function loadRecentGallery() {
    try {
        const response = await fetch('/api/fotos.php?limit=6');
        const data = await response.json();
        
        if (data.success && data.data && data.data.length > 0) {
            const galleryHTML = data.data.map(photo => `
                <div class="gallery-item">
                    <img src="${photo.url}" alt="${photo.descripcion || 'Foto del partido'}" loading="lazy">
                    <div class="gallery-overlay">
                        <p>${photo.descripcion || 'Foto del partido'}</p>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('recent-gallery').innerHTML = galleryHTML;
        } else {
            document.getElementById('recent-gallery').innerHTML = `
                <div class="no-photos">
                    <p>No hay fotos disponibles aún.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar galería:', error);
        document.getElementById('recent-gallery').innerHTML = `
            <div class="error">
                <p>Error al cargar la galería.</p>
            </div>
        `;
    }
}

// Cargar todo el contenido al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadNextMatch();
    loadRecentResults();
    loadFeaturedPlayers();
    loadRecentGallery();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
