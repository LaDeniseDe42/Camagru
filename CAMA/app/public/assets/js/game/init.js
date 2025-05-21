// === INITIALISATION DU CANVAS ===
export const canvas = document.getElementById("gameCanvas");
export const ctx = canvas.getContext("2d");
export let width = window.innerWidth;
export let height = window.innerHeight;
canvas.width = width;
canvas.height = height;

// === VARIABLES DE JEU ===
export let score = 0;
export let animationId;
export let gameOver = false;
export let frameCount = 0;

// === PARAMÈTRES DE DIFFICULTÉ ===
export let eSpeed = 4;
export let backgroundSpeed = 2;
export let spawnInterval = 120;
export let enemySpawnTimer = 120;
export let lastSpeedUpdateScore = 0;

// === ENTITÉS DU JEU ===
export let enemies = [];
export let fireballs = [];
export let explosions = [];
export let armor = null;

// === ARMURE ===
export let armorProtection = 0;
export let lastArmorScore = 0;

// === BOULES DE FEU ===
export let fireballCharges = 3;
export const maxFireballCharges = 3;
export let lastFireballScore = 0;

// === IMAGES ===
export const backgroundImg = new Image();
backgroundImg.src = "assets/img/quidditch.png";

export const playerImg = new Image();
playerImg.src = "assets/img/filters/jeuB.png";
export const playerImg2 = new Image();
playerImg2.src = "assets/img/game/pBoule.png";

export const pArmorImg = new Image();
pArmorImg.src = "assets/img/game/pArmor.png";
export const pArmorImg2 = new Image();
pArmorImg2.src = "assets/img/game/AFB.png";

export const armorImg = new Image();
armorImg.src = "assets/img/game/armor.png";

export const fireballImg = new Image();
fireballImg.src = "assets/img/game/FB.png";

export const explosionImg = new Image();
explosionImg.src = "assets/img/game/explo.png";

export const enemyImgs = [
  "assets/img/filters/FOG(1).png",
  "assets/img/filters/hibouxG.png",
  "assets/img/filters/jeuM.png",
].map((src) => {
  const img = new Image();
  img.src = src;
  return img;
});

// === JOUEUR ===
export const player = {
  x: 50,
  y: height / 2 - 50,
  width: 100,
  height: 100,
  speed: 5,
};
