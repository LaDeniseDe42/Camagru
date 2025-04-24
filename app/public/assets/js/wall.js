//Pour recuperer les id de pub deja affichees
function getDisplayedPublicationIds() {
  const publicationElements = document.querySelectorAll('.publication[data-id]');
  const ids = Array.from(publicationElements).map(el => el.dataset.id);
  return ids;
}

// Ajoute les publications reçues au DOM
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
          <img src="${pub.filepath}" alt="${pub.filename}" style="width:75%">
        </a>`;
    } else {
      mediaContent = `
        <a href="media.php?user=${pub.user_id}&file=${encodeURIComponent(pub.filepath)}">
          <video width="75%" controls>
            <source src="${pub.filepath}" type="video/webm">
          </video>
        </a>`;
    }

    pubDiv.innerHTML = `
    ${mediaContent}
      <div class="reaction-buttons" data-id="${pub.id}">
          <button class="like-button ${pub.userReaction === 'like' ? 'active' : ''}">👍 ${pub.nb_likes}</button>
          <button class="dislike-button ${pub.userReaction === 'dislike' ? 'active' : ''}">👎 ${pub.nb_dislikes}</button>
      </div>
      <a href="gallery.php?user=${pub.user_id}">
        <p>Posté par : ${pub.username}</p>
      </a>
      <p>Le : ${pub.uploaded_at}</p>
    `;

    container.appendChild(pubDiv);
  });
}

if (document.getElementById('loadMoreBtn')) {
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
      .then(res => res.json())
      .then(data => {
        appendPublicationsToDOM(data);
        attachReactionListeners();
      })
  });

}

document.querySelectorAll('.like-button, .dislike-button').forEach(button => {
  button.addEventListener('click', (event) => {
    const pubId = event.target.closest('.publication').dataset.id;
    let reactionType = null;

    if (event.target.classList.contains('like-button')) {
      reactionType = 'like';
    } else if (event.target.classList.contains('dislike-button')) {
      reactionType = 'dislike';
    }
    if (reactionType) {
      fetch(window.location.href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          action: 'react',
          publication_id: pubId,
          reaction: reactionType
        })
      })
        .then(res => {
          if (!res.ok) throw new Error(`Erreur HTTP ${res.status}`);
          return res.json();
        })
        .then(data => {
          updateReactionsUI(pubId, data.nb_likes, data.nb_dislikes);
        })
        .catch(err => {
          console.error("Erreur de réaction:", err);
        });
    }
  });
});

function updateReactionsUI(pubId, nbLikes, nbDislikes) {
  const pubDiv = document.querySelector(`.publication[data-id="${pubId}"]`);
  if (!pubDiv) return;

  const likeBtn = pubDiv.querySelector('.like-button');
  const dislikeBtn = pubDiv.querySelector('.dislike-button');

  likeBtn.textContent = `👍 ${nbLikes}`;
  dislikeBtn.textContent = `👎 ${nbDislikes}`;
}

//mettre les event de btn pour like/dislike
function attachReactionListeners() {
  document.querySelectorAll('.like-button, .dislike-button').forEach(button => {
    if (button.dataset.listenerAttached) return;
    button.dataset.listenerAttached = "true";

    button.addEventListener('click', (event) => {
      const pubId = event.target.closest('.publication').dataset.id;
      let reactionType = null;

      if (event.target.classList.contains('like-button')) {
        reactionType = 'like';
      } else if (event.target.classList.contains('dislike-button')) {
        reactionType = 'dislike';
      }

      if (reactionType) {
        fetch(window.location.href, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            action: 'react',
            publication_id: pubId,
            reaction: reactionType
          })
        })
          .then(res => {
            if (!res.ok) throw new Error(`Erreur HTTP ${res.status}`);
            return res.json();
          })
          .then(data => {
            updateReactionsUI(pubId, data.nb_likes, data.nb_dislikes);
          })
          .catch(err => {
            console.error("Erreur de réaction:", err);
          });
      }
    });
  });
}
