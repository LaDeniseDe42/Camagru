const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");
let score = 0;
let animationId;
let gameOver = false;
let eSpeed = 4;
let lastSpeedUpdateScore = 0;
let backgroundSpeed = 2;
let spawnInterval = 120;
let enemies = [];
let frameCount = 0;
let enemySpawnTimer = 120;
//BOULE DE FEU
let fireballs = [];
let fireballCharges = 3;
const maxFireballCharges = 3;
let lastFireballScore = 0;

const fireballImg = new Image();
fireballImg.src = "assets/img/game/FB.png"; // ajoute cette image dans ton projet
// Fin Boule de feu

//explosion
let explosions = [];

const explosionImg = new Image();
explosionImg.src = "assets/img/game/explo.png";
// Fin explosion
//armor
let armorImg = new Image();
armorImg.src = "assets/img/game/armor.png";
let pArmorImg = new Image();
pArmorImg.src = "assets/img/game/pArmor.png";
let pArmorImg2 = new Image();
pArmorImg2.src = "assets/img/game/AFB.png";
let armor = null; // L'objet armure
let armorProtection = 0; // Protection restante
let lastArmorScore = 0; // Pour éviter de redonner à chaque frame

// Fin armor

let width = window.innerWidth;
let height = window.innerHeight;

canvas.width = width;
canvas.height = height;

// Chargement des images
const playerImg = new Image();
playerImg.src = "assets/img/filters/jeuB.png";
const playerImg2 = new Image();
playerImg2.src = "assets/img/game/pBoule.png";

const enemyImgs = [
  "assets/img/filters/FOG(1).png",
  "assets/img/filters/hibouxG.png",
  "assets/img/filters/jeuM.png",
].map((src) => {
  const img = new Image();
  img.src = src;
  return img;
});

const backgroundImg = new Image();
backgroundImg.src = "assets/img/quidditch.png";

// Joueur
const player = {
  x: 50,
  y: height / 2 - 50,
  width: 100,
  height: 100,
  speed: 5,
};

// Gestion des touches
let keys = {};
window.addEventListener("keydown", (e) => (keys[e.key] = true));
window.addEventListener("keyup", (e) => (keys[e.key] = false));

// Animation principale
function gameLoop() {
  ctx.clearRect(0, 0, width, height);
  ctx.drawImage(backgroundImg, -(frameCount % width), 0, width, height);
  ctx.drawImage(backgroundImg, width - (frameCount % width), 0, width, height);
  frameCount += backgroundSpeed;

  // Affichage du score
  ctx.fillStyle = "white";
  ctx.font = "30px Arial";
  ctx.textAlign = "center";
  ctx.fillText("Score : " + score, width / 2, 40);
  ctx.fillText("Boules de feu : " + fireballCharges, width / 2, 80);

  // Mouvement du joueur
  if ((keys["ArrowUp"] || keys["w"]) && player.y > 0) player.y -= player.speed;
  if ((keys["ArrowDown"] || keys["s"]) && player.y + player.height < height)
    player.y += player.speed;
  if ((keys["ArrowLeft"] || keys["a"]) && player.x > 0)
    player.x -= player.speed;
  if ((keys["ArrowRight"] || keys["d"]) && player.x + player.width < width)
    player.x += player.speed;
  //fireball
  // Ajout d'un verrou pour éviter le tir continu
  if (
    (keys[" "] || keys["Space"]) &&
    fireballCharges > 0 &&
    !keys["_fireballPressed"]
  ) {
    fireballs.push({
      x: player.x + player.width,
      y: player.y + player.height / 2 - 10,
      width: 30,
      height: 50,
      speed: player.speed + 5,
    });
    fireballCharges--;
    keys["_fireballPressed"] = true;
  }
  if (!(keys[" "] || keys["Space"])) {
    keys["_fireballPressed"] = false;
  }
  let currentPlayerImg;
  if (armorProtection > 0) {
    currentPlayerImg = fireballs.length > 0 ? pArmorImg2 : pArmorImg;
  } else {
    currentPlayerImg = fireballs.length > 0 ? playerImg2 : playerImg;
  }

  ctx.drawImage(
    currentPlayerImg,
    player.x,
    player.y,
    player.width,
    player.height
  );

  // Mise à jour des boules de feu
  fireballs.forEach((fireball, fIndex) => {
    fireball.x += fireball.speed;
    ctx.drawImage(
      fireballImg,
      fireball.x,
      fireball.y,
      fireball.width,
      fireball.height
    );

    // Collision avec les ennemis
    enemies.forEach((enemy, eIndex) => {
      if (
        fireball.x < enemy.x + enemy.width &&
        fireball.x + fireball.width > enemy.x &&
        fireball.y < enemy.y + enemy.height &&
        fireball.y + fireball.height > enemy.y
      ) {
        // Supprimer l’ennemi et la boule
        explosions.push({
          x: enemy.x + enemy.width / 2 - 40,
          y: enemy.y + enemy.height / 2 - 40,
          width: 80,
          height: 80,
          timer: 15, // durée de vie en frames (~250ms si 60fps)
        });
        enemies.splice(eIndex, 1);
        fireballs.splice(fIndex, 1);
        score++;
      }
    });

    // Supprimer si hors écran
    if (fireball.x > width) {
      fireballs.splice(fIndex, 1);
    }
  });
  explosions.forEach((explosion, index) => {
    ctx.drawImage(
      explosionImg,
      explosion.x,
      explosion.y,
      explosion.width,
      explosion.height
    );
    explosion.timer--;
    if (explosion.timer <= 0) {
      explosions.splice(index, 1);
    }
  });

  enemySpawnTimer += 1;
  if (enemySpawnTimer >= Math.max(spawnInterval, 15)) {
    enemies.push({
      x: width,
      y: Math.random() * (height - 100),
      width: 80,
      height: 80,
      speed: eSpeed,
      img: getRandomEnemy(),
    });
    enemySpawnTimer = 0;
  }

  // Déplacement et dessin des ennemis
  enemies.forEach((enemy, index) => {
    enemy.speed = eSpeed;
    enemy.x -= enemy.speed;
    ctx.drawImage(enemy.img, enemy.x, enemy.y, enemy.width, enemy.height);

    // Collision
    if (
      enemy.x < player.x + player.width &&
      enemy.x + enemy.width > player.x &&
      enemy.y < player.y + player.height &&
      enemy.y + enemy.height > player.y &&
      !gameOver
    ) {
      if (armorProtection > 0) {
        armorProtection--;
        enemies.splice(index, 1); // détruire l’ennemi
        explosions.push({
          x: enemy.x + enemy.width / 2 - 40,
          y: enemy.y + enemy.height / 2 - 40,
          width: 80,
          height: 80,
          timer: 15,
        });
      } else {
        gameOver = true;
        cancelAnimationFrame(animationId); // stoppe la boucle
        const finalScore = score;

        showRestartDialog(finalScore).then((restart) => {
          if (restart) resetGame();
          else window.location.href = "/index.php";
        });
      }
    }

    // Supprimer les ennemis sortis de l'écran
    if (enemy.x + enemy.width < 0) {
      enemies.splice(index, 1);
      score++;
    }
  });
  updateSpeedBasedOnScore();
  // Apparition aléatoire d'armure tous les 20 points
  if (score > 0 && score % 20 === 0 && score !== lastArmorScore && !armor) {
    if (Math.random() < 0.5) {
      // 50% de chance
      armor = {
        x: width * 0.75,
        y: -80,
        width: 60,
        height: 60,
        speed: 3,
      };
      lastArmorScore = score;
    }
  }
  if (armor) {
    armor.y += armor.speed;

    // Collision avec le joueur
    const collidesWithPlayer =
      armor.x < player.x + player.width &&
      armor.x + armor.width > player.x &&
      armor.y < player.y + player.height &&
      armor.y + armor.height > player.y;

    if (collidesWithPlayer) {
      armorProtection = 2;
      armor = null;
    } else if (armor.y > height) {
      armor = null;
    } else {
      // On dessine uniquement si l’armure n’a pas été ramassée ou sortie
      ctx.drawImage(armorImg, armor.x, armor.y, armor.width, armor.height);
    }
  }

  animationId = requestAnimationFrame(gameLoop);
}

