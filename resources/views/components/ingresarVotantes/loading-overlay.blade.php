<div id="loadingOverlay" class="loading-overlay" style="display:none;">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <div class="loading-text">Procesando archivo Excel</div>
        <div class="loading-subtext">Por favor espere...</div>
        <div class="loading-progress">
            <div class="loading-progress-bar" id="progressBar" style="width: 0%"></div>
        </div>
    </div>
</div>
<script>
    function showLoadingOverlay() {
        document.getElementById("loadingOverlay").style.display = "flex";
        setProgress(0);
    }

    function hideLoadingOverlay() {
        document.getElementById("loadingOverlay").style.display = "none";
    }

    function setProgress(percent) {
        const progressBar = document.getElementById("progressBar");
        progressBar.style.width = percent + "%";
    }
</script>
<style>
/* Estilos del Loading Overlay Simplificado */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease-out;
}

.loading-content {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    min-width: 320px;
    animation: slideIn 0.4s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0; 
        transform: translateY(-30px) scale(0.9); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    }
}

/* Spinner animado */
.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #e3e3e3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Texto estático */
.loading-text {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.loading-subtext {
    color: #7f8c8d;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

/* Barra de progreso */
.loading-progress {
    background: #ecf0f1;
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}

.loading-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #3498db, #2ecc71);
    border-radius: 4px;
    transition: width 0.3s ease;
    position: relative;
    overflow: hidden;
}

.loading-progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Responsive */
@media (max-width: 768px) {
    .loading-content {
        margin: 1rem;
        min-width: auto;
        padding: 1.5rem;
    }
}

</style>