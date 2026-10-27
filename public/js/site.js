(() => {
  const path = (window.location.pathname.split("/").pop() || "index.html").toLowerCase();

  const pages = [
    { href: "index.html", label: "Home", match: ["index.html", ""] },
    {
      label: "Società",
      children: [
        { href: "societa.html", label: "Società" },
        { href: "organigramma.html", label: "Organigramma" },
        { href: "albo-oro.html", label: "Albo d'oro" },
        { href: "pubblicazioni.html", label: "Pubblicazioni L.124" },
      ],
    },
    { href: "team.html", label: "Team" },
    {
      label: "Agonistica",
      children: [
        { href: "gare.html", label: "Gare" },
        { href: "gaf.html", label: "GAF" },
        { href: "gam.html", label: "GAM" },
      ],
    },
    { href: "corsi.html", label: "Corsi" },
    { href: "contatti.html", label: "Contatti" },
    { href: "lavora-con-noi.html", label: "Lavora con noi" },
    { href: "eventi.html", label: "Eventi" },
    { href: "safeguarding.html", label: "Safeguarding" },
  ];

  function isCurrent(href) {
    return path === href.toLowerCase() || (path === "" && href === "index.html");
  }

  function navHtml() {
    return pages
      .map((item) => {
        if (item.children) {
          const open = item.children.some((c) => isCurrent(c.href)) ? " open" : "";
          const links = item.children
            .map(
              (c) =>
                `<a href="${c.href}"${isCurrent(c.href) ? ' aria-current="page"' : ""}>${c.label}</a>`
            )
            .join("");
          return `<details class="nav-group"${open}><summary>${item.label}</summary><div class="nav__panel">${links}</div></details>`;
        }
        return `<a href="${item.href}"${isCurrent(item.href) ? ' aria-current="page"' : ""}>${item.label}</a>`;
      })
      .join("");
  }

  const header = document.getElementById("site-header");
  if (header) {
    header.innerHTML = `
      <div class="container site-header__inner">
        <a class="brand" href="index.html" aria-label="Ginnastica Meda home">
          <img class="brand__logo" src="images/logo/logo-mark-menu.svg" alt="Ginnastica Meda" width="48" height="47" />
          <div class="brand__text">Ginnastica<span>Meda</span></div>
        </a>
        <button class="nav-toggle" type="button" aria-label="Apri menu" aria-expanded="false">
          <span></span><span></span><span></span>
        </button>
        <nav class="nav" id="site-nav" aria-label="Navigazione principale">${navHtml()}</nav>
      </div>`;

    const toggle = header.querySelector(".nav-toggle");
    const nav = header.querySelector("#site-nav");
    toggle?.addEventListener("click", () => {
      const open = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", String(open));
    });

    header.querySelectorAll("details").forEach((d) => {
      d.addEventListener("toggle", () => {
        if (d.open) {
          header.querySelectorAll("details").forEach((other) => {
            if (other !== d) other.open = false;
          });
        }
      });
    });
  }

  const footer = document.getElementById("site-footer");
  if (footer) {
    footer.innerHTML = `
      <div class="tricolor"></div>
      <div class="container footer-grid" style="padding-top:2rem">
        <div>
          <h3>A.S.D. Ginnastica Meda</h3>
          <p>Associazione sportiva dilettantistica affiliata alla Federazione Ginnastica d'Italia. Centro Federale di Alta Specializzazione dal 1986.</p>
        </div>
        <div>
          <h3>Contatti</h3>
          <ul>
            <li>Via Caduti Medesi 13, 20821 Meda (MB)</li>
            <li><a href="tel:+39036275844">0362 75844</a></li>
            <li><a href="mailto:info@ginnasticameda.it">info@ginnasticameda.it</a></li>
            <li>Segreteria: Lun–Ven 18:30–20:00</li>
          </ul>
        </div>
        <div>
          <h3>Seguici</h3>
          <ul>
            <li><a href="https://www.instagram.com/ginnasticameda_official/" target="_blank" rel="noopener">Instagram</a></li>
            <li><a href="safeguarding.html">Safeguarding</a></li>
            <li><a href="lavora-con-noi.html">Lavora con noi</a></li>
            <li><a href="pubblicazioni.html">Pubblicazioni L.124/2017</a></li>
          </ul>
        </div>
      </div>
      <div class="container footer-copy">
        <span>© ${new Date().getFullYear()} Ginnastica Meda — P. IVA 02327880965</span>
        <span>PEC: 000382@pec.federginnastica.it</span>
      </div>`;
  }

  const sponsors = document.getElementById("sponsors-track");
  if (sponsors) {
    const logos = [
      { src: "images/sponsors/mtm.png", alt: "MTM" },
      { src: "images/sponsors/mtm-alt.png", alt: "MTM Shop" },
      { src: "images/sponsors/coren.jpeg", alt: "Coren" },
      { src: "images/sponsors/emotion.png", alt: "E motion Meda" },
      { src: "images/sponsors/nobili.png", alt: "Nobili Ferramenta" },
      { src: "images/sponsors/vefer.png", alt: "Vefer poliuretani" },
      { src: "images/sponsors/albero-gelati.png", alt: "Albero dei Gelati" },
      { src: "images/sponsors/freddy.png", alt: "Freddy" },
      { src: "images/sponsors/ventura.png", alt: "Ventura" },
      { src: "images/sponsors/download-2.png", alt: "Vodafone Seregno" },
      { src: "images/sponsors/download-4.png", alt: "Caiuli Carne" },
      { src: "images/sponsors/download-6.png", alt: "Arco Mobili" },
      { src: "images/sponsors/download-7.png", alt: "La Costa" },
      { src: "images/sponsors/download-9.png", alt: "Vallimar Graniti" },
      { src: "images/sponsors/download-10.png", alt: "Junior B Meda" },
      { src: "images/sponsors/download-11.png", alt: "Osteria Oss Bus" },
      { src: "images/sponsors/download-12.png", alt: "Auxologico Meda" },
      { src: "images/sponsors/images.png", alt: "Farmacia Cinzia Re" },
      { src: "images/sponsors/images-1.png", alt: "Sigoa" },
      { src: "images/sponsors/download.jpg", alt: "Music Center" },
      { src: "images/sponsors/download-1.jpg", alt: "MTS" },
      { src: "images/sponsors/download-2.jpg", alt: "Panetteria Più del Pane" },
      { src: "images/sponsors/download-3.jpg", alt: "Pasticceria Centrale" },
    ];
    const html = logos
      .map((l) => `<img src="${l.src}" alt="${l.alt}" title="${l.alt}" />`)
      .join("");
    sponsors.innerHTML = html + html;
  }

  const reveals = document.querySelectorAll(".reveal");
  if (reveals.length && "IntersectionObserver" in window) {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add("is-visible");
            io.unobserve(e.target);
          }
        });
      },
      { threshold: 0.12 }
    );
    reveals.forEach((el) => io.observe(el));
  } else {
    reveals.forEach((el) => el.classList.add("is-visible"));
  }
})();
