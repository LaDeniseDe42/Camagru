// bouttonComment = document.getElementById("newComment");
// if (bouttonComment) {
//     bCToken = document.querySelector('input[name="token"]').value;
// }

// document.querySelectorAll(".deleteComment").forEach(btn => {
//     btn.addEventListener("click", function (e) {
//         e.preventDefault();
//         const commentId = btn.dataset.commentId;
//         const csrfToken = btn.value;
//         let filePath = new URLSearchParams(window.location.search).get("file");
//         const form = new FormData();
//         form.append("commentId", commentId);
//         form.append("token", csrfToken);
//         form.append("file", filePath);
//         form.append("deleteComment", "1");

//         fetch("media.php", {
//             method: "POST",
//             body: form,
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 const commentElement = btn.closest(".comment");
//                 commentElement.remove();
//                 console.log("Commentaire supprimé !");
//             } else {
//                 console.error("Erreur :", data.message);
//             }
//         })
//         .catch(error => {
//             console.error("Erreur Fetch :", error);
//         });
//     });
// });


// document.querySelectorAll(".editComment").forEach(btn => {
//     btn.addEventListener("click", function (e) {
//         e.preventDefault();
//         let commentId = btn.dataset.commentId;
//         let csrfToken = btn.value;
//         let filePath = new URLSearchParams(window.location.search).get("file");
//         let commentText = btn.dataset.commentText;
//         let modal = document.createElement("div");
//         modal.classList.add("modal");
//         modal.innerHTML = `
//             <div class="modal-content">
//                 <span class="close">&times;</span>
//                 <h2>Modifier le commentaire</h2>
//                 <textarea id="editCommentText">${commentText}</textarea>
//                 <button id="saveEditComment" data-comment-id="${commentId}" value="${csrfToken}">Enregistrer</button>
//             </div>
//         `;
//         document.body.appendChild(modal);
//         modal.querySelector(".close").addEventListener("click", function () {
//             modal.remove();
//         });
//         modal.querySelector("#saveEditComment").addEventListener("click", function () {
//             let newCommentText = modal.querySelector("#editCommentText").value;
//             const form = new FormData();
//             form.append("commentId", commentId);
//             form.append("token", csrfToken);
//             form.append("file", filePath);
//             form.append("comment", newCommentText);
//             form.append("editComment", "1");

//             if (newCommentText.trim() === "") {
//                 alert("Le commentaire ne peut pas être vide.");
//                 return;
//             }
//             fetch("media.php", {
//                 method: "POST",
//                 body: form,
//             })
//             .then(response => response.text())
//             .then(text => {
//                 if (text.includes("Commentaire modifié")) {
//                     const commentContainer = btn.closest(".comment");
//                     const commentParagraph = commentContainer.querySelector("p");
//                     commentParagraph.textContent = newCommentText;
//                     btn.dataset.commentText = newCommentText;
//                     modal.remove();
//                 } else {
//                     console.error("Erreur :", text);
//                 }
//             })
//             .catch(error => {
//                 console.error("Erreur Fetch :", error);
//             });
//         }
//         );
//     });
// });



// if (bouttonComment) {
//     bouttonComment.addEventListener("click", function (e) {
//         e.preventDefault();
//         let comment = document.getElementById("theComment");
//         let commentText = comment.value;
//         let postId = comment.dataset.postId;
//         form = new FormData();
//         let filePath = new URLSearchParams(window.location.search).get("file");
//         form.append("comment", commentText);
//         form.append("postId", postId);
//         form.append("file", filePath);
//         form.append("token", bCToken);
//         form.append("commentPost", "1");
//         fetch("media.php", {
//             method: "POST",
//             body: form,
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 const commentContainer = document.querySelector(".comments");
//                 const newComment = document.createElement("div");
//                 newComment.classList.add("comment");
        
