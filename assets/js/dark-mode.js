// Set the theme before the page paints to avoid a light/dark flash.
(() => {
  const root = document.documentElement;
  const systemTheme = window.matchMedia?.("(prefers-color-scheme: dark)")
    .matches
    ? "dark"
    : "light";
  let savedTheme = null;

  try {
    savedTheme = localStorage.getItem("theme");
  } catch (error) {
    // Storage can be unavailable in private or restricted browser contexts.
  }

  const theme =
    savedTheme === "dark" || savedTheme === "light" ? savedTheme : systemTheme;
  root.setAttribute("data-bs-theme", theme);
  root.dataset.themePreference = savedTheme ? "saved" : "system";
})();

function updateThemeControls(theme) {
  document.querySelectorAll("[data-theme-toggle]").forEach((button) => {
    const icon =
      button.querySelector("[data-theme-icon]") || button.querySelector("i");
    const nextTheme = theme === "dark" ? "light" : "dark";

    button.setAttribute(
      "aria-label",
      `Aktifkan mode ${nextTheme === "dark" ? "gelap" : "terang"}`,
    );
    button.setAttribute(
      "title",
      `Aktifkan mode ${nextTheme === "dark" ? "gelap" : "terang"}`,
    );
    button.setAttribute("aria-pressed", String(theme === "dark"));

    if (icon) {
      icon.className =
        theme === "dark" ? "bi bi-sun-fill" : "bi bi-moon-stars-fill";
    }
  });
}

function setTheme(theme, persist = true) {
  const nextTheme = theme === "dark" ? "dark" : "light";
  document.documentElement.setAttribute("data-bs-theme", nextTheme);

  if (persist) {
    try {
      localStorage.setItem("theme", nextTheme);
      document.documentElement.dataset.themePreference = "saved";
    } catch (error) {
      // Keep the current-page theme even when it cannot be persisted.
    }
  }

  updateThemeControls(nextTheme);
}

function toggleDarkMode() {
  const currentTheme = document.documentElement.getAttribute("data-bs-theme");
  setTheme(currentTheme === "dark" ? "light" : "dark");
}

document.addEventListener("DOMContentLoaded", () => {
  updateThemeControls(
    document.documentElement.getAttribute("data-bs-theme") || "light",
  );

  document.querySelectorAll("[data-theme-toggle]").forEach((button) => {
    button.addEventListener("click", toggleDarkMode);
  });
});

const colorScheme = window.matchMedia?.("(prefers-color-scheme: dark)");
colorScheme?.addEventListener?.("change", (event) => {
  if (document.documentElement.dataset.themePreference === "system") {
    setTheme(event.matches ? "dark" : "light", false);
  }
});
