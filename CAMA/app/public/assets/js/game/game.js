import {
  drawBossProjectiles,
  isColliding,
  showRestartDialog,
  sendScoreToPHP,
  showBossMessage,
} from "./utils.js";
// === INITIALISATION DU CANVAS ===
const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");
let width = window.innerWidth;
let height = window.innerHeight;
canvas.width = width;
canvas.height = height;

// === VARIABLES DE JEU ===
let score = 149;
let animationId;
let gameOver = false;
let frameCount = 0;

// === PARAMÈTRES DE DIFFICULTÉ ===
let eSpeed = 4;
let backgroundSpeed = 2;
let spawnInterval = 120;
let enemySpawnTimer = 120;
let lastSpeedUpdateScore = 0;

// === ENTITÉS DU JEU ===
let enemies = [];
let fireballs = [];
let explosions = [];
let armor = null;
let potion = null;

// === ARMURE ===
let armorProtection = 0;
let lastArmorScore = 0;

// === MANA ===
let fullMana = false;
let lastFullManaScore = 0;

// === BOULES DE FEU ===
let fireballCharges = 3;
const maxFireballCharges = 5;
let lastFireballScore = 0;

// === BOSS ===
let bossActive = false;
let bossAttackCount = 0;
let bossProjectiles = [];

// === IMAGES ===
const manaImg = new Image();
manaImg.src = "assets/img/game/MANA.png";

const backgroundImg = new Image();
backgroundImg.src = "assets/img/quidditch.png";

const playerImg = new Image();
playerImg.src = "assets/img/filters/jeuB.png";
const playerImg2 = new Image();
playerImg2.src = "assets/img/game/pBoule.png";

const pArmorImg = new Image();
pArmorImg.src = "assets/img/game/pArmor.png";
const pArmorImg2 = new Image();
pArmorImg2.src = "assets/img/game/AFB.png";

const armorImg = new Image();
armorImg.src = "assets/img/game/armor.png";

const fireballImg = new Image();
fireballImg.src = "assets/img/game/FB.png";

const explosionImg = new Image();
explosionImg.src = "assets/img/game/explo.png";

const bossImage = new Image();
bossImage.src = "assets/img/game/boss.png";
const bossImg2 = new Image();
bossImg2.src = "assets/img/game/bossAttack.png";

const enemyImgs = [
  "assets/img/filters/FOG(1).png",
  "assets/img/filters/hibouxG.png",
  "assets/img/filters/jeuM.png",
  "assets/img/game/E2.png",
].map((src) => {
  const img = new Image();
  img.src = src;
  return img;
});

// === JOUEUR ===
let player = {
  x: 50,
  y: height / 2 - 50,
  width: 100,
  height: 100,
  speed: 5,
};

// === boss ===
const boss = {
  x: width - 150,
  y: height / 2 - 75,
  width: 300,
  height: 300,
  speed: 8,
};

// === GESTION DES TOUCHES ===
let keys = {};
window.addEventListener("keydown", (e) => (keys[e.key] = true));
window.addEventListener("keyup", (e) => (keys[e.key] = false));

