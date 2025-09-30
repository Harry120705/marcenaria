// Menu hamburguer para loja.php
document.addEventListener('DOMContentLoaded', function() {
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
});
// script.js
// Animação fluida para menu hamburguer
document.addEventListener('DOMContentLoaded', function() {
	const menuBtn = document.getElementById('menuBtn');
	const sideMenu = document.getElementById('side-menu');
	if (menuBtn && sideMenu) {
		menuBtn.addEventListener('click', function(e) {
			e.stopPropagation();
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
});
