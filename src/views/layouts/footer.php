    </div><!-- Fechamento do .container do conteúdo principal -->
</main>

<footer class="footer" id="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <hr class="mt-4">
                <p class="text-center text-muted mb-4">
                    &copy; <?= date('Y') ?> P21 Sistemas. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
/* Estilos para as mensagens flash */
.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

/* Estilos do footer */
.footer {
    width: 100%;
    background-color: #fff;
    transition: position 0.3s;
}

.footer.fixed {
    position: fixed;
    bottom: 0;
    left: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const footer = document.getElementById('footer');
    
    function adjustFooter() {
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const hasScroll = documentHeight > windowHeight;

        if (hasScroll) {
            footer.classList.remove('fixed');
        } else {
            footer.classList.add('fixed');
        }
    }

    // Ajusta o footer inicialmente
    adjustFooter();

    // Ajusta o footer quando a janela for redimensionada
    window.addEventListener('resize', adjustFooter);

    // Ajusta o footer quando o conteúdo da página mudar
    const observer = new MutationObserver(adjustFooter);
    observer.observe(document.body, { 
        childList: true, 
        subtree: true 
    });
});
</script>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= APP_URL ?>/js/app.js"></script>
</body>
</html> 