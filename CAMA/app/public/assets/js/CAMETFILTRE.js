// // Récupérer les éléments DOM nécessaires
// const elements = {
//   startCamButton: document.getElementById("startCamButton"),
//   cameraContainer: document.getElementById("cameraContainer"),
//   snapButton: document.getElementById("snap"),
//   video: document.getElementById("video"),
//   capturedImage: document.getElementById("capturedImage"),
//   photoModal: document.getElementById("photoModal"),
//   discardButton: document.getElementById("discard"),
//   filterSelect: document.getElementById("filterSelect"),
//   imageFilterSelect: document.getElementById("imageFilterSelect"),
//   filterImage: document.getElementById("filterImage"),
//   startRecordingButton: document.getElementById("startRecording"),
//   stopRecordingButton: document.getElementById("stopRecording"),
//   previewVideo: document.getElementById("previewVideo"),
//   previewControls: document.getElementById("previewControls"),
//   publishButton: document.getElementById("publishButton"),
//   cancelButton: document.getElementById("cancelButton"),
//   cToiButton: document.getElementById("cToi"),
//   closeCamButton: document.getElementById("closeCamButton"),
// };

// // Variables globales
// let mediaStream;
// let mediaRecorder;
// let recordedChunks = [];
// let stopRecordingTimeoutId;
// let capturedImageData = null;

// // Désactiver les filtres quand la vidéo est en cours d'enregistrement
// function startRecordingVideo() {
//   if (!mediaStream) {
//     console.error("Aucun flux vidéo disponible !");
//     return;
//   }

//   // Cacher filtres et boutons inutiles
//   elements.filterImage.style.display = "none";
//   elements.video.style.filter = "none";
//   elements.filterSelect.style.display = "none";
//   elements.imageFilterSelect.style.display = "none";
//   elements.snapButton.style.display = "none";

//   recordedChunks = [];
//   mediaRecorder = new MediaRecorder(mediaStream, { mimeType: "video/webm" });

//   mediaRecorder.ondataavailable = (event) => {
//     if (event.data.size > 0) {
//       recordedChunks.push(event.data);
//     }
//   };

//   mediaRecorder.onstop = () => {
//     restoreUIAfterRecording();

//     const blob = new Blob(recordedChunks, { type: "video/webm" });
//     const videoURL = URL.createObjectURL(blob);

//     elements.previewVideo.src = videoURL;
//     elements.previewVideo.style.display = "block";
//     elements.previewVideo.blobToUpload = blob;
//     elements.previewControls.classList.add("show");
//   };

//   mediaRecorder.start();

//   elements.startRecordingButton.disabled = true;
//   elements.stopRecordingButton.disabled = false;

//   stopRecordingTimeoutId = setTimeout(() => {
//     if (mediaRecorder.state === "recording") {
//       mediaRecorder.stop();
//     }
//   }, 6000);
// }

// // Fonction pour arrêter l'enregistrement vidéo
// function stopRecordingVideo() {
//   if (mediaRecorder && mediaRecorder.state === "recording") {
//     mediaRecorder.stop();
//   }

//   elements.startRecordingButton.disabled = false;
//   elements.stopRecordingButton.disabled = true;

//   if (stopRecordingTimeoutId) {
//     clearTimeout(stopRecordingTimeoutId);
//     stopRecordingTimeoutId = null;
//   }
// }

// // Restaurer l'UI après l'enregistrement vidéo
// function restoreUIAfterRecording() {
//   elements.video.style.filter = elements.filterSelect.value;
//   elements.filterImage.style.display =
//     elements.imageFilterSelect.value === "none" ? "none" : "block";
//   elements.filterSelect.style.display = "block";
//   elements.imageFilterSelect.style.display = "block";
//   elements.snapButton.style.display = "block";
// }

// // Prendre une photo avec les filtres appliqués
// function takeSnapshot() {
//   let canvas = document.getElementById("canvas");
//   if (!canvas) {
//     canvas = document.createElement("canvas");
//     canvas.id = "canvas";
//     canvas.width = 640;
//     canvas.height = 480;
//     canvas.style.position = "absolute";
//     canvas.style.top = "0";
//     canvas.style.left = "0";
//     canvas.style.width = "100%";
//     canvas.style.height = "100%";
//     canvas.style.display = "none"; // Caché par défaut
//     document.getElementById("cameraContainer").appendChild(canvas);
//   }

//   const context = canvas.getContext("2d");

