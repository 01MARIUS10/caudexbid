{{--
    Comptes à rebours en direct : chaque élément [data-countdown="<date ISO>"] affiche le temps restant jusqu'à sa date.
    Le texte rendu par le serveur reste affiché sans JavaScript. [data-countdown-end] = texte affiché à zéro.
--}}
<script>
    (() => {
        const els = document.querySelectorAll('[data-countdown]');
        if (!els.length) return;
        const days = new Intl.NumberFormat(document.documentElement.lang, { style: 'unit', unit: 'day', unitDisplay: 'short' });
        const pad = (n) => String(n).padStart(2, '0');
        const tick = () => {
            const now = Date.now();
            els.forEach((el) => {
                const s = Math.max(0, Math.floor((Date.parse(el.dataset.countdown) - now) / 1000));
                if (s === 0 && el.dataset.countdownEnd) {
                    el.textContent = el.dataset.countdownEnd;
                    return;
                }
                const d = Math.floor(s / 86400);
                el.textContent = (d ? days.format(d) + ' ' : '') + `${pad(Math.floor(s / 3600) % 24)}:${pad(Math.floor(s / 60) % 60)}:${pad(s % 60)}`;
            });
        };
        setInterval(tick, 1000);
        tick();
    })();
</script>
