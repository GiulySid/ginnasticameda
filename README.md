# A.S.D. Ginnastica Meda — sito statico

Sito statico modernizzato (stessa struttura del sito Wix, palette e loghi originali).

## Avvio locale

```bash
npm start
```

Apri [http://127.0.0.1:4321/](http://127.0.0.1:4321/).

Richiede Node.js 18+ (`node scripts/serve.mjs`).

## Struttura

- `public/` — sito pubblicato (HTML, CSS, JS, immagini, PDF)
- `liste dinamiche/` — CSV esportati da Wix
- `site files/` — export media Wix (sorgente)
- `_content/` — testi e mappature raccolte in fase di migrazione

## Deploy

Carica il contenuto di `public/` su Netlify, Cloudflare Pages, GitHub Pages o qualsiasi hosting statico.
Pointa il dominio `ginnasticameda.com` alla nuova hosting quando sei pronto.
