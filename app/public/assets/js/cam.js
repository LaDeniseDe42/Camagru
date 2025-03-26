const startCamButton = document.getElementById('startCamButton');
const cameraContainer = document.getElementById('cameraContainer');
const snapButton = document.getElementById('snap');
const video = document.getElementById('video');
const capturedImage = document.getElementById('capturedImage');
const photoModal = document.getElementById('photoModal');
const discardButton = document.getElementById('discard');
const photoInput = document.getElementById('photocam');
let capturedImageData = null;  // Déclare la variable globalement
const cToiButton = document.getElementById('cToi');

// Démarrer la caméra quand l'utilisateur clique sur le bouton
startCamButton.addEventListener('click', () => {
    startCamButton.style.display = 'none'; // Cacher le bouton "Prendre une photo"
    cameraContainer.style.display = 'block'; // Afficher la caméra
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            console.error("Erreur d'accès à la webcam : ", err);
        });
});

snapButton.addEventListener('click', () => {
  const canvas = document.createElement('canvas');
  canvas.width = 640;
  canvas.height = 480;
  const context = canvas.getContext('2d');
  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  // Convertir l'image en base64 dans le format JPEG ou PNG selon le besoin
  const format = 'image/jpeg'; // Changez ici pour 'image/png' si vous préférez PNG
  const imageQuality = 0.8; // Qualité de l'image pour le JPEG (0.0 à 1.0)
  capturedImageData = canvas.toDataURL(format, imageQuality); // Format et qualité

  // Nettoyer la chaîne base64 pour enlever le préfixe data:image/jpeg;base64,
  const base64Image = capturedImageData.replace(/^data:image\/(jpeg|png|gif);base64,/, '');

  // Vérifier que l'image est capturée et afficher l'image dans le modal
  capturedImage.src = capturedImageData;
  photoInput.src = capturedImageData; // Mettre l'image dans le champ caché
  photoModal.style.display = 'block'; // Afficher le modal

  // Cacher la caméra
  cameraContainer.style.display = 'none';
});

// Annuler la photo (retour à la caméra)
discardButton.addEventListener('click', () => {
    photoModal.style.display = 'none';
    cameraContainer.style.display = 'block';
});

// Fonction pour convertir une image Base64 en un objet Blob
function dataURItoBlob(dataURI) {
  const byteString = atob(dataURI.split(',')[1]); // Décoder base64
  const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]; // Extraire le type MIME

  const arrayBuffer = new ArrayBuffer(byteString.length);
  const intArray = new Uint8Array(arrayBuffer);

  for (let i = 0; i < byteString.length; i++) {
      intArray[i] = byteString.charCodeAt(i);
  }

  return new Blob([intArray], { type: mimeString });
}

// Ajouter un écouteur d'événement sur le bouton "cToiButton"
cToiButton.addEventListener('click', () => {
  if (!capturedImageData) {
      console.error("Aucune image capturée !");
      return;
  }
  // Convertir l'image en fichier JPEG
  const blob = dataURItoBlob(capturedImageData);
  let fileName = Date.now() + '.jpeg';
  const file = new File([blob], fileName, { type: 'image/jpeg' });
  console.log("Nom du fichier:", file.name);
console.log("Type du fichier:", file.type);
console.log("Taille du fichier:", file.size, "bytes");

  // Créer un objet FormData pour l'envoyer en POST
  const formData = new FormData();
  formData.append('file', file); // Correspond au $_FILES['file'] en PHP

  // Envoi via fetch() vers gallery.php
  fetch('gallery.php', {
      method: 'POST',
      body: formData,
  })
  .catch(error => {
    console.error("Erreur Fetch :", error);
});
});
