// Sélectionne le bouton hamburger et la navbar
const navbarToggle = document.getElementById('navbar-toggle');
const navbar = document.getElementById('navbar');

// Ajoute un événement au clic sur le hamburger
navbarToggle.addEventListener('click', () => {
  navbar.classList.toggle('open');
});
