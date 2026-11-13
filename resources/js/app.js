import './bootstrap';

// PDF auto-fill on the Forms / Support Documents upload pages. Loaded on demand so
// other pages don't download it; pdf.js itself loads only once a file is picked.
if (document.querySelector('form[data-pdf-autofill]')) {
    import('./pdf-autofill').then(m => m.init());
}
