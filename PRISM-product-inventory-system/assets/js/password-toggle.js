(function () {
  // Function na magse-setup ng show/hide password toggle sa isang container
  function wire(container) {
    // Hanapin ang password input sa loob ng container
    const input =
      container.querySelector('input[data-toggle="password"]') ||
      container.querySelector('input[type="password"]');

    if (!input) return; // kung walang input, exit agad

    // Hanapin kung may existing toggle button, kung wala, gagawa tayo
    let btn = container.querySelector(".toggle-visibility");
    if (!btn) {
      btn = document.createElement("button");
      btn.type = "button";
      btn.className = "btn btn-outline-secondary toggle-visibility";
      btn.setAttribute("aria-label", "Show password");
      btn.innerHTML = '<i class="fa-solid fa-eye"></i>';

      // Kung nasa Bootstrap input-group, i-append after input
      if (container.classList.contains("input-group")) {
        container.appendChild(btn);
      } else {
        // Kung hindi, lagyan ng konting space sa left para hindi dikit
        btn.style.marginLeft = "0.5rem";
        container.appendChild(btn);
      }
    }

    // Function para mag-set ng current state ng password visibility
    function setState(show) {
      input.type = show ? "text" : "password"; // toggle input type
      btn.setAttribute("aria-label", show ? "Hide password" : "Show password"); // accessibility
      const icon = btn.querySelector("i");
      if (icon) {
        icon.classList.toggle("fa-eye", !show); // eye icon kapag nakatago
        icon.classList.toggle("fa-eye-slash", show); // eye-slash kapag nakikita
      }
    }

    // Initialize state (hidden by default)
    setState(false);

    // Event listener para sa toggle button
    btn.addEventListener("click", () => {
      const isHidden = input.type === "password";
      setState(isHidden); // toggle visibility
      input.focus({ preventScroll: true }); // i-focus ulit input
      // Ilagay caret sa dulo (useful sa ibang browsers)
      const len = input.value.length;
      input.setSelectionRange(len, len);
    });
  }

  // Function para i-initialize lahat ng password toggles sa page
  function init() {
    // Suportado ang dalawang patterns:
    // - .password-toggle wrappers (recommended)
    // - .input-group.password-toggle wrappers
    document.querySelectorAll(".password-toggle").forEach(wire);
  }

  // Check kung ready na ang DOM bago mag-initialize
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
