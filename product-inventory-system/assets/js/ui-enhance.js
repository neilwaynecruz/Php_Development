// Enhances alerts: adds icons, entrance animation, and gentle auto-dim
(function () {
  const ICONS = {
    success: "fa-circle-check",
    danger: "fa-circle-xmark",
    warning: "fa-triangle-exclamation",
    info: "fa-circle-info",
  };

  function enhanceAlerts() {
    document.querySelectorAll(".alert").forEach((alert) => {
      // Add entrance animation
      alert.classList.add("alert-animate", "alert-dim");

      // If it already has an icon, skip
      if (!alert.querySelector(".alert-icon")) {
        const span = document.createElement("span");
        span.className = "alert-icon";
        const i = document.createElement("i");
        i.className = "fa-solid";

        if (alert.classList.contains("alert-success"))
          i.classList.add(ICONS.success);
        else if (alert.classList.contains("alert-danger"))
          i.classList.add(ICONS.danger);
        else if (alert.classList.contains("alert-warning"))
          i.classList.add(ICONS.warning);
        else i.classList.add(ICONS.info);

        span.appendChild(i);

        // Wrap existing content in .alert-content for better spacing
        const content = document.createElement("div");
        content.className = "alert-content";
        while (alert.firstChild) content.appendChild(alert.firstChild);
        alert.appendChild(span);
        alert.appendChild(content);
      }

      // Gentle auto-dim after a short delay (non-intrusive)
      setTimeout(() => alert.classList.add("dimmed"), 3500);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", enhanceAlerts);
  } else {
    enhanceAlerts();
  }
})();
