(function () {
  function wire(container) {
    // Find a target input inside this container
    const input =
      container.querySelector('input[data-toggle="password"]') ||
      container.querySelector('input[type="password"]');

    if (!input) return;

    // Find an existing toggle button or create one
    let btn = container.querySelector(".toggle-visibility");
    if (!btn) {
      btn = document.createElement("button");
      btn.type = "button";
      btn.className = "btn btn-outline-secondary toggle-visibility";
      btn.setAttribute("aria-label", "Show password");
      btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
      // If using Bootstrap input-group, append after input. Otherwise just append to container.
      if (container.classList.contains("input-group")) {
        container.appendChild(btn);
      } else {
        // Keep spacing when not inside input-group
        btn.style.marginLeft = "0.5rem";
        container.appendChild(btn);
      }
    }

    function setState(show) {
      input.type = show ? "text" : "password";
      btn.setAttribute("aria-label", show ? "Hide password" : "Show password");
      const icon = btn.querySelector("i");
      if (icon) {
        icon.classList.toggle("fa-eye", !show);
        icon.classList.toggle("fa-eye-slash", show);
      }
    }

    // Initialize based on current type
    setState(false);

    btn.addEventListener("click", () => {
      const isHidden = input.type === "password";
      setState(isHidden);
      input.focus({ preventScroll: true });
      // Place caret at end (useful on some browsers)
      const len = input.value.length;
      input.setSelectionRange(len, len);
    });
  }

  function init() {
    // Support both patterns:
    // - .password-toggle wrappers (recommended)
    // - .input-group.password-toggle wrappers
    document.querySelectorAll(".password-toggle").forEach(wire);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
