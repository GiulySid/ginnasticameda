const { execFileSync } = require("child_process");
const fs = require("fs");
const path = require("path");

const srcDir = path.join(__dirname, "..", "public", "images", "org", "_full");
const orgOut = path.join(__dirname, "..", "public", "images", "org");
const teamOut = path.join(__dirname, "..", "public", "images", "team");

/** Eye-line Y as fraction of height. Crop keeps head + upper shoulders. */
const eyeY = {
  "angelo-buraschi.png": 0.32,
  "renato-amedeo.jpg": 0.3,
  "fabio-minotti.jpg": 0.32,
  "lorena-salvi.jpg": 0.34,
  "annamaria-radice.jpg": 0.3,
  "paolo-fedeli.jpg": 0.3,
  "giulia-vezzoli.jpg": 0.36,
  "maurizio-allievi.jpg": 0.34,
  "franco-giorgetti.jpg": 0.28,
  "daniele-maj.jpg": 0.28,
  "riccardo-avanzi.jpg": 0.28,
  "lino-spinella.jpg": 0.28,
  "lorenzo-masiero.jpg": 0.4,
  "lella-beretta.jpg": 0.3,
  "cristina-campagner.jpg": 0.4,
  "francesca-sala.jpg": 0.28,
  "francesco-orsetti.jpg": 0.28,
  "giovanni-moscato.jpg": 0.28,
  "elisa-redaelli.jpg": 0.28,
  "filippo-testi.jpg": 0.4,
  "mattia-redaelli.jpg": 0.28,
  "davide-beretta.jpg": 0.28,
  "alessio-dangelo.jpg": 0.38,
  "laura-raini.jpg": 0.4,
  "stefania-sinanaj.jpg": 0.28,
  "alice-viana.jpg": 0.4,
  "serena-arreghini.jpg": 0.28,
  "siria-ostini.jpg": 0.4,
  "eleonora-trabattoni.jpg": 0.4,
};

const eyeX = {
  "paolo-fedeli.jpg": 0.58,
  "giulia-vezzoli.jpg": 0.52,
  "maurizio-allievi.jpg": 0.42,
  "franco-giorgetti.jpg": 0.48,
};

const toTeam = {
  "maurizio-allievi.jpg": "maurizio-allievi.jpg",
  "franco-giorgetti.jpg": "franco-giorgetti.jpg",
  "daniele-maj.jpg": "daniele-maj.jpg",
  "riccardo-avanzi.jpg": "riccardo-avanzi.jpg",
  "lino-spinella.jpg": "lino-spinella.jpg",
  "lella-beretta.jpg": "gabriella-beretta.jpg",
  "francesca-sala.jpg": "francesca-sala.jpg",
  "francesco-orsetti.jpg": "francesco-orsetti.jpg",
  "giovanni-moscato.jpg": "giovanni-moscato.jpg",
  "elisa-redaelli.jpg": "elisa-redaelli.jpg",
};

function probe(file) {
  const out = execFileSync(
    "ffprobe",
    ["-v", "error", "-select_streams", "v:0", "-show_entries", "stream=width,height", "-of", "csv=p=0", file],
    { encoding: "utf8" }
  ).trim();
  const [w, h] = out.split(",").map(Number);
  return { w, h };
}

function cropOne(src, dest, file) {
  const { w, h } = probe(src);
  const ey = eyeY[file] ?? 0.3;
  const ex = eyeX[file] ?? 0.5;

  // Wider head-and-shoulders: ~88% of short side so faces aren't cramped
  let side = Math.round(Math.min(w, h) * 0.88);
  side = Math.min(side, w, h);

  let x = Math.round(ex * w - side / 2);
  // Eyes sit ~35% from top of the square (classic portrait)
  let y = Math.round(ey * h - side * 0.35);
  x = Math.max(0, Math.min(w - side, x));
  y = Math.max(0, Math.min(h - side, y));

  const tmp = dest + ".__tmp__";
  execFileSync(
    "ffmpeg",
    ["-y", "-i", src, "-vf", `crop=${side}:${side}:${x}:${y},scale=480:480:flags=lanczos`, "-q:v", "2", tmp],
    { stdio: "ignore" }
  );
  fs.renameSync(tmp, dest);
  return `${side}@${x},${y}`;
}

for (const file of fs.readdirSync(srcDir)) {
  if (!/\.(jpe?g|png)$/i.test(file)) continue;
  const src = path.join(srcDir, file);
  const info = cropOne(src, path.join(orgOut, file), file);
  let extra = "";
  if (toTeam[file]) {
    fs.copyFileSync(path.join(orgOut, file), path.join(teamOut, toTeam[file]));
    extra = ` -> team/${toTeam[file]}`;
  }
  console.log(`${file}: ${info}${extra}`);
}
