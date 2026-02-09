const textarea = document.getElementById('fiche_content');
const preview = document.getElementById('preview');

function formatContent(text) {
    let html = text
        .replace(/!! (.+)/g, '<div class="fiche-block definition">⚠️ <strong>$1</strong></div>')
        .replace(/>> (.+)/g, '<div class="fiche-block example">📘 <strong>$1</strong></div>')
        .replace(/\n/g, '<br>');

    return html;
}

textarea.addEventListener('input', () => {
    if (textarea.value.trim() === '') {
        preview.innerHTML = '<p class="text-muted">Commence à écrire pour voir l’aperçu...</p>';
    } else {
        preview.innerHTML = formatContent(textarea.value);
    }
});

function insertText(text) {
    const start = textarea.selectionStart;
    textarea.setRangeText(text, start, start, 'end');
    textarea.focus();
    textarea.dispatchEvent(new Event('input'));
}
