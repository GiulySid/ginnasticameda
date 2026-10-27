# A.S.D. Ginnastica Meda

Sito statico + area admin PHP (per hosting Aruba).

## Avvio locale (solo frontend)

```bash
npm start
```

Apri [http://127.0.0.1:4321/](http://127.0.0.1:4321/).

Per l’admin serve PHP (es. `php -S 127.0.0.1:8080 -t public` dalla root del progetto), poi [http://127.0.0.1:8080/admin/](http://127.0.0.1:8080/admin/).

## Struttura

- `public/` — contenuto da caricare su Aruba (document root)
- `public/admin/` — pannello per pubblicazioni, gare, GAF, GAM
- `public/data/` — JSON modificabili dall’admin
- `public/js/data.js` — generato automaticamente a ogni salvataggio admin

## Admin (Aruba)

1. Carica tutto il contenuto di `public/` nella document root.
2. Assicurati che `data/` e `js/` siano **scrivibili** dal PHP (chmod 755/775 sulle cartelle).
3. Apri `https://tuodominio/admin/` e crea username/password al primo accesso.
4. Da lì aggiorni pubblicazioni, gare, GAF e GAM: il sito pubblico legge `js/data.js`.

Le credenziali restano in `admin/credentials.php` (non va in git).
