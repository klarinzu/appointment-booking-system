<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=REM:opsz,wght@8..144,600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --doc-ink: #16203a;
        --doc-text: var(--doc-ink);
        --doc-muted: #52617d;
        --doc-text-muted: var(--doc-muted);
        --doc-border: #d7dced;
        --doc-border-strong: #d4c186;
        --doc-surface: #f6f3eb;
        --doc-surface-soft: #fbfbfe;
        --doc-card: #ffffff;
        --doc-primary: #0a177d;
        --doc-primary-dark: #050a4d;
        --doc-primary-bright: #243dbe;
        --doc-gold: #d4a63f;
        --doc-gold-soft: #f4e4ad;
        --doc-gold-deep: #a67816;
        --doc-teal: #10827c;
        --doc-success: #1d7d60;
        --doc-warning: #b87b06;
        --doc-danger: #b14c49;
        --doc-shadow: 0 22px 54px rgba(10, 23, 125, 0.12);
        --doc-shadow-soft: 0 14px 30px rgba(10, 23, 125, 0.08);
        --doc-ring: 0 0 0 0.22rem rgba(10, 23, 125, 0.14);
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body,
    .content-wrapper,
    .main-footer,
    .login-page,
    .register-page,
    .hold-transition {
        font-family: "Poppins", "Segoe UI", sans-serif;
        color: var(--doc-ink);
        background:
            radial-gradient(circle at top right, rgba(212, 166, 63, 0.22), transparent 24%),
            radial-gradient(circle at top left, rgba(10, 23, 125, 0.08), transparent 28%),
            linear-gradient(180deg, #f6f2e7 0%, #f7f8fd 46%, #edf1ff 100%);
        line-height: 1.55;
        text-rendering: optimizeLegibility;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    .doc-heading,
    .brand-text,
    .content-header h1 {
        font-family: "REM", Georgia, serif;
        letter-spacing: -0.02em;
    }

    .content-wrapper {
        background: transparent;
    }

    a,
    button,
    .btn,
    .card-title,
    .doc-note,
    .doc-chip,
    .doc-status-badge,
    .doc-detail-row span,
    .doc-inline-stat,
    .doc-surface,
    .doc-status-panel,
    .doc-filter-summary,
    .doc-directory-item,
    .doc-auth-tip,
    .doc-search-bar,
    .table td,
    .table th,
    .alert,
    .badge {
        overflow-wrap: anywhere;
    }

    .row > [class*="col-"],
    .card,
    .card-header,
    .card-body,
    .card-footer,
    .doc-page-hero,
    .doc-hero-copy,
    .doc-hero-actions,
    .doc-chip-row,
    .doc-toolbar,
    .doc-table-tools,
    .doc-inline-stats > *,
    .doc-card-grid > *,
    .doc-info-grid > *,
    .doc-auth-layout > *,
    .doc-auth-grid > *,
    .doc-auth-name-grid > * {
        min-width: 0;
    }

    .content-header {
        padding-bottom: 0.35rem;
    }

    .content-header h1 {
        font-size: clamp(2rem, 2vw + 1.1rem, 2.8rem);
    }

    .main-header.navbar {
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(14px);
        border-bottom: 1px solid rgba(10, 23, 125, 0.1);
        box-shadow: 0 8px 24px rgba(10, 23, 125, 0.05);
    }

    .main-header .nav-link,
    .main-sidebar .nav-link,
    .main-footer,
    .small-box,
    .brand-link,
    .card-title,
    .table,
    .btn,
    .form-control,
    .custom-select,
    .input-group-text,
    .content {
        font-family: "Poppins", "Segoe UI", sans-serif;
    }

    .main-footer {
        border-top: 1px solid rgba(10, 23, 125, 0.08);
        background: rgba(255, 255, 255, 0.9);
        color: var(--doc-muted);
    }

    .main-sidebar {
        background:
            radial-gradient(circle at top right, rgba(212, 166, 63, 0.14), transparent 18%),
            linear-gradient(180deg, #050a4d 0%, #0a177d 58%, #243dbe 100%);
        box-shadow: inset -1px 0 0 rgba(255, 255, 255, 0.08);
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link.active,
    .main-sidebar .nav-sidebar > .nav-item.menu-open > .nav-link,
    .main-sidebar .nav-sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 14px;
    }

    .main-sidebar .nav-link {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.25rem;
        border-radius: 16px;
    }

    .main-sidebar .nav-link p {
        white-space: normal;
    }

    .main-sidebar .nav-icon {
        color: rgba(244, 228, 173, 0.95);
    }

    .brand-link {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding-top: 0.95rem;
        padding-bottom: 0.95rem;
    }

    .brand-link,
    .navbar-brand,
    .login-logo a,
    .register-logo a {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .brand-link .brand-image,
    .navbar-brand .brand-image {
        float: none;
        margin: 0;
    }

    .doc-brand-badge {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--doc-primary-dark), var(--doc-primary) 58%, var(--doc-primary-bright));
        box-shadow: 0 12px 28px rgba(3, 0, 87, 0.24);
        color: #ffffff;
        font-size: 0.84rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        flex-shrink: 0;
    }

    .doc-brand-badge-xl {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        font-size: 0.94rem;
    }

    .doc-brand-copy,
    .doc-auth-logo-copy {
        display: inline-flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .doc-brand-title {
        font-family: "REM", Georgia, serif;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .doc-brand-sub {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .main-sidebar .brand-text,
    .main-sidebar .doc-brand-copy,
    .main-sidebar .doc-brand-title,
    .main-sidebar .doc-brand-sub {
        color: rgba(255, 255, 255, 0.94);
    }

    .main-sidebar .doc-brand-sub {
        color: rgba(255, 255, 255, 0.72);
    }

    .login-logo,
    .register-logo {
        margin-bottom: 1.5rem;
    }

    .doc-auth-logo-link {
        justify-content: center;
        color: var(--doc-primary-dark);
    }

    .doc-auth-logo-img {
        max-height: 58px;
        width: auto;
        object-fit: contain;
    }

    .doc-auth-logo-copy .doc-brand-title {
        color: var(--doc-primary-dark);
    }

    .doc-auth-logo-copy .doc-brand-sub {
        color: var(--doc-muted);
    }

    .card {
        border: 1px solid var(--doc-border);
        border-radius: 26px;
        box-shadow: var(--doc-shadow);
        overflow: hidden;
        background: var(--doc-card);
    }

    .card-header {
        border-bottom: 1px solid var(--doc-border);
        padding: 1rem 1.35rem;
        background:
            linear-gradient(90deg, rgba(212, 166, 63, 0.12), transparent 28%),
            linear-gradient(180deg, rgba(10, 23, 125, 0.08), rgba(255, 255, 255, 0.96));
    }

    .card-footer {
        border-top: 1px solid var(--doc-border);
        padding: 1rem 1.35rem;
        background: rgba(246, 243, 235, 0.88);
    }

    .card-body {
        padding: 1.4rem;
    }

    .card-body.p-0,
    .card-header.p-0,
    .card-footer.p-0 {
        padding: 0 !important;
    }

    .card-title {
        line-height: 1.3;
        color: var(--doc-primary-dark);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        border-radius: 999px;
        font-weight: 700;
        padding: 0.68rem 1.2rem;
        box-shadow: none;
        min-height: 46px;
        max-width: 100%;
        white-space: normal;
        text-align: center;
        line-height: 1.25;
        transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, border-color 0.18s ease, color 0.18s ease;
    }

    .btn-sm {
        padding: 0.45rem 0.9rem;
        min-height: 38px;
    }

    .btn-primary,
    .btn-danger {
        border-color: transparent;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--doc-primary-dark), var(--doc-primary) 55%, var(--doc-primary-bright));
    }

    .btn-primary:hover,
    .btn-primary:focus {
        background: linear-gradient(135deg, #02003f, #070372 60%, #0600c9);
    }

    .btn-outline-primary,
    .btn-outline-secondary {
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 8px 18px rgba(10, 23, 125, 0.04);
    }

    .btn-outline-primary {
        border-color: rgba(10, 23, 125, 0.18);
        color: var(--doc-primary-dark);
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
        background: rgba(10, 23, 125, 0.06);
        border-color: rgba(10, 23, 125, 0.28);
        color: var(--doc-primary-dark);
    }

    .btn-outline-secondary {
        border-color: rgba(212, 166, 63, 0.28);
        color: var(--doc-primary-dark);
    }

    .btn-outline-secondary:hover,
    .btn-outline-secondary:focus {
        background: rgba(212, 166, 63, 0.12);
        border-color: rgba(212, 166, 63, 0.42);
        color: var(--doc-primary-dark);
    }

    .form-control,
    .custom-select,
    .input-group-text {
        min-height: calc(1.5em + 1.1rem + 2px);
        border-radius: 16px;
        border-color: var(--doc-border);
        background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
        color: var(--doc-ink);
    }

    textarea.form-control {
        min-height: 110px;
        resize: vertical;
    }

    .form-control:focus,
    .custom-select:focus {
        border-color: rgba(10, 23, 125, 0.26);
        box-shadow: var(--doc-ring);
    }

    .input-group > .form-control:not(:last-child),
    .input-group > .custom-select:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group-append .input-group-text {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .input-group-text {
        background: linear-gradient(180deg, rgba(212, 166, 63, 0.1), rgba(10, 23, 125, 0.05));
        color: var(--doc-primary);
        font-weight: 700;
    }

    .form-group label,
    label:not(.custom-control-label) {
        color: var(--doc-primary-dark);
        font-weight: 700;
        margin-bottom: 0.45rem;
    }

    .form-control::placeholder,
    .custom-select,
    textarea.form-control::placeholder {
        color: #7885a3;
    }

    .custom-control-label {
        color: var(--doc-ink);
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: linear-gradient(180deg, rgba(10, 23, 125, 0.05), rgba(255, 255, 255, 0.96));
        border-bottom: 1px solid var(--doc-border);
        color: var(--doc-muted);
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .table td,
    .table th {
        padding: 1rem 1rem;
        vertical-align: middle;
        line-height: 1.55;
    }

    .table-responsive {
        border-radius: inherit;
    }

    .table-hover tbody tr:hover {
        background: rgba(8, 0, 242, 0.04);
    }

    .alert {
        border-radius: 18px;
        border: 1px solid transparent;
        box-shadow: 0 12px 28px rgba(16, 33, 59, 0.05);
    }

    .alert-success {
        color: #155341;
        background: #e7f6ef;
        border-color: #b8e0ce;
    }

    .alert-danger {
        color: #7a2f2e;
        background: #fbebea;
        border-color: #efc2c0;
    }

    .alert-warning {
        color: #7a5200;
        background: #fff5dd;
        border-color: #f2d38b;
    }

    .alert-info {
        color: var(--doc-primary-dark);
        background: #edf1ff;
        border-color: #ccd7ff;
    }

    .badge {
        border-radius: 999px;
        font-weight: 700;
        padding: 0.5em 0.75em;
        line-height: 1.25;
    }

    .doc-page-hero {
        position: relative;
        padding: clamp(1.35rem, 2vw, 1.85rem);
        border-radius: 28px;
        border: 1px solid var(--doc-border);
        background:
            radial-gradient(circle at top right, rgba(212, 166, 63, 0.18), transparent 22%),
            radial-gradient(circle at left center, rgba(10, 23, 125, 0.04), transparent 28%),
            linear-gradient(135deg, #ffffff 0%, #f8f8fc 58%, #edf1ff 100%);
        box-shadow: var(--doc-shadow);
        overflow: hidden;
    }

    .doc-page-hero::after {
        content: "";
        position: absolute;
        inset: auto -72px -72px auto;
        width: 180px;
        height: 180px;
        border-radius: 999px;
        background: rgba(8, 0, 242, 0.09);
    }

    .doc-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        color: var(--doc-primary);
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .doc-hero-copy {
        position: relative;
        z-index: 1;
    }

    .doc-hero-actions,
    .doc-chip-row,
    .doc-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: stretch;
    }

    .doc-inline-stats,
    .doc-info-grid,
    .doc-card-grid,
    .doc-auth-grid,
    .doc-mini-grid {
        display: grid;
        gap: 1rem;
    }

    .doc-inline-stats {
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        margin-top: 1.25rem;
    }

    .doc-mini-grid {
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    }

    .doc-info-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    .doc-card-grid {
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    }

    .doc-inline-stat,
    .doc-surface,
    .doc-status-panel,
    .doc-filter-summary,
    .doc-timeline-item,
    .doc-directory-item,
    .doc-auth-tip,
    .doc-search-bar {
        position: relative;
        z-index: 1;
        border-radius: 20px;
        border: 1px solid var(--doc-border);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(249, 250, 255, 0.94));
        box-shadow: var(--doc-shadow-soft);
    }

    .doc-inline-stat,
    .doc-surface,
    .doc-status-panel,
    .doc-filter-summary,
    .doc-directory-item,
    .doc-auth-tip,
    .doc-search-bar {
        padding: 1rem 1rem;
    }

    .doc-inline-stat strong {
        display: block;
        margin-bottom: 0.2rem;
        font-size: 1.8rem;
        line-height: 1;
        color: var(--doc-primary-dark);
    }

    .doc-kicker,
    .doc-label {
        color: #5f6980;
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .doc-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        border: 1px solid rgba(10, 23, 125, 0.12);
        background: linear-gradient(180deg, rgba(10, 23, 125, 0.06), rgba(212, 166, 63, 0.08));
        color: var(--doc-primary-dark);
        font-size: 0.88rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .doc-note {
        color: #5a6782;
        line-height: 1.7;
    }

    .doc-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0.8rem;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 800;
        line-height: 1.3;
        border: 1px solid transparent;
        white-space: normal;
        text-align: center;
        max-width: 100%;
    }

    .doc-status-warning {
        color: #7a4e00;
        background: #fff0c8;
        border-color: #ffd473;
    }

    .doc-status-primary {
        color: #030057;
        background: #e6e4ff;
        border-color: #c5c0ff;
    }

    .doc-status-info {
        color: #070372;
        background: #ecebff;
        border-color: #d3d0ff;
    }

    .doc-status-accent {
        color: #7a5200;
        background: #fff1cf;
        border-color: #ffcf62;
    }

    .doc-status-success {
        color: #13553f;
        background: #d8f3e8;
        border-color: #abddc8;
    }

    .doc-status-danger {
        color: #7b2f2f;
        background: #f9dddb;
        border-color: #efbcbc;
    }

    .doc-status-neutral {
        color: #4d5070;
        background: #f2f1f8;
        border-color: #d9d7e6;
    }

    .doc-record-meta {
        display: grid;
        gap: 0.35rem;
    }

    .doc-record-meta strong {
        font-size: 1rem;
    }

    .doc-workflow-list,
    .doc-helper-list {
        margin: 0;
        padding-left: 1.15rem;
        color: var(--doc-muted);
    }

    .doc-workflow-list li,
    .doc-helper-list li {
        margin-bottom: 0.55rem;
        line-height: 1.65;
    }

    .doc-detail-stack {
        display: grid;
        gap: 0.75rem;
    }

    .doc-detail-row {
        display: grid;
        gap: 0.28rem;
        padding: 0.9rem 1rem;
        border-radius: 18px;
        border: 1px solid rgba(10, 23, 125, 0.08);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 249, 255, 0.92));
    }

    .doc-timeline {
        display: grid;
        gap: 1rem;
    }

    .doc-timeline-item {
        padding: 1rem 1rem 1rem 1.15rem;
        border-left: 4px solid rgba(7, 3, 114, 0.16);
    }

    .doc-empty-state {
        padding: 2rem;
        text-align: center;
        color: var(--doc-muted);
    }

    .doc-search-bar .form-control {
        background: transparent;
    }

    .doc-table-tools {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .doc-auth-layout {
        min-height: calc(100vh - 66px);
        display: grid;
        grid-template-columns: minmax(320px, 1.15fr) minmax(340px, 0.85fr);
        gap: 2rem;
        align-items: center;
        padding: 2rem 1.25rem 1.5rem;
        max-width: 1240px;
        margin: 0 auto;
    }

    .doc-auth-shell {
        width: 100%;
        align-self: stretch;
    }

    .doc-auth-hero {
        position: relative;
        overflow: hidden;
        padding: 2rem;
        border-radius: 32px;
        background: linear-gradient(135deg, rgba(3, 0, 87, 0.99), rgba(7, 3, 114, 0.98) 55%, rgba(8, 0, 242, 0.96));
        color: #fff;
        box-shadow: 0 28px 60px rgba(3, 0, 87, 0.28);
    }

    .doc-auth-hero::after {
        content: "";
        position: absolute;
        inset: auto -70px -90px auto;
        width: 240px;
        height: 240px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
    }

    .doc-auth-hero p,
    .doc-auth-hero li {
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.75;
    }

    .login-box,
    .register-box {
        width: 100%;
        max-width: 100%;
        margin-inline: auto;
    }

    .register-box {
        max-width: 980px;
    }

    .login-page .card,
    .register-page .card {
        margin-bottom: 0;
    }

    .doc-auth-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .doc-auth-grid .doc-span-2 {
        grid-column: 1 / -1;
    }

    .doc-auth-section {
        padding: 1.1rem 1.15rem;
        border: 1px solid var(--doc-border);
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #faf9fc 100%);
        box-shadow: var(--doc-shadow-soft);
    }

    .doc-auth-section-title {
        margin-bottom: 0.9rem;
        color: var(--doc-primary);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.11em;
        text-transform: uppercase;
    }

    .doc-auth-tip strong {
        display: block;
        margin-bottom: 0.25rem;
    }

    .doc-form-help {
        display: block;
        margin-top: 0.4rem;
        color: var(--doc-muted);
        font-size: 0.85rem;
        line-height: 1.55;
    }

    .doc-auth-name-grid {
        display: grid;
        gap: 0.85rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .doc-auth-name-grid .doc-full-span {
        grid-column: 1 / -1;
    }

    .doc-password-toggle {
        min-width: 52px;
        border-color: var(--doc-border);
        border-left: 0;
        border-radius: 0 16px 16px 0;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(245, 247, 255, 0.96));
        color: var(--doc-primary-dark);
    }

    .doc-password-toggle:focus {
        box-shadow: none;
    }

    .doc-password-toggle .fas {
        pointer-events: none;
    }

    .doc-middle-name-toggle {
        margin-top: 0.75rem;
    }

    .doc-suggestion-btn {
        border-radius: 999px;
        border: 1px solid rgba(10, 23, 125, 0.14);
        background: rgba(10, 23, 125, 0.06);
        color: var(--doc-primary-dark);
        font-weight: 700;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .doc-chat-response {
        min-height: 110px;
        white-space: pre-wrap;
    }

    .doc-mobile-cards .doc-mobile-card {
        display: none;
    }

    @media (max-width: 991.98px) {
        .doc-auth-layout {
            grid-template-columns: 1fr;
            padding-top: 1.5rem;
        }

        .doc-auth-hero {
            order: 2;
        }
    }

    @media (max-width: 767.98px) {
        .content-header {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .doc-page-hero {
            padding: 1.25rem;
            border-radius: 24px;
        }

        .card-body,
        .card-header,
        .card-footer {
            padding-left: 1.05rem;
            padding-right: 1.05rem;
        }

        .doc-auth-grid,
        .doc-auth-name-grid,
        .doc-card-grid,
        .doc-info-grid,
        .doc-inline-stats,
        .doc-mini-grid {
            grid-template-columns: 1fr;
        }

        .table td,
        .table th {
            padding: 0.85rem 0.75rem;
        }

        .doc-hero-actions .btn,
        .doc-toolbar .btn {
            width: 100%;
        }

        .doc-table-stack {
            display: none;
        }

        .doc-mobile-cards .doc-mobile-card {
            display: block;
        }
    }
</style>
