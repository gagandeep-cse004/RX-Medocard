Short summary
This is a static/multi-page website for the "Rx MedoCard" marketing/site frontend with some lightweight backend PHP endpoints under `backend/`. Primary assets are plain HTML, CSS and vanilla JS. Focus areas for edits: layout templates (root HTML files), shared JS in `js/`, and CSS under `css/`.

How to be helpful (do these first)
- Preserve existing markup structure in `index.html`, `Pharmacy.html`, and other top-level HTML files when changing layout/classes. Many pages assume the same header/footer markup and include the same `lib/` and `css/` files.
- When editing JS, prefer updating `js/site3d49.js` for shared behaviour (nav active state, form validation) and `js/*` files for page-specific features (testimonials slider, hero video scripts). Avoid introducing heavy frameworks.
- When adding assets, place them under `images/`, `css/` or `js/` and update references with relative paths as used (most links are relative, e.g., `css/pharmacy1cbb.css`).

Big picture architecture
- Frontend: Multi-page static site (HTML files at repo root) using Bootstrap 5, custom CSS under `css/`, and small vanilla JS modules in `js/`.
- Shared libs: local `lib/bootstrap/` and `lib/jquery/` are included; CDN fallbacks are present in `cdn.jsdelivr.net/` and `cdnjs.cloudflare.com/` folders.
- Backend: Lightweight PHP endpoints exist (`login.php`, `sign.php`) and an empty `backend/` directory intended for server-side logic. Treat backend changes cautiously — PHP likely handles authentication/booking forms.

Developer workflows & debugging notes
- Local dev: The site is designed to run from a webserver. Use XAMPP or any local Apache/PHP server serving `public_html` as the document root. Example (Windows PowerShell):
```powershell
# Start XAMPP Apache (manual in XAMPP Control Panel) and open http://localhost/ or http://localhost/Pharmacy.html
``` 
- Editing flow: open and modify HTML/CSS/JS directly. Many files include cache-busting query strings (e.g. `?v=...`); when testing, clear browser cache or update the query string to see changes.
- Forms and server behaviour: forms in the UI may post to `login.php`, `sign.php`, or endpoints under `backend/`. Without a running PHP server these will 404 — test server-side flows with XAMPP running.

Project-specific conventions & patterns
- Multi-page header/footer reuse: Each page contains full header/footer markup rather than templates. When changing navigation or header classes, update every top-level HTML file (`index.html`, `Pharmacy.html`, `Offers.html`, etc.).
- Naming: CSS files include opaque hash-like suffixes (e.g. `site224e.css`, `pharmacy1cbb.css`) — these are tracked in HTML; search and update references when renaming.
- JS versioning: script tags include `?v=...` for cache-busting. Update the version when making behavioral changes so browsers pick up edits.
- Local vendor copies: Prefer using the included `lib/` and local static copies (CDN folders). Don't change to remote CDN URLs unless necessary; the repo expects local assets.

Integration points & external dependencies
- Google services: meta tags and AdSense script references are present in `index.html` and others — be careful when editing. Ad unit IDs are placeholders and may require real account IDs.
- Fonts & AOS: Google Fonts and AOS (Animate On Scroll) are loaded via external URLs. Changes to animation timings live in `js/site3d49.js` and inline scripts.
- Payment and booking: Payment styles exist (`css/payment74ef.css`) and forms use client-side validation in `js/site3d49.js`. Real payment endpoints are not present — avoid hardcoding secrets or pretending to implement payments without backend.

Examples of common edits
- To change the navigation label or add a link, update `index.html` and `Pharmacy.html` (and other pages) — the nav markup is duplicated across files.
- To improve the testimonials slider, change `initTestimonialsSlider()` inside `js/site3d49.js` (it clones nodes and manages responsive visibleCards). Keep the cloning logic intact when changing DOM structure.
- To add a new CSS utility class, put it in an existing stylesheet like `css/site224e.css` to follow the current pattern where global styles live in `site*` files.

Do NOT do these
- Do not replace the local `lib/` vendor libraries with remote CDNs without updating all consumers. The site expects local copies.
- Do not remove inline cache-busting query strings; instead bump them when you want browsers to fetch updated assets.
- Do not commit production secrets (API keys, payment credentials). The repo has placeholders but no secret store.

Where to look first (key files)
- `index.html`, `Pharmacy.html`, `Offers.html`, `Hospitals.html` — main page templates and repeated header/footer patterns.
- `js/site3d49.js` — shared behaviors (nav active state, form behaviour, testimonials slider).
- `css/site224e.css`, `css/pharmacy1cbb.css`, `css/pharmacy-stepsc896.css` — main styling and page-specific styles.
- `lib/` and `cdn.jsdelivr.net/`, `cdnjs.cloudflare.com/` — vendor libraries included in the repo.
- `login.php`, `sign.php`, `backend/` — server-side entry points; test with XAMPP.

If something is ambiguous
- Ask which pages are critical to change (e.g., homepage vs. membership flow). Note that header/footer repetition means a cross-file update will be needed.
- If backend/API details are required (authentication, payments), request credentials and a running dev server, or provide a stubbed flow that clearly documents the missing pieces.

Next step
- I added this guidance file. Tell me if you want the file tailored for a different workflow (GitHub Actions, CI/CD, or frontend build steps). 