(function () {
  const STORAGE_KEY = "pref-theme";
  const root = document.documentElement;
  const toggleBtnId = "themeToggle";

  function prefersDark() {
    return (
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
    );
  }
  function prefersReducedMotion() {
    return (
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches
    );
  }

  function getSavedTheme() {
    try {
      return localStorage.getItem(STORAGE_KEY);
    } catch {
      return null;
    }
  }
  function saveTheme(theme) {
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch {
      /* ignore */
    }
  }

  function apply(theme) {
    if (theme === "dark") {
      root.setAttribute("data-theme", "dark");
      document.documentElement.style.colorScheme = "dark";
    } else {
      root.removeAttribute("data-theme");
      document.documentElement.style.colorScheme = "light";
    }
    const btn = document.getElementById(toggleBtnId);
    if (btn) {
      btn.setAttribute("aria-pressed", theme === "dark" ? "true" : "false");
      btn.dataset.theme = theme; // CSS uses this to swap icons
    }
    updateLabel(theme);
  }

  // CURRENT MODE label (Dark when dark, Light when light)
  function updateLabel(theme) {
    const btn = document.getElementById(toggleBtnId);
    if (!btn) return;
    const labelSpan = btn.querySelector(".label");
    if (labelSpan) {
      labelSpan.textContent = theme === "dark" ? "Dark" : "Light";
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

  function toggleTheme() {
    const next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
    if (!prefersReducedMotion()) document.body.classList.add("theme-switching");
    apply(next);
    saveTheme(next);
    if (!prefersReducedMotion()) {
      setTimeout(() => document.body.classList.remove("theme-switching"), 220);
    }
  }

  function init() {
    const saved = getSavedTheme();
    const initial = saved || (prefersDark() ? "dark" : "light");
    apply(initial);

    const btn = document.getElementById(toggleBtnId);
    if (btn) {
      btn.addEventListener("click", toggleTheme);
      btn.addEventListener("keydown", (e) => {
        if (e.key === " " || e.key === "Enter") {
          e.preventDefault();
          toggleTheme();
        }
      });
      btn.setAttribute("tabindex", "0");
      btn.setAttribute("role", "button");
    }

    // Follow system changes only if no saved preference
    if (!saved && window.matchMedia) {
      const mq = window.matchMedia("(prefers-color-scheme: dark)");
      const handler = (e) => apply(e.matches ? "dark" : "light");
      try {
        mq.addEventListener("change", handler);
      } catch {
        mq.addListener(handler);
      }
    }
  }

  // Smooth transition + icon swap rules
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

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
