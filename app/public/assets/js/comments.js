    //gerer les commentaires
    document.addEventListener("DOMContentLoaded", () => {
        const token = document.querySelector('input[name="token"]').value;
        const filePath = new URLSearchParams(window.location.search).get("file");
        const commentButton = document.getElementById("newComment");
    
        if (commentButton) {
            commentButton.addEventListener("click", () => handleAddComment(token, filePath));
        }
    
        attachDeleteListeners(filePath);
        attachEditListeners(filePath);
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    insertNewComment(data, commentText, token);
                    commentInput.value = "";
                } else {
                    console.error("Erreur serveur :", data.message);
                }
            })
            .catch(error => console.error("Erreur Fetch :", error));
    }
    
    // mettre le commentaire directement sur la page
    function insertNewComment(data, commentText, token) {
        const commentContainer = document.querySelector(".comments");
        const newComment = document.createElement("div");
        const noComment = document.getElementById("noComment");
        // Cacher le message "Aucun commentaire" si un commentaire est ajouté
        if (noComment) {
            noComment.style.display = "none";
        }
        newComment.classList.add("comment");
        newComment.innerHTML = `
            <strong>${data.username}:</strong>
            <p>${commentText}</p>
            <span class="timestamp">${data.created_at}</span>
            <button class="editComment" value="${token}" data-comment-id="${data.comment_id}" data-comment-text="${commentText}">✏️ Modifier</button>
            <button class="deleteComment" value="${token}" data-comment-id="${data.comment_id}">Supprimer</button>
        `;
        commentContainer.insertBefore(newComment, document.getElementById("theComment"));
    
        attachDeleteListeners();
        attachEditListeners();
    }
    
    //Suppression de commentaire
    function attachDeleteListeners(filePath) {
        document.querySelectorAll(".deleteComment").forEach(btn => {
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
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            btn.closest(".comment").remove();
                            const commentContainer = document.querySelector(".comments");
                            if (commentContainer.querySelectorAll(".comment").length === 0) {
                                const noComment = document.getElementById("noComment");
                                // Afficher le message "Aucun commentaire" si aucun commentaire n'est présent
                                noComment.style.display = "block";
                            }
                        } else {
                            console.error("Erreur :", data.message);
                        }
                    })
                    .catch(error => console.error("Erreur Fetch :", error));
            });
        });
    }
    
    //Modif de commentaire
    function attachEditListeners(filePath) {
        document.querySelectorAll(".editComment").forEach(btn => {
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
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const p = document.createElement("p");
                                p.textContent = newText;
                                textarea.replaceWith(p);
                                btn.dataset.commentText = newText;
                                saveBtn.remove();
                                btn.style.display = "";
                            } else {
                                console.error("Erreur serveur :", data.message);
                            }
                        })
                        .catch(error => console.error("Erreur Fetch :", error));
                });
            });
        });
    }
    