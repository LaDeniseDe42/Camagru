document.addEventListener("DOMContentLoaded", function () {
  const background = document.querySelector(".background-slider");

  function updateBackground() {
    const timestamp = new Date().getTime(); // Ajoute un timestamp pour éviter le cache
    background.style.backgroundImage = `url('https://thispersondoesnotexist.com?${timestamp}')`;
  }

  updateBackground(); // Charge une première image

  setInterval(updateBackground, 5000); // Change toutes les 5 secondes
});
