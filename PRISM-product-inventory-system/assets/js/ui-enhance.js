// Enhances alerts: adds icons, entrance animation, and gentle auto-dim
(function () {
  // Icon mapping per alert type
  const ICONS = {
    success: "fa-circle-check", // green check
    danger: "fa-circle-xmark", // red X
    warning: "fa-triangle-exclamation", // yellow warning
    info: "fa-circle-info", // blue info
  };

  // Main function to enhance all alerts
  function enhanceAlerts() {
    document.querySelectorAll(".alert").forEach((alert) => {
      // Add entrance animation + prepare for dim effect
      alert.classList.add("alert-animate", "alert-dim");

      // Kung wala pang icon, mag-add ng icon
      if (!alert.querySelector(".alert-icon")) {
        const span = document.createElement("span");
        span.className = "alert-icon"; // container para sa icon

        const i = document.createElement("i");
        i.className = "fa-solid"; // base font-awesome class

        // Pumili ng icon base sa alert type
        if (alert.classList.contains("alert-success"))
          i.classList.add(ICONS.success);
        else if (alert.classList.contains("alert-danger"))
          i.classList.add(ICONS.danger);
        else if (alert.classList.contains("alert-warning"))
          i.classList.add(ICONS.warning);
        else i.classList.add(ICONS.info); // default info

        span.appendChild(i);

        // Wrap existing content sa div para maayos ang spacing
        const content = document.createElement("div");
        content.className = "alert-content";
        while (alert.firstChild) content.appendChild(alert.firstChild); // ilipat lahat ng original content
        alert.appendChild(span); // add icon span sa alert
        alert.appendChild(content); // add wrapped content
      }

      // Gentle auto-dim after 3.5s para hindi nakaka-abala
      setTimeout(() => alert.classList.add("dimmed"), 3500);
    });
  }

  // Run enhanceAlerts kapag ready na ang DOM
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", enhanceAlerts);
  } else {
    enhanceAlerts();
  }
})();
