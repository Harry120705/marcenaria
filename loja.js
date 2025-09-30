// Carrossel horizontal para cada categoria
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.carousel-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = btn.getAttribute('data-target');
            var carousel = document.getElementById(targetId);
            if (carousel) {
                var scrollAmount = window.innerWidth < 700 ? 220 : 340;
                if (btn.classList.contains('left')) {
                    carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                } else {
                    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                }
            }
        });
    });

    // Menu hamburguer
    var menuBtn = document.getElementById('menuBtn');
    var sideMenu = document.getElementById('side-menu');
    if (menuBtn && sideMenu) {
        menuBtn.addEventListener('click', function() {
            sideMenu.classList.toggle('open');
            menuBtn.classList.toggle('active');
        });
        document.addEventListener('click', function(e) {
            if (sideMenu.classList.contains('open') && !sideMenu.contains(e.target) && !menuBtn.contains(e.target)) {
                sideMenu.classList.remove('open');
                menuBtn.classList.remove('active');
            }
        });
    }

    // Card de produto dinâmico (Ver mais)
    function abrirCardProduto(id) {
        let cardExistente = document.getElementById('card-produto-detalhe');
        if (cardExistente) cardExistente.remove();
        let overlay = document.createElement('div');
        overlay.id = 'card-produto-detalhe';
        overlay.className = 'card-produto-detalhe-overlay';
        overlay.innerHTML = '<div class="card-produto-detalhe"><div class="carregando">Carregando...</div></div>';
        document.body.appendChild(overlay);
        fetch('produto_detalhe.php?id=' + id)
            .then(r => r.text())
            .then(html => {
                overlay.innerHTML = html;
            });
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) overlay.remove();
        });
    }
    document.querySelectorAll('.btn-vermais').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let id = btn.parentElement.querySelector('input[name="id"]').value;
            abrirCardProduto(id);
        });
    });
});