// === BOUCLE DE JEU PRINCIPALE ===
function gameLoop() {
  // Nettoyage du canvas
  if (gameOver) {
    return;
  }
  ctx.clearRect(0, 0, width, height);

  // Fond qui défile
  ctx.drawImage(backgroundImg, -(frameCount % width), 0, width, height);
  ctx.drawImage(backgroundImg, width - (frameCount % width), 0, width, height);
  frameCount += backgroundSpeed;

  // Affichage texte
  ctx.fillStyle = "white";
  ctx.font = "30px Arial";
  ctx.textAlign = "center";
  ctx.fillText("Score : " + score, width / 2, 40);
  ctx.fillText("Boules de feu : " + fireballCharges, width / 2, 80);
  if (armorProtection > 0) {
    ctx.fillText(
      "Armure : " + (armorProtection > 0 ? armorProtection : "Aucune"),
      width / 2,
      120
    );
  }

  // Mouvement joueur
  if ((keys["ArrowUp"] || keys["w"]) && player.y > 0) player.y -= player.speed;
  if ((keys["ArrowDown"] || keys["s"]) && player.y + player.height < height)
    player.y += player.speed;
  if ((keys["ArrowLeft"] || keys["a"]) && player.x > 0)
    player.x -= player.speed;
  if ((keys["ArrowRight"] || keys["d"]) && player.x + player.width < width)
    player.x += player.speed;

  // Lancer de boules de feu
  if (
    (keys[" "] || keys["Space"]) &&
    (fireballCharges > 0 || fullMana) &&
    !keys["_fireballPressed"]
  ) {
    fireballs.push({
      x: player.x + player.width,
      y: player.y + player.height / 2 - 10,
      width: 50,
      height: 50,
      speed: player.speed + 5,
    });
    if (!fullMana) fireballCharges--;
    // fireballCharges--;
    keys["_fireballPressed"] = true;
  }
  if (!(keys[" "] || keys["Space"])) keys["_fireballPressed"] = false;
  drawPlayer();
  // Affichage de l'effet de mana
  if (fullMana) {
    const manaEffect = document.getElementById("manaEffect");
    manaEffect.classList.remove("hidden");
    manaEffect.style.left = `${player.x + player.width / 2 - 30}px`;
    manaEffect.style.top = `${player.y + player.height / 2 - 30}px`;
  } else {
    document.getElementById("manaEffect").classList.add("hidden");
  }
  // Boules de feu
  fireballs.forEach((fireball, fIndex) => {
    fireball.x += fireball.speed;
    ctx.drawImage(
      fireballImg,
      fireball.x,
      fireball.y,
      fireball.width,
      fireball.height
    );

    enemies.forEach((enemy, eIndex) => {
      if (
        fireball.x < enemy.x + enemy.width &&
        fireball.x + fireball.width > enemy.x &&
        fireball.y < enemy.y + enemy.height &&
        fireball.y + fireball.height > enemy.y
      ) {
        explosions.push({
          x: enemy.x + enemy.width / 2 - 40,
          y: enemy.y + enemy.height / 2 - 40,
          width: 80,
          height: 80,
          timer: 15,
        });
        enemies.splice(eIndex, 1);
        fireballs.splice(fIndex, 1);
        score++;
      }
    });

    if (fireball.x > width) fireballs.splice(fIndex, 1);
  });

  // Explosions
  explosions.forEach((explosion, index) => {
    ctx.drawImage(
      explosionImg,
      explosion.x,
      explosion.y,
      explosion.width,
      explosion.height
    );
    explosion.timer--;
    if (explosion.timer <= 0) explosions.splice(index, 1);
  });

  // Apparition des ennemis
  enemySpawnTimer++;
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

  // Mouvements des ennemis + collisions
  enemies.forEach((enemy, index) => {
    enemy.x -= eSpeed;
    ctx.drawImage(enemy.img, enemy.x, enemy.y, enemy.width, enemy.height);

    const collide =
      enemy.x < player.x + player.width &&
      enemy.x + enemy.width > player.x &&
      enemy.y < player.y + player.height &&
      enemy.y + enemy.height > player.y;

    if (collide && !gameOver) {
      if (armorProtection > 0) {
        armorProtection--;
        enemies.splice(index, 1);
        explosions.push({
          x: enemy.x + enemy.width / 2 - 40,
          y: enemy.y + enemy.height / 2 - 40,
          width: 80,
          height: 80,
          timer: 15,
        });
      } else {
        itsGameOver(animationId);
        return;
      }
    }
    // Supprimer les ennemis sortis de l'écran
    if (enemy.x + enemy.width < 0) {
      enemies.splice(index, 1);
      score++;
    }
  });

  // Apparition armure
  if (score > 0 && score % 20 === 0 && score !== lastArmorScore && !armor) {
    if (Math.random() < 0.5) {
      const randomX = width * (Math.random() * 0.5 + 0.25); // entre 0.25 et 0.75
      armor = {
        x: randomX,
        y: -80,
        width: 60,
        height: 60,
        speed: Math.random() * 7 + 3,
      };
      lastArmorScore = score;
    }
  }

  // Déplacement / ramassage armure
  if (armor) {
    armor.y += armor.speed;
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
      ctx.drawImage(armorImg, armor.x, armor.y, armor.width, armor.height);
    }
  }

  // Apparition potion de mana
  if (score > 0 && score % 15 === 0 && !potion && score !== lastFullManaScore) {
    if (Math.random() < 0.5) {
      const randomX = width * (Math.random() * 0.5 + 0.25);
      potion = {
        x: randomX,
        y: -80,
        width: 60,
        height: 60,
        speed: Math.random() * 7 + 3,
      };
      lastFullManaScore = score;
    }
  }

  // Déplacement / ramassage potion de mana
  if (potion) {
    potion.y += potion.speed;
    const collidesWithPlayer =
      potion.x < player.x + player.width &&
      potion.x + potion.width > player.x &&
      potion.y < player.y + player.height &&
      potion.y + potion.height > player.y;

    if (collidesWithPlayer) {
      fireballCharges = 5;
      fullMana = true;
      // tant que fullMana est vrai, je veux une animation css effet de flamme derriere le joueur
      const playerElement = document.getElementById("gameCanvas");
      playerElement.classList.add("full-mana");
      setTimeout(() => {
        playerElement.classList.remove("full-mana");
      }, 10000);
      // réinitialiser fullMana après 10 secondes
      setTimeout(() => {
        fullMana = false;
      }, 10000);
      potion = null;
    } else if (potion.y > height) {
      potion = null;
    } else {
      ctx.drawImage(manaImg, potion.x, potion.y, potion.width, potion.height);
    }
  }
  // Mise à jour de la difficulté et relance de la boucle
  updateSpeedBasedOnScore();

  // Affichage du boss
  if (!bossActive && score === 150) {
    bossActive = true;
    pauseGame();
    setTimeout(startBossPhase, 4200); // Petit délai pour le style
    showBossMessage(ctx, width, height);
    return; // Stop la boucle ici pour la phase boss
  }
  animationId = requestAnimationFrame(gameLoop);
}

