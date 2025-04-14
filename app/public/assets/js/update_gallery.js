// document.addEventListener('DOMContentLoaded', () => {
//   document.querySelectorAll('.reaction-buttons').forEach(div => {
//       const publicationId = div.dataset.id;

//       div.querySelector('.like-button').addEventListener('click', () => {
//           sendReaction(publicationId, 'like', div);
//       });

//       div.querySelector('.dislike-button').addEventListener('click', () => {
//           sendReaction(publicationId, 'dislike', div);
//       });
//   });
// });

// function sendReaction(publicationId, type, container) {
//   fetch('gallery.php', {
//       method: 'POST',
//       headers: {
//           'Content-Type': 'application/x-www-form-urlencoded',
//       },
//       body: `publication_id=${publicationId}&${type}=1`
//   })
//   .then(response => response.text())
//   .then(() => {
//       // Recharge les likes/dislikes pour cette publication
//       reloadReaction(publicationId, container);
//   });
// }

// function reloadReaction(publicationId, container) {
//   fetch(`get_reaction_data.php?publication_id=${publicationId}`)
//   .then(res => res.json())
//   .then(data => {
//       const likeButton = container.querySelector('.like-button');
//       const dislikeButton = container.querySelector('.dislike-button');

//       likeButton.textContent = `👍 ${data.nb_likes}`;
//       dislikeButton.textContent = `👎 ${data.nb_dislikes}`;

//       likeButton.classList.toggle('active', data.user_reaction === 'like');
//       dislikeButton.classList.toggle('active', data.user_reaction === 'dislike');
//   });
// }
document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
  
    document.querySelectorAll(".reaction-buttons").forEach((div) => {
      const pubId = div.dataset.id;
      const likeBtn = div.querySelector(".like-button");
      const dislikeBtn = div.querySelector(".dislike-button");
  
      likeBtn.addEventListener("click", () => handleReaction(pubId, "like", div));
      dislikeBtn.addEventListener("click", () => handleReaction(pubId, "dislike", div));
    });
  
    async function handleReaction(publicationId, action, container) {
      const formData = new FormData();
      formData.append("csrf_token", csrfToken);
      formData.append("publication_id", publicationId);
      formData.append(action, "1");
  
      try {
        const response = await fetch("gallery.php", {
          method: "POST",
          body: formData,
        });
  
        const result = await response.json();
  
        if (result.status === "success") {
          const likeBtn = container.querySelector(".like-button");
          const dislikeBtn = container.querySelector(".dislike-button");
  
          likeBtn.textContent = `👍 ${result.nb_likes}`;
          dislikeBtn.textContent = `👎 ${result.nb_dislikes}`;
  
          likeBtn.classList.toggle("active", result.user_reaction === "like");
          dislikeBtn.classList.toggle("active", result.user_reaction === "dislike");
        } else {
          alert(result.message || "Erreur lors de l'enregistrement de la réaction.");
        }
      } catch (err) {
        console.error("Erreur AJAX :", err);
        alert("Erreur de communication avec le serveur.");
      }
    }
  });
  