//   // Appliquer le filtre CSS seulement sur la vidéo
//   context.filter = elements.filterSelect.value;
//   context.drawImage(elements.video, 0, 0, canvas.width, canvas.height);

//   // Désactiver le filtre pour le filtre image
//   context.filter = "none";

//   // Ajouter l’image du filtre par-dessus
//   if (elements.imageFilterSelect.value !== "none") {
//     const filterImg = new Image();
//     filterImg.src = elements.filterImage.src;
//     filterImg.onload = () => {
//       context.drawImage(filterImg, 0, 0, canvas.width, canvas.height);
//       finalizeImage(canvas);
//     };
//   } else {
//     finalizeImage(canvas);
//   }
// }

// // Finaliser l'image capturée
// function finalizeImage(canvas) {
//   capturedImageData = canvas.toDataURL("image/jpeg", 0.8);
//   elements.capturedImage.src = capturedImageData;
//   elements.photoModal.style.display = "block";
//   elements.cameraContainer.style.display = "none";
// }

// // Convertir une image base64 en Blob
// function dataURItoBlob(dataURI) {
//   const byteString = atob(dataURI.split(",")[1]);
//   const mimeString = dataURI.split(",")[0].split(":")[1].split(";")[0];
//   const arrayBuffer = new ArrayBuffer(byteString.length);
//   const intArray = new Uint8Array(arrayBuffer);

//   for (let i = 0; i < byteString.length; i++) {
//     intArray[i] = byteString.charCodeAt(i);
//   }

//   return new Blob([intArray], { type: mimeString });
// }

// // Ajouter un écouteur d'événements pour prendre la photo
// elements.snapButton.addEventListener("click", takeSnapshot);

// // Ajouter un écouteur d'événements pour annuler la photo
// elements.discardButton.addEventListener("click", () => {
//   elements.photoModal.style.display = "none";
//   elements.cameraContainer.style.display = "block";
// });

// // Ajouter un écouteur d'événements pour démarrer la caméra
// elements.startCamButton.addEventListener("click", () => {
//   elements.startCamButton.style.display = "none";
//   elements.cameraContainer.style.display = "block";

//   navigator.mediaDevices
//     .getUserMedia({ video: true, audio: true })
//     .then((stream) => {
//       mediaStream = stream;
//       elements.video.srcObject = mediaStream;
//     })
//     .catch((err) => {
//       console.error("Erreur d'accès à la webcam : ", err);
//     });
// });

// // Enregistrer la photo
// elements.cToiButton.addEventListener("click", () => {
//   if (!capturedImageData) {
//     console.error("Aucune image capturée !");
//     return;
//   }

//   const blob = dataURItoBlob(capturedImageData);
//   let fileName = Date.now() + ".jpeg";
//   const file = new File([blob], fileName, { type: "image/jpeg" });

//   const formData = new FormData();
//   formData.append("file", file);
//   formData.append("type", "photo");

//   fetch("gallery.php", {
//     method: "POST",
//     body: formData,
//   })
//     .then((response) => window.location.reload())
//     .catch((error) => console.error("Erreur d'envoi photo :", error));

//   elements.photoModal.style.display = "none";
//   elements.cameraContainer.style.display = "none";
// });

// // Ajouter un écouteur pour fermer la caméra
// elements.closeCamButton.addEventListener("click", () => {
//   elements.cameraContainer.style.display = "none";
//   elements.startCamButton.style.display = "block";
//   mediaStream.getTracks().forEach((track) => track.stop());
// });

// // Ajouter un écouteur d'événements pour la publication vidéo
// elements.publishButton.addEventListener("click", () => {
//   const blob = elements.previewVideo.blobToUpload;
//   const formData = new FormData();
//   formData.append("file", blob, "video_" + Date.now() + ".webm");
//   formData.append("uploadVideo", "1");
//   formData.append("type", "video");

//   fetch("gallery.php", {
//     method: "POST",
//     body: formData,
//   })
//     .then((response) => location.reload())
//     .catch((error) => console.error("Erreur d'envoi vidéo :", error));
// });

// // Annuler la vidéo et réinitialiser l'UI
// elements.cancelButton.addEventListener("click", () => {
//   elements.previewVideo.pause();
//   elements.previewVideo.removeAttribute("src");
//   elements.previewVideo.load();

//   elements.previewControls.classList.remove("show");
//   recordedChunks = [];
//   mediaRecorder = null;
// });