// === ÉVÈNEMENTS ===
window.addEventListener("resize", () => {
  width = window.innerWidth;
  height = window.innerHeight;
  canvas.width = width;
  canvas.height = height;
});

// === LANCEMENT DU JEU ===
backgroundImg.onload = () => gameLoop();

// === UTILITAIRES ===
function getRandomEnemy() {
  return enemyImgs[Math.floor(Math.random() * enemyImgs.length)];
}

function resetGame() {
  enemies = [];
  fireballs = [];
  explosions = [];
  armor = null;
  potion = null;
  armorProtection = 0;
  player = {
    x: 50,
    y: height / 2 - 50,
    width: 100,
    height: 100,
    speed: 5,
  };
  score = 0;
  gameOver = false;
  frameCount = 0;
  lastSpeedUpdateScore = 0;
  lastFullManaScore = 0;
  lastArmorScore = 0;
  lastFireballScore = 0;
  enemySpawnTimer = 120;
  fireballCharges = maxFireballCharges;
  backgroundSpeed = 2;
  eSpeed = 4;
  spawnInterval = 120;
  fullMana = false;

  bossActive = false;
  bossAttackCount = 0;
  bossProjectiles = [];
  gameLoop();
}

function updateSpeedBasedOnScore() {
  if (score > 0 && score % 10 === 0 && score !== lastFireballScore) {
    if (fireballCharges < maxFireballCharges) {
      fireballCharges++;
      lastFireballScore = score;
    }
  }

  if (score <= 90 && score >= 10 && score >= lastSpeedUpdateScore + 10) {
    lastSpeedUpdateScore = score;
    player.speed = 5 + Math.floor(score / 10);
    backgroundSpeed = 2 + Math.floor(score / 10);
    eSpeed = backgroundSpeed + 3;
    spawnInterval = Math.max(20, 120 - Math.floor(score * 1.5));
  }

  if (score > 190) {
    player.speed = 18;
    backgroundSpeed = 16;
    eSpeed = 20;
    spawnInterval = 15;
  }
}

// === BOSS PHASE ===
function pauseGame() {
  enemies = [];
  fireballs = [];
  explosions = [];
}

function startBossPhase() {
  bossAttackCount = 0;
  bossProjectiles = [];
  requestAnimationFrame(bossLoop);
}

