// StudentInfo PCNL — client-side behaviour
(() => {
    const $ = (id) => document.getElementById(id);

    // Register: password validation
    const reg = $('registerForm');
    if (reg) {
        reg.addEventListener('submit', (ev) => {
            const msg = $('errorMsg');
            const pass = $('password').value;
            if (pass.length < 6) { msg.textContent = 'Password must be at least 6 characters.'; ev.preventDefault(); }
            else if (pass !== $('confirm').value) { msg.textContent = 'Passwords do not match.'; ev.preventDefault(); }
        });
    }

    // Student number: format + availability check (debounced fetch)
    const no = $('student_no');
    if (no) {
        const msg = $('studentNoMsg');
        const initial = no.value;
        let timer;
        const check = async () => {
            const v = no.value.trim();
            if (!v || v === initial && !reg) { msg.textContent = ''; return; }
            try {
                const data = await (await fetch('check_student.php?student_no=' + encodeURIComponent(v))).json();
                const [text, cls] = !data.valid ? ['Format must be like 2026-0001.', 'text-danger']
                    : !data.available ? ['This student number is already registered.', 'text-danger']
                    : ['Student number is available.', 'text-success'];
                msg.textContent = text;
                msg.className = 'form-text ' + cls;
            } catch { msg.textContent = ''; }
        };
        no.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(check, 400); });
        no.addEventListener('blur', check);
    }

    // Students table: live search
    const search = $('searchBox');
    if (search) {
        const rows = document.querySelectorAll('#studentTable tbody tr');
        search.addEventListener('input', () => {
            const q = search.value.toLowerCase();
            rows.forEach((r) => { r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none'; });
        });
    }

    // Confirm dialogs on destructive forms
    document.querySelectorAll('form[data-confirm]').forEach((f) =>
        f.addEventListener('submit', (ev) => { if (!confirm(f.dataset.confirm)) ev.preventDefault(); }));
})();
