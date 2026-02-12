(function () {
  const textarea =
    document.getElementById("fiche_content") ||
    document.querySelector("textarea[name$='[content]']");

  const preview = document.getElementById("preview");

  if (!textarea || !preview) {
    console.warn("Fiche editor: textarea or preview not found.");
    return;
  }

  // Escape HTML (XSS safe)
  function escapeHtml(str) {
    return String(str)
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  // Inline formatting
  function formatInline(line) {
    line = line.replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>");
    line = line.replace(/_(.+?)_/g, "<em>$1</em>");
    line = line.replace(/`(.+?)`/g, "<code>$1</code>");
    line = line.replace(/\[\[(.+?)\]\]/g, "<mark>$1</mark>");
    line = line.replaceAll("->", "→");
    return line;
  }

  function formatContent(text) {
    const raw = String(text || "").replace(/\r\n/g, "\n").replace(/\r/g, "\n");
    const safe = escapeHtml(raw).trim();

    if (!safe) {
      return '<p class="text-muted mb-0">Commence à écrire pour voir l’aperçu...</p>';
    }

    const lines = safe.split("\n");
    const out = [];
    let inList = false;

    function closeList() {
      if (inList) {
        out.push("</ul>");
        inList = false;
      }
    }

    for (const l of lines) {
      const trimmed = l.trim();

      if (!trimmed) {
        closeList();
        out.push("<div class='my-2'></div>");
        continue;
      }

      // Headings (==)
      if (trimmed.startsWith("==")) {
        closeList();
        const title = formatInline(trimmed.replace(/^==\s*/, ""));
        out.push(`<h5 class="fiche-h">${title}</h5>`);
        continue;
      }

      // Bullet list
      if (trimmed.startsWith("- ") || trimmed.startsWith("* ")) {
        if (!inList) {
          out.push("<ul class='fiche-ul'>");
          inList = true;
        }
        out.push(`<li>${formatInline(trimmed.slice(2).trim())}</li>`);
        continue;
      } else {
        closeList();
      }

      // Blocks
      if (trimmed.startsWith("!!+")) {
        const t = formatInline(trimmed.replace(/^!!\+\s*/, ""));
        out.push(`<div class="fiche-block tip">💡 <strong>${t}</strong></div>`);
        continue;
      }

      if (trimmed.startsWith("!!-")) {
        const t = formatInline(trimmed.replace(/^!!-\s*/, ""));
        out.push(`<div class="fiche-block trap">🚫 <strong>${t}</strong></div>`);
        continue;
      }

      if (trimmed.startsWith("!!")) {
        const t = formatInline(trimmed.replace(/^!!\s*/, ""));
        out.push(`<div class="fiche-block definition">⚠️ <strong>${t}</strong></div>`);
        continue;
      }

      if (trimmed.startsWith(">>")) {
        const t = formatInline(trimmed.replace(/^>>\s*/, ""));
        out.push(`<div class="fiche-block example">📘 <strong>${t}</strong></div>`);
        continue;
      }

      if (trimmed.startsWith("??")) {
        const t = formatInline(trimmed.replace(/^\?\?\s*/, ""));
        out.push(`<div class="fiche-block question">❓ <strong>${t}</strong></div>`);
        continue;
      }

      // Default paragraph
      out.push(`<p class="fiche-p">${formatInline(trimmed)}</p>`);
    }

    closeList();
    return out.join("");
  }

  function renderPreview() {
    preview.innerHTML = formatContent(textarea.value);
  }

  function updateStats() {
    const v = (textarea.value || "").trim();
    const words = v ? v.split(/\s+/).filter(Boolean).length : 0;
    const lines = v ? v.split(/\n/).length : 0;
    const readMin = Math.max(0, Math.ceil(words / 160));

    const elW = document.getElementById("statWords");
    const elL = document.getElementById("statLines");
    const elR = document.getElementById("statRead");

    if (elW) elW.textContent = String(words);
    if (elL) elL.textContent = String(lines);
    if (elR) elR.textContent = `~${readMin} min`;
  }

  window.insertText = function insertText(text) {
    textarea.focus();
    const start = textarea.selectionStart ?? textarea.value.length;
    const end = textarea.selectionEnd ?? textarea.value.length;
    textarea.setRangeText(text, start, end, "end");
    renderPreview();
    updateStats();
  };

  // Keyboard shortcuts
  document.addEventListener("keydown", (e) => {
    if (!e.ctrlKey) return;

    const k = e.key;
    if (k === "1") e.preventDefault(), window.insertText("!! Définition\n");
    if (k === "2") e.preventDefault(), window.insertText(">> Exemple\n");
    if (k === "3") e.preventDefault(), window.insertText("!!+ Astuce\n");
    if (k === "4") e.preventDefault(), window.insertText("!!- Piège\n");
    if (k === "5") e.preventDefault(), window.insertText("?? Question\n");
    if (k === "6") e.preventDefault(), window.insertText("== Section\n");
  });

  textarea.addEventListener("input", () => {
    renderPreview();
    updateStats();
  });

  renderPreview();
  updateStats();
})();