function bossLoop() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  // Fond statique
  ctx.drawImage(backgroundImg, 0, 0, canvas.width, canvas.height);
  if ((keys["ArrowUp"] || keys["w"]) && player.y > 0) player.y -= player.speed;
  if ((keys["ArrowDown"] || keys["s"]) && player.y + player.height < height)
    player.y += player.speed;
  if ((keys["ArrowLeft"] || keys["a"]) && player.x > 0)
    player.x -= player.speed;
  if ((keys["ArrowRight"] || keys["d"]) && player.x + player.width < width)
    player.x += player.speed;

  // Affiche joueur et boss
  drawPlayer();
  drawAndMoveBoss();
  // Gère les projectiles du boss
  updateBossProjectiles();

  // Collision joueur <-> tir du boss
  for (let i = 0; i < bossProjectiles.length; i++) {
    if (isColliding(bossProjectiles[i], player)) {
      itsGameOver(animationId);
      return;
    }
  }
  // Collision joueur <-> boss
  if (isColliding(boss, player)) {
    itsGameOver(animationId);
    return;
  }

  if (bossAttackCount >= 300) {
    score += 1; // Bonus de score à la fin de la phase boss
    bossActive = false;
    cancelAnimationFrame(animationId);
    requestAnimationFrame(gameLoop); // Reprise du jeu normal
    return;
  }

  requestAnimationFrame(bossLoop);
}

function drawAndMoveBoss() {
  if (bossActive) {
    // Mouvement horizontal du boss
    if (boss.x > canvas.width - boss.width) {
      boss.x -= boss.speed;
    } else {
      boss.x = canvas.width - boss.width;
    }
    const verticalOffset = 45; // Décalage en pixels vers le haut
    // Mouvement vertical du boss avec un peu d'aléatoire
    const randomOffset = Math.random() * 30 - 15; // entre -15 et +15
    const targetY = player.y - verticalOffset + randomOffset;

    if (boss.y < targetY) {
      boss.y += boss.speed * (0.8 + Math.random() * 0.4);
    } else if (boss.y > targetY) {
      boss.y -= boss.speed * (0.8 + Math.random() * 0.4);
    }
    // Alterne l'image du boss toutes les secondes
    const bossImgToDraw =
      Math.floor(Date.now() / 1000) % 2 === 0 ? bossImage : bossImg2;
    ctx.drawImage(bossImgToDraw, boss.x, boss.y, boss.width, boss.height);
  }
}

function drawPlayer() {
  let currentPlayerImg = playerImg;
  if (armorProtection > 0)
    currentPlayerImg = fireballs.length > 0 ? pArmorImg2 : pArmorImg;
  else currentPlayerImg = fireballs.length > 0 ? playerImg2 : playerImg;

  ctx.drawImage(
    currentPlayerImg,
    player.x,
    player.y,
    player.width,
    player.height
  );
}

function updateBossProjectiles() {
  if (bossAttackCount <= 300) {
    if (Math.random() < 0.06) {
      bossProjectiles.push({
        x: boss.x,
        y: boss.y + boss.height / 2 - 10,
        width: 20,
        height: 20,
        speed: -8,
      });
      bossAttackCount++;
    }
  }

  for (let i = 0; i < bossProjectiles.length; i++) {
    drawBossProjectiles(i, bossProjectiles, ctx);
  }
}

function itsGameOver(IdToStop) {
  gameOver = true;
  cancelAnimationFrame(IdToStop);
  captureFinishPhotoOfCanvas();
}

function captureFinishPhotoOfCanvas() {
  canvas.toBlob((blob) => {
    if (!blob) {
      return showRestartDialog(score).then((restart) => {
        if (restart) resetGame();
        else window.location.href = "/index.php";
      });
    }

    const modal = document.getElementById("photoConfirmModal");
    const yesBtn = document.getElementById("confirmYesBtn");
    const noBtn = document.getElementById("confirmNoBtn");

    modal.classList.remove("hidden");

    const cleanupAndRestart = () => {
      modal.classList.add("hidden");
      showRestartDialog(score).then((restart) => {
        if (restart) resetGame();
        else window.location.href = "/index.php";
      });
    };

    yesBtn.onclick = () => {
      modal.classList.add("hidden");

      const formData = new FormData();
      const photoName = "capture_" + Date.now() + ".png";
      formData.append("file", blob, photoName);
      formData.append("type", "photo");

      fetch("/uploadPhotoFromGame.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then(() => cleanupAndRestart())
        .catch((err) => {
          console.error("Erreur d'envoi :", err);
          cleanupAndRestart();
        });
    };

    noBtn.onclick = () => cleanupAndRestart();
  }, "image/png");
}
