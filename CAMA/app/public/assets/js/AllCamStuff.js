// Récupération des éléments DOM
const startCamButton = document.getElementById("startCamButton");
const cameraContainer = document.getElementById("cameraContainer");
const snapButton = document.getElementById("snap");
const video = document.getElementById("video");
const capturedImage = document.getElementById("capturedImage");
const photoModal = document.getElementById("photoModal");
const discardButton = document.getElementById("discard");
const cToiButton = document.getElementById("cToi");
const cssFilterSelect = document.getElementById("filterSelect");
const imageFilterSelect = document.getElementById("imageFilterSelect");
const filterImage = document.getElementById("filterImage");
const stickerSelect = document.getElementById("filterStickersSelect");
const filterStickImage = document.getElementById("filterStickImage");
const startRecordingButton = document.getElementById("startRecording");
const stopRecordingButton = document.getElementById("stopRecording");
const publishButton = document.getElementById("publishButton");
const cancelButton = document.getElementById("cancelButton");
const closeCamButton = document.getElementById("closeCamButton");

// Variables globales
let capturedImageData = null;
let mediaStream;
let mediaRecorder;
let recordedChunks = [];
let stopRecordingTimeoutId;
let isDragging = false;
let offset = { x: 0, y: 0 };

// Fonctionnalités des filtres CSS
if (cssFilterSelect) {
  cssFilterSelect.addEventListener("change", () => {
    video.style.filter = cssFilterSelect.value;
  });
}

// Application des filtres d'images
if (imageFilterSelect) {
  imageFilterSelect.addEventListener("change", () => {
    const filterValue = imageFilterSelect.value;
    if (filterValue === "none") {
      filterImage.style.display = "none";
    } else {
      filterImage.src = `../assets/img/filters/${filterValue}.png`;
      filterImage.style.display = "block";
      filterImage.onload = () => {
        filterImage.style.width = video.videoWidth + "px";
        filterImage.style.height = video.videoHeight + "px";
      };
    }
  });
}

// Application des stickers
if (stickerSelect) {
  stickerSelect.addEventListener("change", () => {
    const value = stickerSelect.value;
    if (value === "none") {
      filterStickImage.style.display = "none";
      filterStickImage.src = "";
    } else {
      filterStickImage.src = `../assets/img/filters/${value}.png`;
      filterStickImage.style.display = "block";
    }
  });
}

//Gestion du déplacement des stickers
if (filterStickImage) {
  filterStickImage.addEventListener("mousedown", (e) => {
    isDragging = true;
    const rect = filterStickImage.getBoundingClientRect();
    offset.x = e.clientX - rect.left;
    offset.y = e.clientY - rect.top;
    const container = document.getElementById("videoHide");

    const onMouseMove = (e) => {
      if (!isDragging) return;
      const containerRect = container.getBoundingClientRect();
      const stickerWidth = filterStickImage.offsetWidth;
      const stickerHeight = filterStickImage.offsetHeight;

      let left = e.clientX - containerRect.left - offset.x;
      let top = e.clientY - containerRect.top - offset.y;

      const minVisible = 30;
      left = Math.max(
        -stickerWidth + minVisible,
        Math.min(left, container.clientWidth - minVisible)
      );
      top = Math.max(
        -stickerHeight + minVisible,
        Math.min(top, container.clientHeight - minVisible)
      );

      filterStickImage.style.left = left + "px";
      filterStickImage.style.top = top + "px";
    };

    const onMouseUp = () => {
      isDragging = false;
      document.removeEventListener("mousemove", onMouseMove);
      document.removeEventListener("mouseup", onMouseUp);
    };

    document.addEventListener("mousemove", onMouseMove);
    document.addEventListener("mouseup", onMouseUp);
  });
}

if (snapButton) {
  snapButton.addEventListener("click", () => {
    let canvas = document.getElementById("canvas");
    if (!canvas) {
      canvas = document.createElement("canvas");
      canvas.id = "canvas";
      canvas.width = 640;
      canvas.height = 480;
      canvas.style.position = "absolute";
      canvas.style.top = "0";
      canvas.style.left = "0";
      canvas.style.width = "100%";
      canvas.style.height = "100%";
      canvas.style.display = "none";
      document.getElementById("cameraContainer").appendChild(canvas);
    }

    const context = canvas.getContext("2d");

    //Appliquer le filtre CSS
    context.filter = cssFilterSelect.value || "none";
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    //Ajouter le filtre image
    context.filter = "none";
    const filterImageValue = imageFilterSelect.value;
    const stickerValue = filterStickersSelect.value;

    let imagesToLoad = [];

    if (filterImageValue !== "none") {
      const filterImg = new Image();
      filterImg.src = `../assets/img/filters/${filterImageValue}.png`;
      imagesToLoad.push(filterImg);
    }

    const drawStickerImage = () => {
      return new Promise((resolve) => {
        if (filterStickImage.src && filterStickImage.style.display !== "none") {
          const stickerImg = new Image();
          stickerImg.src = filterStickImage.src;

          const videoRect = video.getBoundingClientRect();
          const stickerRect = filterStickImage.getBoundingClientRect();

          // Calculer la position relative au canvas (mêmes dimensions que la vidéo)
          const relativeX = stickerRect.left - videoRect.left;
          const relativeY = stickerRect.top - videoRect.top;

          stickerImg.onload = () => {
            context.drawImage(
              stickerImg,
              relativeX,
              relativeY,
              filterStickImage.width,
              filterStickImage.height
            );
            resolve();
          };
        } else {
          resolve();
        }
      });
    };
    let loadedCount = 0;
    if (imagesToLoad.length > 0) {
      imagesToLoad.forEach((img) => {
        img.onload = () => {
          loadedCount++;
          if (loadedCount === imagesToLoad.length) {
            imagesToLoad.forEach((img) => {
              context.drawImage(img, 0, 0, canvas.width, canvas.height);
            });

            drawStickerImage().then(() => {
              finalizeImage(canvas);
            });
          }
        };
      });
    } else {
      // Aucun filtre image ni sticker, on passe directement à la finalisation
      drawStickerImage().then(() => {
        finalizeImage(canvas);
      });
    }
  });
}

