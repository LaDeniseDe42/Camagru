const fileInput = document.getElementById("fileInput");
const previewContainer = document.getElementById("previewContainer");
const canvas = document.getElementById("uploadCanvas");
const ctx = canvas.getContext("2d");
const uploadFilterSelect = document.getElementById("uploadFilterSelect");
const uploadFilteredImage = document.getElementById("uploadFilteredImage");
const cancelButtonU = document.getElementById("cancelUploadBtn");
let loadedImage = new Image();

fileInput.addEventListener("change", (event) => {
  const file = event.target.files[0];
  if (!file || !file.type.startsWith("image/")) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    loadedImage.onload = () => {
      canvas.width = loadedImage.width;
      canvas.height = loadedImage.height;
      ctx.filter = uploadFilterSelect.value || "none";
      ctx.drawImage(loadedImage, 0, 0);
    };
    loadedImage.src = e.target.result;
    previewContainer.style.display = "block";
  };
  reader.readAsDataURL(file);
});

uploadFilterSelect.addEventListener("change", () => {
  if (!loadedImage.src) return;
  redrawCanvas();
});

uploadFilteredImage.addEventListener("click", () => {
  const filteredDataUrl = canvas.toDataURL("image/jpeg", 0.8);
  const blob = dataURItoBlob(filteredDataUrl);
  const file = new File([blob], Date.now() + ".jpeg", { type: "image/jpeg" });

  const formData = new FormData();
  formData.append("file", file);
  formData.append("type", "photo");

  fetch("gallery.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.text())
    .then(() => {
      location.reload();
    })
    .catch((err) => {
      console.error("Erreur d'upload image filtrée :", err);
      alert("Erreur lors de l’envoi.");
    });
});

function dataURItoBlob(dataURI) {
  const byteString = atob(dataURI.split(",")[1]);
  const mimeString = dataURI.split(",")[0].split(":")[1].split(";")[0];
  const arrayBuffer = new ArrayBuffer(byteString.length);
  const intArray = new Uint8Array(arrayBuffer);
  for (let i = 0; i < byteString.length; i++) {
    intArray[i] = byteString.charCodeAt(i);
  }
  return new Blob([intArray], { type: mimeString });
}

cancelButtonU.addEventListener("click", () => {
  fileInput.value = "";
  previewContainer.style.display = "none";
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  loadedImage.src = "";
  placedStickers = []; // Réinitialiser la liste des stickers
  redrawCanvas(); // Réafficher l'image sans stickers
});

// Tous les stickers posés sur le canvas
let placedStickers = [];

// Position/dimensions du canvas (calculé au clic)
let canvasRect = null;

// Index du sticker actuellement déplacé (si on est en train de le bouger)
let draggedStickerIndex = null;

// Décalage de l’image dans le canvas (centrée)
let imageOffset = { x: 0, y: 0 };

// Dimensions affichées de l’image (en fonction du ratio)
let imageDrawSize = { width: 0, height: 0 };

// Lorsqu'on clique sur un sticker de la liste => on l’ajoute centré sur le canvas
document.querySelectorAll(".sticker").forEach((sticker) => {
  sticker.addEventListener("click", () => {
    canvasRect = uploadCanvas.getBoundingClientRect();

    const centerX = uploadCanvas.width / 2;
    const centerY = uploadCanvas.height / 2;

    const newSticker = {
      image: sticker,
      width: 80,
      height: 80,
      x: centerX - 40,
      y: centerY - 40,
    };

    placedStickers.push(newSticker);
    redrawCanvas();
  });
});

// Lorsqu'on clique sur le canvas, on vérifie si on clique sur un sticker existant
uploadCanvas.addEventListener("mousedown", (e) => {
  canvasRect = uploadCanvas.getBoundingClientRect();

  const scaleX = uploadCanvas.width / canvasRect.width;
  const scaleY = uploadCanvas.height / canvasRect.height;

  const mouseX = (e.clientX - canvasRect.left) * scaleX;
  const mouseY = (e.clientY - canvasRect.top) * scaleY;

  // Vérifie si la souris est dans les bornes d’un sticker
  for (let i = placedStickers.length - 1; i >= 0; i--) {
    const sticker = placedStickers[i];
    if (
      mouseX >= sticker.x &&
      mouseX <= sticker.x + sticker.width &&
      mouseY >= sticker.y &&
      mouseY <= sticker.y + sticker.height
    ) {
      draggedStickerIndex = i;
      document.addEventListener("mousemove", dragSticker);
      document.addEventListener("mouseup", stopDraggingSticker);
      document.addEventListener("wheel", resizeSticker); // Ajout de l'écouteur de la molette
      document.addEventListener("keydown", (event) => {
        if (event.key === "ArrowUp") {
          turnSticker(event);
        }
      });
      return;
    }
  }
});

// Déplacement du sticker sélectionné
function dragSticker(e) {
  if (draggedStickerIndex === null) return;

  const scaleX = uploadCanvas.width / canvasRect.width;
  const scaleY = uploadCanvas.height / canvasRect.height;

  const mouseX = (e.clientX - canvasRect.left) * scaleX;
  const mouseY = (e.clientY - canvasRect.top) * scaleY;

  const sticker = placedStickers[draggedStickerIndex];
  sticker.x = mouseX - sticker.width / 2;
  sticker.y = mouseY - sticker.height / 2;

  redrawCanvas();
}

// Fonction pour redimensionner le sticker avec la molette
function resizeSticker(e) {
  if (draggedStickerIndex === null) return;

  // La molette de souris génère des valeurs positives ou négatives
  const zoomSpeed = 0.05;
  const sticker = placedStickers[draggedStickerIndex];
  if (e.deltaY < 0) {
    // Molette vers le haut : agrandir
    sticker.width += sticker.width * zoomSpeed;
    sticker.height += sticker.height * zoomSpeed;
  } else {
    // Molette vers le bas : réduire
    sticker.width -= sticker.width * zoomSpeed;
    sticker.height -= sticker.height * zoomSpeed;
  }

  // Empêcher un agrandissement ou une réduction trop importante
  sticker.width = Math.max(20, Math.min(sticker.width, uploadCanvas.width));
  sticker.height = Math.max(20, Math.min(sticker.height, uploadCanvas.height));

  redrawCanvas();
}

// Fonction pour faire pivoter le sticker avec un upArrow
function turnSticker(e) {
  if (draggedStickerIndex === null) return;

  e.preventDefault();

  const sticker = placedStickers[draggedStickerIndex];
  sticker.rotation = (sticker.rotation || 0) + 30;
  if (sticker.rotation >= 360) {
    sticker.rotation = 0;
  }

  redrawCanvas();
}

// Fin du drag
function stopDraggingSticker() {
  draggedStickerIndex = null;
  document.removeEventListener("mousemove", dragSticker);
  document.removeEventListener("mouseup", stopDraggingSticker);
  document.removeEventListener("wheel", resizeSticker); // Retirer l'écouteur de la molette
  document.removeEventListener("rightclick", turnSticker);
}

// Redessine tout le canvas : image + tous les stickers
function redrawCanvas() {
  ctx.clearRect(0, 0, uploadCanvas.width, uploadCanvas.height);
  ctx.filter = uploadFilterSelect.value;

  const ratio = Math.min(
    uploadCanvas.width / loadedImage.width,
    uploadCanvas.height / loadedImage.height
  );

  imageDrawSize.width = loadedImage.width * ratio;
  imageDrawSize.height = loadedImage.height * ratio;

  imageOffset.x = (uploadCanvas.width - imageDrawSize.width) / 2;
  imageOffset.y = (uploadCanvas.height - imageDrawSize.height) / 2;

  ctx.drawImage(
    loadedImage,
    imageOffset.x,
    imageOffset.y,
    imageDrawSize.width,
    imageDrawSize.height
  );

  placedStickers.forEach(drawSticker);
}

// Dessine un sticker
function drawSticker(sticker) {
  // Dessine simplement l’image du sticker
  ctx.save();
  ctx.translate(sticker.x + sticker.width / 2, sticker.y + sticker.height / 2);
  ctx.rotate((sticker.rotation || 0) * (Math.PI / 180));
  ctx.drawImage(
    sticker.image,
    -sticker.width / 2,
    -sticker.height / 2,
    sticker.width,
    sticker.height
  );
  ctx.restore();
}
