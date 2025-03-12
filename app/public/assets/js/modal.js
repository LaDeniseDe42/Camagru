// Fonction pour ouvrir le modal
function openModal(infoType) {
  const body = document.body;
  let modalToOpen;

  const houseColors = {
    Poufsouffle: { bg: "#ffdb58", text: "#372e29" },
    Serpentard: { bg: "#1a472a", text: "#aaaaaa" },
    Gryffondor: { bg: "#7f0909", text: "#d3a625" },
    Serdaigle: { bg: "#0e1a40", text: "#946b2d" },
    Moldu: { bg: "#3d3d3d", text: "#ff6600" },
    Crakmol: { bg: "#4b0082", text: "#c0c0c0" },
  };
  // Fonction pour détecter la maison actuelle
  function detectHouse() {
    for (let house in houseColors) {
      if (body.classList.contains(house)) {
        return house;
      }
    }
    return "Moldu";
  }
  let house = detectHouse();
  modalToOpen = document.getElementById(infoType);

  if (houseColors[house]) {
    modalToOpen.querySelector(".modal-content").style.backgroundColor =
      houseColors[house].bg;
    modalToOpen.querySelector(".modal-content").style.color =
      houseColors[house].text;
  }
  modalToOpen.style.display = "flex";
  // Fonction pour fermer le modal
  function closeModal() {
    modalToOpen.style.display = "none";
  }

  // Ferme le modal si on clique en dehors
  window.onclick = function (event) {
    if (event.target === modalToOpen) {
      closeModal();
    }
  };
  window.closeModal = closeModal;
}