//                 newComment.innerHTML = `
//                     <strong>${data.username}:</strong>
//                     <p>${commentText}</p>
//                     <span class="timestamp">${data.created_at}</span>
//                     <button class="editComment" value="${bCToken}" data-comment-id="${data.comment_id}" data-comment-text="${commentText}">✏️ Modifier</button>
//                     <button class="deleteComment" value="${bCToken}" data-comment-id="${data.comment_id}">Supprimer</button>
//                 `;
//                 commentContainer.insertBefore(newComment, commentContainer.querySelector("#theComment").parentElement);
//                 comment.value = "";
//             } else {
//                 console.error("Erreur serveur :", data.message);
//             }
//         })
//         .catch(error => {
//             console.error("Erreur Fetch :", error);
//         });
//     }
//     );
// }
    //     fetch("media.php", {
    //         method: "POST",
    //         body: form,
    //     })
    //         .then((response) => response.text())
    //         .then(text => {
    //             console.log("Réponse PHP :", text);
    //             if (text.includes("Commentaire ajouté")) {
    //                 console.log("Commentaire ajouté avec succès !");
    //                 window.location.reload();
    //             } else {
    //                 console.error("Erreur serveur :", text);
    //             }
    //       })
    //       .catch(error => {
    //           console.error("Erreur Fetch :", error);
    //       });
    // });

    document.addEventListener("DOMContentLoaded", () => {
        const bouttonComment = document.getElementById("newComment");
        const bCToken = document.querySelector('input[name="token"]').value;
        const filePath = new URLSearchParams(window.location.search).get("file");
    
        function attachDeleteListeners() {
            document.querySelectorAll(".deleteComment").forEach(btn => {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    const commentId = btn.dataset.commentId;
                    const csrfToken = btn.value;
                    const form = new FormData();
                    form.append("commentId", commentId);
                    form.append("token", csrfToken);
                    form.append("file", filePath);
                    form.append("deleteComment", "1");
    
                    fetch("media.php", { method: "POST", body: form })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                btn.closest(".comment").remove();
                            } else {
                                console.error("Erreur :", data.message);
                            }
                        })
                        .catch(error => console.error("Erreur Fetch :", error));
                });
            });
        }
    
        // Ajouter un commentaire sans recharger la page
        if (bouttonComment) {
            bouttonComment.addEventListener("click", function (e) {
                e.preventDefault();
                const comment = document.getElementById("theComment");
                const commentText = comment.value.trim();
                const postId = comment.dataset.postId;
    
                if (!commentText) return;
    
                const form = new FormData();
                form.append("comment", commentText);
                form.append("postId", postId);
                form.append("file", filePath);
                form.append("token", bCToken);
                form.append("commentPost", "1");
    
                fetch("media.php", { method: "POST", body: form })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const commentContainer = document.querySelector(".comments");
                            const newComment = document.createElement("div");
                            newComment.classList.add("comment");
                            newComment.innerHTML = `
                                <strong>${data.username}:</strong>
                                <p>${commentText}</p>
                                <span class="timestamp">${data.created_at}</span>
                                <button class="editComment" value="${bCToken}" data-comment-id="${data.comment_id}" data-comment-text="${commentText}">✏️ Modifier</button>
                                <button class="deleteComment" value="${bCToken}" data-comment-id="${data.comment_id}">Supprimer</button>
                            `;
                            commentContainer.insertBefore(newComment, comment);
                            comment.value = "";
    
                            attachDeleteListeners();
                            attachEditListeners();
                        } else {
                            console.error("Erreur serveur :", data.message);
                        }
                    })
                    .catch(error => console.error("Erreur Fetch :", error));
            });
        }
    
        attachDeleteListeners();
        attachEditListeners();
    
    });
    
    function attachEditListeners() {
        document.querySelectorAll(".editComment").forEach(btn => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                const commentElement = btn.closest(".comment");
                const commentId = btn.dataset.commentId;
                const currentText = btn.dataset.commentText;
    
                // Remplacer <p> par une textarea
                const textarea = document.createElement("textarea");
                textarea.value = currentText;
                commentElement.querySelector("p").replaceWith(textarea);
    
                // Créer bouton sauvegarde
                const saveBtn = document.createElement("button");
                saveBtn.textContent = "💾 Sauvegarder";
                saveBtn.classList.add("saveEdit");
                btn.style.display = "none"; // cacher le bouton modifier
                btn.insertAdjacentElement("afterend", saveBtn);
    
                saveBtn.addEventListener("click", function () {
                    const newText = textarea.value.trim();
                    if (!newText) return;
                    const filePath = new URLSearchParams(window.location.search).get("file");
                    console.log("File path:", filePath);
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
                                console.log("Réponse PHP :", data);
                                const p = document.createElement("p");
                                p.textContent = newText;
                                textarea.replaceWith(p);
                                btn.dataset.commentText = newText;
                                saveBtn.remove();
                                btn.style.display = ""; // réafficher le bouton modifier
                            } else {
                                console.log("Réponse PHP :", data);
                                console.error("Erreur serveur :", data.message);
                            }
                        })
                        .catch(error => console.error("Erreur Fetch :", error));
                });
            });
        });
    }
    