function getDisplayedPublicationIds() {
  const publicationElements = document.querySelectorAll('.publication[data-id]');
  const ids = Array.from(publicationElements).map(el => el.dataset.id);
  return ids;
}

// 💡 Cette fonction ajoute les publications reçues au DOM
function appendPublicationsToDOM(publications) {
  const container = document.querySelector('.publications');

  publications.forEach(pub => {
    if (document.querySelector(`.publication[data-id="${pub.id}"]`)) return;

    const pubDiv = document.createElement('div');
    pubDiv.classList.add('publication');
    pubDiv.setAttribute('data-id', pub.id);

    let mediaContent = '';
    if (pub.type === 'photo') {
      mediaContent = `
        <a href="media.php?user=${pub.user_id}&file=${encodeURIComponent(pub.filepath)}">
          <img src="${pub.filepath}" alt="${pub.filename}" style="width:100%">
        </a>`;
    } else {
      mediaContent = `
        <a href="media.php?user=${pub.user_id}&file=${encodeURIComponent(pub.filepath)}">
          <video width="100%" controls>
            <source src="${pub.filepath}" type="video/webm">
          </video>
        </a>`;
    }

    pubDiv.innerHTML = `
      ${mediaContent}
      <a href="gallery.php?user=${pub.user_id}">
        <p>Posté par : ${pub.username}</p>
      </a>
      <p>Le : ${pub.uploaded_at}</p>
    `;

    container.appendChild(pubDiv);
  });
}

document.getElementById('loadMoreBtn').addEventListener('click', () => {
  const existingIds = getDisplayedPublicationIds();

  fetch(window.location.href, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: 'load_more',
        existingIds: existingIds
      })
  })
  .then(res => res.text()) // TEMPORAIRE pour voir la réponse brute
.then(text => {
    console.log("Réponse brute:", text); // 🔥
    const data = JSON.parse(text); // ici, tu verras ce qui bloque exactement
    appendPublicationsToDOM(data);
})
});
