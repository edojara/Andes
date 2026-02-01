<?php
/**
 * Página de galería del Club Deportivo Andes
 * Muestra fotos de los partidos
 */
require_once __DIR__ . '/../includes/header.php';
?>

<main class="gallery-main">
    <div class="container">
        <h1 class="page-title">Galería de Fotos</h1>
        
        <!-- Filtros por partido -->
        <div class="gallery-filters">
            <button class="filter-btn active" data-match="all">Todos los partidos</button>
        </div>

        <!-- Grid de fotos -->
        <div id="gallery-grid" class="gallery-grid">
            <div class="loading">Cargando fotos...</div>
        </div>
    </div>
</main>

<!-- Modal para ver fotos en grande -->
<div id="photo-modal" class="photo-modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <img id="modal-image" src="" alt="">
        <div id="modal-caption" class="modal-caption"></div>
    </div>
</div>

<script>
let currentMatch = 'all';

// Cargar fotos según el filtro
async function loadGallery(matchId = 'all') {
    currentMatch = matchId;
    
    try {
        let url = '/api/fotos.php';
        if (matchId !== 'all') {
            url += `?partido_id=${matchId}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success && data.data && data.data.length > 0) {
            // Agrupar fotos por partido
            const groupedPhotos = {};
            data.data.forEach(photo => {
                const partidoId = photo.partido_id || 'sin-partido';
                if (!groupedPhotos[partidoId]) {
                    groupedPhotos[partidoId] = [];
                }
                groupedPhotos[partidoId].push(photo);
            });
            
            // Crear filtros de partidos si no existen
            if (matchId === 'all' && Object.keys(groupedPhotos).length > 1) {
                const filtersContainer = document.querySelector('.gallery-filters');
                const existingFilters = filtersContainer.querySelectorAll('.filter-btn');
                
                // Solo agregar filtros si no están ya creados
                if (existingFilters.length === 1) {
                    Object.keys(groupedPhotos).forEach(partidoId => {
                        if (partidoId !== 'sin-partido') {
                            const btn = document.createElement('button');
                            btn.className = 'filter-btn';
                            btn.dataset.match = partidoId;
                            btn.textContent = `Partido #${partidoId}`;
                            filtersContainer.appendChild(btn);
                        }
                    });
                    
                    // Agregar event listeners a los nuevos filtros
                    filtersContainer.querySelectorAll('.filter-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                            this.classList.add('active');
                            loadGallery(this.dataset.match);
                        });
                    });
                }
            }
            
            // Crear HTML de las fotos
            const galleryHTML = data.data.map(photo => `
                <div class="gallery-item" data-partido="${photo.partido_id || 'sin-partido'}">
                    <img src="${photo.url}" alt="${photo.descripcion || 'Foto del partido'}" loading="lazy">
                    <div class="gallery-overlay">
                        <p>${photo.descripcion || 'Foto del partido'}</p>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('gallery-grid').innerHTML = galleryHTML;
            
            // Agregar event listeners para abrir modal
            document.querySelectorAll('.gallery-item').forEach(item => {
                item.addEventListener('click', function() {
                    const img = this.querySelector('img');
                    const caption = this.querySelector('.gallery-overlay p').textContent;
                    openModal(img.src, caption);
                });
            });
        } else {
            document.getElementById('gallery-grid').innerHTML = `
                <div class="no-photos">
                    <p>No hay fotos disponibles.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al cargar galería:', error);
        document.getElementById('gallery-grid').innerHTML = `
            <div class="error">
                <p>Error al cargar la galería.</p>
            </div>
        `;
    }
}

// Abrir modal con foto
function openModal(src, caption) {
    const modal = document.getElementById('photo-modal');
    const modalImg = document.getElementById('modal-image');
    const modalCaption = document.getElementById('modal-caption');
    
    modalImg.src = src;
    modalCaption.textContent = caption;
    modal.style.display = 'flex';
}

// Cerrar modal
document.querySelector('.close-modal').addEventListener('click', function() {
    document.getElementById('photo-modal').style.display = 'none';
});

document.getElementById('photo-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('photo-modal').style.display = 'none';
    }
});

// Cargar galería al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadGallery('all');
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
