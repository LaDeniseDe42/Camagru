export function drawBossProjectiles(i, bossProjectiles, ctx) {
  const projectile = bossProjectiles[i];
  projectile.x += projectile.speed;

  // Dessin d'un trait de flamme
  const grad = ctx.createLinearGradient(
    projectile.x,
    projectile.y + projectile.height / 2,
    projectile.x + projectile.width * 2,
    projectile.y + projectile.height / 2
  );
  grad.addColorStop(0, "#fffa00");
  grad.addColorStop(0.3, "#ff9900");
  grad.addColorStop(0.7, "#ff3300");
  grad.addColorStop(1, "rgba(255,0,0,0.1)");

  ctx.save();
  ctx.globalAlpha = 0.85;
  ctx.strokeStyle = grad;
  ctx.lineWidth = 12;
  ctx.lineCap = "round";
  ctx.beginPath();
  ctx.moveTo(projectile.x, projectile.y + projectile.height / 2);
  ctx.lineTo(
    projectile.x + projectile.width * 2.5,
    projectile.y + projectile.height / 2 + (Math.random() - 0.5) * 10
  );
  ctx.stroke();
  ctx.globalAlpha = 1;
  ctx.restore();
  ctx.save();
  ctx.globalAlpha = 0.4;
  ctx.strokeStyle = "#fff";
  ctx.lineWidth = 4;
  ctx.beginPath();
  ctx.moveTo(projectile.x + 8, projectile.y + projectile.height / 2);
  ctx.lineTo(
    projectile.x + projectile.width * 2,
    projectile.y + projectile.height / 2 + (Math.random() - 0.5) * 6
  );
  ctx.stroke();
  ctx.globalAlpha = 1;
  ctx.restore();

  if (projectile.x < -projectile.width) {
    bossProjectiles.splice(i, 1);
    i--;
  }
}

// Fonction de collision entre deux rectangles
export function isColliding(rect1, rect2) {
  return (
    rect1.x < rect2.x + rect2.width &&
    rect1.x + rect1.width > rect2.x &&
    rect1.y < rect2.y + rect2.height &&
    rect1.y + rect1.height > rect2.y
  );
}

export function showRestartDialog(finalScore) {
  const modal = document.getElementById("restartModal");
  modal.classList.remove("hidden");

  return new Promise((resolve) => {
    sendScoreToPHP(finalScore).then((data) => {
      document.getElementById("currentScore").innerHTML =
        "Tu es tombé de ton balai avec un score de <b>" + finalScore + "</b>";

      document.getElementById("bestScore").innerHTML = data.new_best
        ? "Bravo ! Tu as battu ton meilleur score !"
        : "Ton meilleur score est de <b>" + data.best_score + "</b>";

      document.getElementById("yesBtn").onclick = () => {
        modal.classList.add("hidden");
        resolve(true);
      };
      document.getElementById("noBtn").onclick = () => {
        modal.classList.add("hidden");
        resolve(false);
      };
    });
  });
}

export function sendScoreToPHP(score) {
  return fetch("/get_score.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "score=" + encodeURIComponent(score),
  })
    .then((response) => response.json())
    .catch((error) => console.error("Erreur lors de l'envoi du score:", error));
}

export function showBossMessage(ctx, width, height) {
  // Afficher le message en grand au centre, assombrir le reste de l'écran
  ctx.save();
  ctx.fillStyle = "rgba(0,0,0,0.85)";
  ctx.fillRect(0, 0, width, height);

  // Zone centrale pour le texte (bandeau centré) - Responsive basé sur la taille de l'écran
  const boxPadding = Math.max(20, Math.min(width, height) * 0.04);
  const boxWidth = Math.min(width * 0.8, 700);
  const boxHeight = Math.max(height * 0.18, 120);
  const boxX = (width - boxWidth) / 2;
  const boxY = (height - boxHeight) / 2;

  ctx.fillStyle = "rgba(30,30,30,0.95)";
  ctx.fillRect(boxX, boxY, boxWidth, boxHeight);

  ctx.strokeStyle = "white";
  ctx.lineWidth = 3;
  ctx.strokeRect(boxX, boxY, boxWidth, boxHeight);

  // Responsive font sizes selon la hauteur et largeur de l'écran
  // Calcul dynamique de la taille de police pour éviter le dépassement sur petits écrans
  const minDim = Math.min(width, height);
  // On limite la taille max et min pour éviter le débordement
  const baseFont = Math.max(12, Math.min(18, Math.floor(minDim / 32)));
  const titleFont = Math.max(14, Math.min(22, Math.floor(minDim / 22)));

  ctx.fillStyle = "white";
  ctx.textAlign = "center";
  ctx.textBaseline = "middle";

  ctx.font = `bold ${baseFont}px Arial`;
  ctx.fillText(
    "Vous apercevez au loin un immense dragon volant dans votre direction.",
    width / 2,
    boxY + boxHeight / 2 - baseFont
  );
  ctx.font = `bold ${titleFont}px Arial`;
  ctx.fillText(
    "Vous savez qu'aucun sort ne pourra vous aider",
    width / 2,
    boxY + boxHeight / 2 + titleFont * 0.7
  );
  ctx.restore();
}
