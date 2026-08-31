// import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/*
 * Syntax highlighting (Prism.js).
 *
 * Only the languages listed in the project brief are loaded, to keep the
 * bundle small. `prism-clike` and `prism-markup` are loaded first because
 * several other languages (php, css, javascript, markup-based ones) depend
 * on them.
 */
import Prism from 'prismjs';
import 'prismjs/themes/prism-tomorrow.css';
import 'prismjs/components/prism-markup';
import 'prismjs/components/prism-clike';
import 'prismjs/components/prism-css';
import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-typescript';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-bash';
import 'prismjs/components/prism-go';
import 'prismjs/components/prism-python';
import 'prismjs/components/prism-sql';
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-yaml';
import 'prismjs/components/prism-docker';

document.addEventListener('DOMContentLoaded', () => {
    Prism.highlightAll();
});

/*
 * Copy-to-clipboard for <x-code-block>.
 *
 * Kept as a plain global function (no library) since the button uses a
 * simple inline onclick handler — see resources/views/components/code-block.blade.php.
 */
window.copyCodeBlock = function (button) {
    const targetId = button.getAttribute('data-copy-target');
    const codeEl = document.getElementById(targetId);

    if (!codeEl) {
        return;
    }

    navigator.clipboard.writeText(codeEl.textContent ?? '').then(() => {
        const label = button.querySelector('[data-copy-label]');

        if (!label) {
            return;
        }

        const original = label.textContent;
        label.textContent = 'Copied!';
        button.classList.add('text-emerald-400');

        setTimeout(() => {
            label.textContent = original;
            button.classList.remove('text-emerald-400');
        }, 1500);
    });
};
