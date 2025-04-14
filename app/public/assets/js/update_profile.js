document.addEventListener("DOMContentLoaded", () => {
  // const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || window.csrfToken;
  const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;

  // Enregistre tous les formulaires des modales
  const forms = document.querySelectorAll(".modal form");

  forms.forEach((form) => {
      form.addEventListener("submit", async (e) => {
          e.preventDefault();

          const formData = new FormData(form);

          formData.append("csrf_token", csrfToken);

          try {
              const response = await fetch(window.location.href, {
                  method: "POST",
                  body: formData,
              });

              const result = await response.json();
              console.log(result);

              if (result.status === "success") {
                  // Mise à jour ciblée de l'élément modifié
                  const updatedValue = result.updatedValue;
                  const targetElement = document.getElementById(result.targetId);
                  if (result.targetId == "house") {
                    window.location.reload();
                  } else if (targetElement && result.targetId != "house") {
                      targetElement.textContent = updatedValue;
                  }
                  // Fermer la modal après mise à jour
                  closeModal();
              } else {
                  alert(result.message || "Une erreur est survenue.");
              }
          } catch (error) {
              console.error("Erreur AJAX:", error);
              alert("Une erreur réseau est survenue. " + error.message);
          }
      });
  });
});
