(function () {
  const STORAGE_KEY = "pref-theme"; // key sa localStorage para i-save yung user preference
  const root = document.documentElement; // <html> element
  const toggleBtnId = "themeToggle"; // id ng theme toggle button

  // Check kung system prefers dark mode
  function prefersDark() {
    return (
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
    );
  }

  // Check kung user prefers reduced motion (accessibility)
  function prefersReducedMotion() {
    return (
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches
    );
  }

  // Kunin yung previously saved theme sa localStorage
  function getSavedTheme() {
    try {
      return localStorage.getItem(STORAGE_KEY);
    } catch {
      return null; // ignore kung may error
    }
  }

  // I-save yung theme preference sa localStorage
  function saveTheme(theme) {
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch {
      /* ignore errors */
    }
  }

  // Apply yung theme sa page
  function apply(theme) {
    if (theme === "dark") {
      root.setAttribute("data-theme", "dark"); // para sa CSS [data-theme="dark"]
      document.documentElement.style.colorScheme = "dark"; // native dark mode support
    } else {
      root.removeAttribute("data-theme");
      document.documentElement.style.colorScheme = "light";
    }

    // Update toggle button state
    const btn = document.getElementById(toggleBtnId);
    if (btn) {
      btn.setAttribute("aria-pressed", theme === "dark" ? "true" : "false");
      btn.dataset.theme = theme; // gagamitin ng CSS para sa icon swap
    }
    updateLabel(theme); // update label text at aria attributes
  }

  // Update yung button label at accessibility attributes
  function updateLabel(theme) {
    const btn = document.getElementById(toggleBtnId);
    if (!btn) return;
    const labelSpan = btn.querySelector(".label");
    if (labelSpan) {
      labelSpan.textContent = theme === "dark" ? "Dark" : "Light"; // text na makikita ng user
    }
    btn.setAttribute(
      "aria-label",
      theme === "dark"
        ? "Current theme: Dark. Toggle theme"
        : "Current theme: Light. Toggle theme"
    );
    btn.setAttribute(
      "title",
      theme === "dark" ? "Theme: Dark" : "Theme: Light"
    );
  }

  // Function na magta-toggle ng theme kapag kinlick
  function toggleTheme() {
    const next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
    if (!prefersReducedMotion()) document.body.classList.add("theme-switching"); // smooth transition
    apply(next);
    saveTheme(next); // save preference
    if (!prefersReducedMotion()) {
      setTimeout(() => document.body.classList.remove("theme-switching"), 220);
    }
  }

  // Initialize page with theme
  function init() {
    const saved = getSavedTheme(); // kunin saved theme
    const initial = saved || (prefersDark() ? "dark" : "light"); // default sa system preference
    apply(initial);

    const btn = document.getElementById(toggleBtnId);
    if (btn) {
      // Click listener para toggle
      btn.addEventListener("click", toggleTheme);

      // Keyboard accessibility: space at enter key
      btn.addEventListener("keydown", (e) => {
        if (e.key === " " || e.key === "Enter") {
          e.preventDefault();
          toggleTheme();
        }
      });

      // Make it focusable and role=button for screen readers
      btn.setAttribute("tabindex", "0");
      btn.setAttribute("role", "button");
    }

    // Follow system theme changes kung walang saved preference
    if (!saved && window.matchMedia) {
      const mq = window.matchMedia("(prefers-color-scheme: dark)");
      const handler = (e) => apply(e.matches ? "dark" : "light");
      try {
        mq.addEventListener("change", handler);
      } catch {
        mq.addListener(handler); // fallback sa old browsers
      }
    }
  }

  // Add style rules para smooth transition at icon swap
  const style = document.createElement("style");
  style.innerHTML = `
    .theme-switching, .theme-switching * {
      transition: color .2s ease, background-color .2s ease, border-color .2s ease, box-shadow .2s ease !important;
    }
    #${toggleBtnId} .icon-sun { display: inline-flex; }
    #${toggleBtnId} .icon-moon { display: none; }
    #${toggleBtnId}[data-theme="dark"] .icon-sun { display: none; }
    #${toggleBtnId}[data-theme="dark"] .icon-moon { display: inline-flex; }
  `;
  document.head.appendChild(style);

  // Initialize kapag ready na ang DOM
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
