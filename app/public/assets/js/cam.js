const startCamButton = document.getElementById('startCamButton');
const cameraContainer = document.getElementById('cameraContainer');
const snapButton = document.getElementById('snap');
const video = document.getElementById('video');
const capturedImage = document.getElementById('capturedImage');
const photoModal = document.getElementById('photoModal');
const discardButton = document.getElementById('discard');
const photoInput = document.getElementById('photocam');
let capturedImageData = null;
const cToiButton = document.getElementById('cToi');



const filterSelect = document.getElementById('filterSelect');
if (filterSelect) {
    filterSelect.addEventListener('change', () => {
        video.style.filter = filterSelect.value;
    });
}

// Appliquer les filtres Image
const imageFilterSelect = document.getElementById('imageFilterSelect');
const filterImage = document.getElementById('filterImage');
if (imageFilterSelect) {
    imageFilterSelect.addEventListener('change', () => {
    const filterValue = imageFilterSelect.value;
    if (filterValue === 'none') {
        filterImage.style.display = 'none';
    } else {
        filterImage.src = `../assets/img/filters/${filterValue}.png`;

        filterImage.style.display = 'block';
        filterImage.onload = () => {
            filterImage.style.width = canvas.Width + "px";
            filterImage.style.height = canvas.Height + "px";
        };
    }
    });
}

let mediaStream; // pour stocker la vidéo et l'audio
if (startCamButton) {
    startCamButton.addEventListener('click', () => {
        startCamButton.style.display = 'none';
        cameraContainer.style.display = 'block';
        
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(stream => {
                mediaStream = stream; // Stocke le flux avec vidéo et audio
                video.srcObject = mediaStream;
            })
            .catch(err => {
                console.error("Erreur d'accès à la webcam ou au microphone : ", err);
            });
    });
}

if (snapButton) {
    snapButton.addEventListener('click', () => {
    let canvas = document.getElementById('canvas');
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.id = 'canvas';
        canvas.width = 640;
        canvas.height = 480;
        canvas.style.position = 'absolute';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        canvas.style.display = 'none'; // Caché par défaut
        document.getElementById('cameraContainer').appendChild(canvas);
    }

    const context = canvas.getContext('2d');

    // Appliquer le filtre CSS seulement sur la vidéo
    context.filter = filterSelect.value;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Désactiver le filtre pour le filtre image
    context.filter = 'none';

    // Ajouter l’image du filtre par-dessus
    if (imageFilterSelect.value !== 'none') {
        const filterImg = new Image();
        filterImg.src = filterImage.src;
        filterImg.onload = () => {
            context.drawImage(filterImg, 0, 0, canvas.width, canvas.height);
            finalizeImage(canvas);
        };
    } else {
        finalizeImage(canvas);
    }
    });
}

//Converti l'image en base64 dans le format JPEG 
function finalizeImage(canvas) {
  capturedImageData = canvas.toDataURL('image/jpeg', 0.8);
  capturedImage.src = capturedImageData;
  photoModal.style.display = 'block';
  cameraContainer.style.display = 'none';
}

// Annuler la photo (retour à la caméra)
if (discardButton) {
    discardButton.addEventListener('click', () => {
        photoModal.style.display = 'none';
        cameraContainer.style.display = 'block';
    });
}

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
if (cToiButton) {
    cToiButton.addEventListener('click', () => {
    event.preventDefault();
    if (!capturedImageData) {
        console.error("Aucune image capturée !");
        return;
    }
    // Convertir l'image en fichier JPEG
    const blob = dataURItoBlob(capturedImageData);
    let fileName = Date.now() + '.jpeg';
    const file = new File([blob], fileName, { type: 'image/jpeg' });
    // Créer un objet FormData pour l'envoyer en POST
    const formData = new FormData();
    formData.append('file', file); // Correspond au $_FILES['file'] en PHP
    formData.append('type', 'photo');
    // Envoi via fetch() vers gallery.php
    console.log("Envoi de la requête...");
    fetch('gallery.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        window.location.reload();
    })
    .catch(error => {
        console.error("Erreur Fetch :", error);
    });
    photoModal.style.display = 'none';
    cameraContainer.style.display = 'none';
    });

    closeCamButton.addEventListener('click', () => {
    cameraContainer.style.display = 'none';
    startCamButton.style.display = 'block';
    video.srcObject.getTracks().forEach(track => track.stop());
    }
    );
}

const startRecordingButton = document.getElementById('startRecording');
const stopRecordingButton = document.getElementById('stopRecording');
const downloadLink = document.getElementById('downloadLink');
const recordedVideo = document.getElementById('recordedVideo');
let mediaRecorder;

if (startRecordingButton) {
    startRecordingButton.addEventListener('click', () => {
        if (!mediaStream) {
            console.error("Aucun flux disponible !");
            return;
        }

        recordedChunks = [];
        mediaRecorder = new MediaRecorder(mediaStream, { mimeType: 'video/webm' });

        mediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                recordedChunks.push(event.data);
            }
        };

        mediaRecorder.onstop = () => {
            const blob = new Blob(recordedChunks, { type: 'video/webm' });

            const formData = new FormData();
            formData.append('file', blob, 'video_' + Date.now() + '.webm');
            formData.append('uploadVideo', '1');
            formData.append('type', 'video');
            fetch('gallery.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                location.reload(); // Recharger la page pour afficher la vidéo
            })
            .catch(error => console.error('Erreur:', error));
        };

        mediaRecorder.start();
        startRecordingButton.disabled = true;
        stopRecordingButton.disabled = false;
    });
}

// Arrêter l'enregistrement
if (stopRecordingButton) {
    stopRecordingButton.addEventListener('click', () => {
        mediaRecorder.stop();
        startRecordingButton.disabled = false;
        stopRecordingButton.disabled = true;
    });
}
