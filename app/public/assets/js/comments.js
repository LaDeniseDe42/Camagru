//gerer les commentaires
document.addEventListener("DOMContentLoaded", () => {
  const token = document.querySelector('input[name="token"]').value;
  const filePath = new URLSearchParams(window.location.search).get("file");
  const commentButton = document.getElementById("newComment");

  if (commentButton) {
    commentButton.addEventListener("click", () =>
      handleAddComment(token, filePath)
    );
  }

  // Attacher les listeners initiaux une seule fois
  document
    .querySelectorAll(".deleteComment")
    .forEach((btn) => attachDeleteListener(btn, filePath));
  document
    .querySelectorAll(".editComment")
    .forEach((btn) => attachEditListener(btn, filePath));
});

//Ajout d’un commentaire
function handleAddComment(token, filePath) {
  const commentInput = document.getElementById("theComment");
  const commentText = commentInput.value.trim();
  const postId = commentInput.dataset.postId;

  if (!commentText) return;

  const form = new FormData();
  form.append("comment", commentText);
  form.append("postId", postId);
  form.append("file", filePath);
  form.append("token", token);
  form.append("commentPost", "1");

  fetch("media.php", { method: "POST", body: form })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        insertNewComment(data, commentText, token, filePath);
        commentInput.value = "";
      } else {
        alert("Erreur lors de l'ajout du commentaire : " + data.message);
      }
    })
    .catch((error) => console.error("Erreur Fetch :", error));
}

// mettre le commentaire directement sur la page
function insertNewComment(data, commentText, token, filePath) {
  const commentContainer = document.querySelector(".comments");
  const newComment = document.createElement("div");
  const noComment = document.getElementById("noComment");

  if (noComment) {
    noComment.style.display = "none";
  }

  newComment.classList.add("comment");

  const usernameElem = document.createElement("strong");
  usernameElem.textContent = `${data.username}:`;

  const contentElem = document.createElement("p");
  contentElem.textContent = commentText;

  const timestampElem = document.createElement("span");
  timestampElem.classList.add("timestamp");
  timestampElem.textContent = data.created_at;

  const editBtn = document.createElement("button");
  editBtn.classList.add("editComment");
  editBtn.textContent = "✏️ Modifier";
  editBtn.value = token;
  editBtn.dataset.commentId = data.comment_id;
  editBtn.dataset.commentText = commentText;

  const deleteBtn = document.createElement("button");
  deleteBtn.classList.add("deleteComment");
  deleteBtn.textContent = "Supprimer";
  deleteBtn.value = token;
  deleteBtn.dataset.commentId = data.comment_id;

  newComment.appendChild(usernameElem);
  newComment.appendChild(contentElem);
  newComment.appendChild(timestampElem);
  newComment.appendChild(editBtn);
  newComment.appendChild(deleteBtn);

  commentContainer.insertBefore(
    newComment,
    document.getElementById("theComment")
  );

  // Attacher les listeners uniquement au nouveau commentaire
  attachDeleteListener(deleteBtn, filePath);
  attachEditListener(editBtn, filePath);
}

// Attache un seul delete listener
function attachDeleteListener(btn, filePath) {
  btn.addEventListener("click", function (e) {
    e.preventDefault();
    const commentId = btn.dataset.commentId;
    const token = btn.value;

    const form = new FormData();
    form.append("commentId", commentId);
    form.append("token", token);
    form.append("file", filePath);
    form.append("deleteComment", "1");

    fetch("media.php", { method: "POST", body: form })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          btn.closest(".comment").remove();
          setTimeout(() => {
            const remainingComments = document.querySelectorAll(".comment");
            if (remainingComments.length === 0) {
              const noComment = document.getElementById("noComment");
              if (noComment) {
                noComment.style.display = "block";
              }
            }
          }, 0);
        } else {
          // Afficher l'erreur sur la page
          const errorContainer = document.getElementById("errorContainer");
          if (errorContainer) {
            errorContainer.textContent = data.message;
            errorContainer.style.display = "block";
          }
        }
      })
      .catch((error) => console.error("Erreur Fetch :", error));
  });
}

// Attache un seul edit listener
function attachEditListener(btn, filePath) {
  btn.addEventListener("click", function (e) {
    e.preventDefault();

    const commentElement = btn.closest(".comment");
    const commentId = btn.dataset.commentId;
    const currentText = btn.dataset.commentText;

    const textarea = document.createElement("textarea");
    textarea.value = currentText;
    commentElement.querySelector("p").replaceWith(textarea);

    const saveBtn = document.createElement("button");
    saveBtn.textContent = "💾 Sauvegarder";
    saveBtn.classList.add("saveEdit");
    btn.style.display = "none";
    btn.insertAdjacentElement("afterend", saveBtn);

    saveBtn.addEventListener("click", () => {
      const newText = textarea.value.trim();
      if (!newText) return;

      const form = new FormData();
      form.append("editComment", "1");
      form.append("commentId", commentId);
      form.append("newContent", newText);
      form.append("token", btn.value);
      form.append("file", filePath);

      fetch("media.php", { method: "POST", body: form })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            const p = document.createElement("p");
            p.textContent = newText;
            textarea.replaceWith(p);
            btn.dataset.commentText = newText;
            saveBtn.remove();
            btn.style.display = "";
          } else {
            alert(
              "Erreur lors de la modification du commentaire : " + data.message
            );
          }
        })
        .catch((error) => console.error("Erreur Fetch :", error));
    });
  });
}
