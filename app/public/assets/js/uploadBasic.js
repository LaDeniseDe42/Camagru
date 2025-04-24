const fileInput = document.getElementById('fileInput');
const previewContainer = document.getElementById('previewContainer');
const canvas = document.getElementById('uploadCanvas');
const ctx = canvas.getContext('2d');
const uploadFilterSelect = document.getElementById('uploadFilterSelect');
const uploadFilteredImage = document.getElementById('uploadFilteredImage');
const cancelButtonU = document.getElementById('cancelUploadBtn');
let loadedImage = new Image();

fileInput.addEventListener('change', (event) => {
  const file = event.target.files[0];
  if (!file || !file.type.startsWith('image/')) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    loadedImage.onload = () => {
      canvas.width = loadedImage.width;
      canvas.height = loadedImage.height;
      ctx.filter = uploadFilterSelect.value || 'none';
      ctx.drawImage(loadedImage, 0, 0);
    };
    loadedImage.src = e.target.result;
    previewContainer.style.display = 'block';
  };
  reader.readAsDataURL(file);
});

uploadFilterSelect.addEventListener('change', () => {
  if (!loadedImage.src) return;
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.filter = uploadFilterSelect.value;
  ctx.drawImage(loadedImage, 0, 0);
});

uploadFilteredImage.addEventListener('click', () => {
  const filteredDataUrl = canvas.toDataURL('image/jpeg', 0.8);
  const blob = dataURItoBlob(filteredDataUrl);
  const file = new File([blob], Date.now() + ".jpeg", { type: "image/jpeg" });

  const formData = new FormData();
  formData.append("file", file);
  formData.append("type", "photo");

  fetch('gallery.php', {
    method: 'POST',
    body: formData,
  })
    .then((res) => res.text())
    .then(() => {
      location.reload();
    })
    .catch(err => {
      console.error("Erreur d'upload image filtrée :", err);
      alert("Erreur lors de l’envoi.");
    });
});

function dataURItoBlob(dataURI) {
  const byteString = atob(dataURI.split(',')[1]);
  const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
  const arrayBuffer = new ArrayBuffer(byteString.length);
  const intArray = new Uint8Array(arrayBuffer);
  for (let i = 0; i < byteString.length; i++) {
    intArray[i] = byteString.charCodeAt(i);
  }
  return new Blob([intArray], { type: mimeString });
}


cancelButtonU.addEventListener('click', () => {
  fileInput.value = '';
  previewContainer.style.display = 'none';
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  loadedImage.src = '';
}
);