// Redimensionnement du canvas
window.addEventListener("resize", () => {
  width = window.innerWidth;
  height = window.innerHeight;
  canvas.width = width;
  canvas.height = height;
});

// Démarrer une fois que le fond est chargé
backgroundImg.onload = () => gameLoop();

// Fonction pour choisir une image d’ennemi aléatoire
function getRandomEnemy() {
  const randomIndex = Math.floor(Math.random() * enemyImgs.length);
  return enemyImgs[randomIndex];
}

// Fonction pour réinitialiser le jeu
function resetGame() {
  enemies = [];
  player.x = 50;
  player.y = height / 2 - 50;
  player.speed = 5;
  player.width = 100;
  player.height = 100;
  backgroundSpeed = 2;
  frameCount = 0;
  score = 0;
  gameOver = false;
  animationId = null;
  lastSpeedUpdateScore = 0;
  spawnInterval = 120;
  eSpeed = 4;
  enemySpawnTimer = 120;
  fireballCharges = 3;
  armor = null;
  armorProtection = 0;
  lastArmorScore = 0;
}

function showRestartDialog(finalScore) {
  const modal = document.getElementById("restartModal");
  modal.classList.remove("hidden");

  return new Promise((resolve) => {
    sendScoreToPHP(finalScore).then((data) => {
      document.getElementById("currentScore").innerHTML =
        "Tu es tombé de ton balai avec un score de <b>" + finalScore + "</b>";

      if (data.new_best === true) {
        document.getElementById("bestScore").textContent =
          "Bravo ! Tu as battu ton meilleur score !";
      } else {
        document.getElementById("bestScore").innerHTML =
          "Ton meilleur score est de <b>" + data.best_score + "</b>";
      }
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

function sendScoreToPHP(score) {
  return fetch("/get_score.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "score=" + encodeURIComponent(score),
  })
    .then((response) => response.json())
    .catch((error) => {
      console.error("Erreur lors de l'envoi du score:", error);
    });
}

function updateSpeedBasedOnScore() {
  // const speedLevel = Math.floor(score / 10);
  if (score > 0 && score % 10 === 0 && score !== lastFireballScore) {
    if (fireballCharges < maxFireballCharges) {
      fireballCharges++;
      lastFireballScore = score;
    }
  }

  if (score <= 90) {
    if (score >= 10 && score >= lastSpeedUpdateScore + 10) {
      lastSpeedUpdateScore = score;
      // Augmente les vitesses
      player.speed = 5 + Math.floor(score / 10);
      backgroundSpeed = 2 + Math.floor(score / 10);
      eSpeed = backgroundSpeed + 3;
      // Ajuste le spawnInterval (progressif)
      spawnInterval = Math.max(20, 120 - Math.floor(score * 1.5));
    }
  }
  if (score > 190) {
    player.speed = 18;
    backgroundSpeed = 18;
    eSpeed = 18;
    spawnInterval = 15;
  }
}