//Démarrer la caméra
if (startCamButton) {
  startCamButton.addEventListener("click", () => {
    startCamButton.style.display = "none";
    cameraContainer.style.display = "block";
    if (startRecordingButton.disabled === true) {
      startRecordingButton.disabled = false;
    }
    navigator.mediaDevices
      .getUserMedia({ video: true, audio: true })
      .then((stream) => {
        mediaStream = stream;
        video.srcObject = mediaStream;
      })
      .catch((err) => {
        cameraContainer.style.display = "none";
        startCamButton.style.display = "block";
        alert("Pad de webcam ou de microphone détecté !", err);
      });
  });
}

// Finaliser l'image et l'afficher dans la modale
function finalizeImage(canvas) {
  capturedImageData = canvas.toDataURL("image/jpeg", 0.8);
  capturedImage.src = capturedImageData;
  photoModal.style.display = "block";
  cameraContainer.style.display = "none";
}

// Annuler la photo
if (discardButton) {
  discardButton.addEventListener("click", () => {
    photoModal.style.display = "none";
    cameraContainer.style.display = "block";
  });
}

// Sauvegarder l'image en base64 et l'envoyer au serveur
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

if (cToiButton) {
  cToiButton.addEventListener("click", (event) => {
    event.preventDefault();
    if (!capturedImageData) {
      console.error("Aucune image capturée !");
      return;
    }
    const blob = dataURItoBlob(capturedImageData);
    let fileName = Date.now() + ".jpeg";
    const file = new File([blob], fileName, { type: "image/jpeg" });
    const formData = new FormData();
    formData.append("file", file);
    formData.append("type", "photo");
    fetch("gallery.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => window.location.reload())
      .catch((error) => console.error("Erreur Fetch :", error));
    photoModal.style.display = "none";
    cameraContainer.style.display = "none";
  });
}

// Fermer la caméra
if (closeCamButton) {
  closeCamButton.addEventListener("click", () => {
    cameraContainer.style.display = "none";
    startCamButton.style.display = "block";
    video.srcObject.getTracks().forEach((track) => track.stop());
  });
}

if (publishButton) {
  publishButton.addEventListener("click", () => {
    const blob = document.getElementById("previewVideo").blobToUpload;

    const formData = new FormData();
    formData.append("file", blob, "video_" + Date.now() + ".webm");
    formData.append("uploadVideo", "1");
    formData.append("type", "video");

    fetch("gallery.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((data) => {
        location.reload();
      })
      .catch((error) => {
        console.error("Erreur envoi vidéo :", error);
      });
  });
}

//////////////////////VIDEO///////////////////////

// Démarrer l'enregistrement vidéo
if (startRecordingButton) {
  startRecordingButton.addEventListener("click", () => {
    if (!mediaStream) {
      console.error("Aucun flux disponible !");
      return;
    }
    filterImage.style.display = "none";
    video.style.filter = "none";
    filterSelect.style.display = "none";
    imageFilterSelect.style.display = "none";

    filterStickersSelect.style.display = "none";
    filterStickImage.style.display = "none";

    recordedChunks = [];
    mediaRecorder = new MediaRecorder(mediaStream, { mimeType: "video/webm" });

    mediaRecorder.ondataavailable = (event) => {
      if (event.data.size > 0) {
        recordedChunks.push(event.data);
      }
    };
    mediaRecorder.onstop = () => {
      const blob = new Blob(recordedChunks, { type: "video/webm" });
      const videoURL = URL.createObjectURL(blob);
      const previewVideo = document.getElementById("previewVideo");
      const previewControls = document.getElementById("previewControls");
      previewControls.classList.add("show");

      previewVideo.src = videoURL;
      previewVideo.style.display = "block";
      previewVideo.blobToUpload = blob;
    };

    mediaRecorder.start();
    startRecordingButton.disabled = true;
    stopRecordingButton.disabled = false;

    stopRecordingTimeoutId = setTimeout(() => {
      if (mediaRecorder.state === "recording") {
        mediaRecorder.stop();
        startRecordingButton.disabled = false;
        stopRecordingButton.disabled = true;
      }
    }, 6000);
  });
}

// Arrêter l'enregistrement vidéo
if (stopRecordingButton) {
  stopRecordingButton.addEventListener("click", () => {
    mediaRecorder.stop();
    clearTimeout(stopRecordingTimeoutId);
    startRecordingButton.disabled = false;
    stopRecordingButton.disabled = true;
  });
}

//Annuler la vidéo
cancelButton.addEventListener("click", () => {
  const previewVideo = document.getElementById("previewVideo");
  const previewControls = document.getElementById("previewControls");

  previewVideo.pause();
  previewVideo.removeAttribute("src");
  previewVideo.load();

  previewControls.classList.remove("show");
  recordedChunks = [];
  mediaRecorder = null;
  filterImage.style.display = "block";
  video.style.filter = cssFilterSelect.value;
  filterSelect.style.display = "block";
  imageFilterSelect.style.display = "block";
  filterStickersSelect.style.display = "block";
  filterStickImage.style.display = "block";
});
