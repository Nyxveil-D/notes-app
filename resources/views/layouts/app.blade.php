<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Notes') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-canvas: #f8fafc;
            --bg-surface: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --border-color: #e2e8f0;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --focus-ring: #93c5fd;
            --danger: #dc2626;
            --danger-hover: #b91c1c;
            --status-bg: #dcfce7;
            --status-text: #166534;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background-color: var(--bg-canvas);
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: var(--primary);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        button,
        .button,
        input,
        textarea {
            font: inherit;
        }

        button,
        .button,
        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            border-radius: 0.5rem;
        }

        button,
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 44px;
            padding: 0.625rem 1.125rem;
            border: 1px solid transparent;
            background-color: var(--primary);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease, box-shadow 150ms ease;
        }

        button:hover,
        .button:hover {
            background-color: var(--primary-hover);
            text-decoration: none;
        }

        .button-secondary {
            background-color: var(--bg-surface);
            color: var(--text-secondary);
            border-color: var(--border-color);
        }

        .button-secondary:hover {
            background-color: #f1f5f9;
            color: var(--text-primary);
            border-color: #cbd5e1;
        }

        .danger {
            background-color: var(--danger);
        }

        .danger:hover {
            background-color: var(--danger-hover);
        }

        a:focus-visible,
        button:focus-visible,
        .button:focus-visible,
        input:focus-visible,
        textarea:focus-visible {
            outline: 2px solid var(--focus-ring);
            outline-offset: 2px;
        }

        header.site-header {
            background-color: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .nav,
        main {
            max-width: 1120px;
            margin: 0 auto;
            padding: 1.25rem 1.5rem;
        }

        main {
            padding-top: 2rem;
            padding-bottom: 3.5rem;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .brand {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-primary);
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            min-height: 44px;
            letter-spacing: -0.01em;
        }

        .brand-icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background-color: #eff6ff;
            color: var(--primary);
            border-radius: 0.5rem;
            border: 1px solid #dbeafe;
        }

        .brand-icon {
            width: 18px;
            height: 18px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            flex-wrap: wrap;
        }

        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            background-color: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 9999px;
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .user-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            background-color: #e2e8f0;
            color: var(--text-primary);
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.03);
        }

        .status {
            padding: 0.875rem 1rem;
            color: var(--status-text);
            background-color: var(--status-bg);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            border: 1px solid #bbf7d0;
        }

        .errors {
            color: var(--danger);
            margin: 0 0 1rem 0;
            padding-left: 1.25rem;
            font-size: 0.875rem;
        }

        form,
        label {
            display: grid;
            gap: 0.5rem;
        }

        label {
            color: var(--text-primary);
            font-size: 0.875rem;
            font-weight: 600;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            width: 100%;
            min-height: 44px;
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--border-color);
            background-color: var(--bg-surface);
            color: var(--text-primary);
            transition: border-color 150ms ease, box-shadow 150ms ease;
        }

        input:focus,
        textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.5);
        }

        textarea {
            min-height: 12rem;
            resize: vertical;
        }

        /* Dashboard specific layout */
        .dashboard-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
        }

        .dashboard-title-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 1.875rem;
            line-height: 1.25;
            font-weight: 700;
            letter-spacing: -0.025em;
            color: var(--text-primary);
        }

        .count-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background-color: #eff6ff;
            color: var(--primary);
            border: 1px solid #dbeafe;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .dashboard-header p {
            margin: 0.375rem 0 0 0;
            color: var(--text-secondary);
            font-size: 0.9375rem;
            opacity: 0.9;
        }

        .search-panel {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.125rem 1.25rem;
            margin-bottom: 1.75rem;
            box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.02);
        }

        .search-form {
            display: flex;
            gap: 0.875rem;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .search-input-group {
            flex: 1 1 260px;
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        .search-input-group label {
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .search-field-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-icon {
            position: absolute;
            left: 0.875rem;
            width: 18px;
            height: 18px;
            color: var(--text-secondary);
            opacity: 0.7;
            pointer-events: none;
        }

        .search-field-wrapper input {
            padding-left: 2.5rem;
        }

        .search-field-wrapper input::placeholder {
            color: var(--text-secondary);
            opacity: 0.72;
        }

        .search-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .notes-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        @media (min-width: 768px) {
            .notes-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .note-card {
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 1.125rem;
            transition: border-color 150ms ease, box-shadow 150ms ease, transform 150ms ease;
        }

        .note-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.06), 0 4px 6px -2px rgba(15, 23, 42, 0.03);
            transform: translateY(-1px);
        }

        .note-card-header {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .doc-icon-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 0.5rem;
            background-color: #f1f5f9;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            flex-shrink: 0;
            margin-top: 0.125rem;
        }

        .doc-icon {
            width: 18px;
            height: 18px;
        }

        .note-card h2 {
            margin: 0 0 0.375rem 0;
            font-size: 1.1875rem;
            line-height: 1.35;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .note-card h2 a {
            color: var(--text-primary);
        }

        .note-card h2 a:hover {
            color: var(--primary);
        }

        .note-excerpt {
            margin: 0;
            color: var(--text-secondary);
            opacity: 0.88;
            font-size: 0.9375rem;
            line-height: 1.55;
            word-break: break-word;
        }

        .note-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding-top: 0.875rem;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
        }

        .note-date-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: var(--text-secondary);
            opacity: 0.75;
            font-size: 0.8125rem;
            font-weight: 500;
        }

        .clock-icon {
            width: 14px;
            height: 14px;
        }

        .note-card-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .note-action-edit,
        .note-action-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease;
            cursor: pointer;
            border: 1px solid transparent;
            background-color: var(--bg-surface);
        }

        .note-action-edit {
            color: var(--primary);
            border-color: #bfdbfe;
            background-color: var(--bg-surface);
        }

        .note-action-edit:hover {
            background-color: #eff6ff;
            border-color: #93c5fd;
            color: var(--primary-hover);
            text-decoration: none;
        }

        .note-action-delete {
            color: var(--danger);
            border-color: #fecaca;
            background-color: var(--bg-surface);
        }

        .note-action-delete:hover {
            background-color: #fef2f2;
            border-color: #f87171;
            color: #991b1b;
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 3.5rem 1.5rem;
            margin: 0 0 1.5rem 0;
            background-color: var(--bg-surface);
            border: 1px dashed #cbd5e1;
            border-radius: 0.875rem;
        }

        .empty-icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            margin-bottom: 1rem;
            background-color: #f1f5f9;
            color: var(--text-secondary);
            border-radius: 50%;
        }

        .empty-icon {
            width: 28px;
            height: 28px;
        }

        .empty-state h2 {
            margin: 0 0 0.5rem 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .empty-state p {
            margin: 0 auto 1.5rem auto;
            max-width: 420px;
            color: var(--text-secondary);
            font-size: 0.9375rem;
            opacity: 0.9;
        }

        .pagination-container {
            margin-top: 1.75rem;
        }

        /* Customize default pagination look natively */
        .pagination-container nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .pagination-container nav p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .pagination-container nav a,
        .pagination-container nav span[aria-current="page"],
        .pagination-container nav span[aria-disabled="true"] {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            min-width: 40px;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid var(--border-color);
            background-color: var(--bg-surface);
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .pagination-container nav a:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
        }

        .pagination-container nav span[aria-current="page"] {
            background-color: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .pagination-container nav span[aria-disabled="true"] {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f8fafc;
        }

        .page-actions-bar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .note-article-content {
            white-space: pre-wrap;
            line-height: 1.7;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .note-delete-form {
            display: inline;
        }

        .modal[hidden] {
            display: none;
        }

        .modal {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: grid;
            place-items: center;
            padding: 1.5rem;
        }

        .modal-backdrop {
            position: absolute;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.45);
        }

        .modal-dialog {
            position: relative;
            width: min(100%, 28rem);
            padding: 1.5rem;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.16), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
        }

        .modal-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            margin-bottom: 1rem;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            background-color: #fef2f2;
            color: var(--danger);
        }

        .modal-dialog h2 {
            margin: 0 0 0.5rem;
            color: var(--text-primary);
            font-size: 1.25rem;
            line-height: 1.35;
        }

        .modal-dialog p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9375rem;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .modal-delete-button {
            background-color: var(--danger);
        }

        .modal-delete-button:hover {
            background-color: var(--danger-hover);
        }
        /* Authentication */
        .auth-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(22rem, 0.95fr);
            min-height: calc(100vh - 154px);
            overflow: hidden;
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            background-color: var(--bg-surface);
            box-shadow: 0 16px 32px -20px rgba(15, 23, 42, 0.28);
        }

        .auth-intro {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(2rem, 5vw, 4.5rem);
            overflow: hidden;
            background-color: #eff6ff;
            border-right: 1px solid #dbeafe;
        }

        .auth-intro::before,
        .auth-intro::after {
            position: absolute;
            content: '';
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-intro::before {
            width: 18rem;
            height: 18rem;
            right: -7rem;
            bottom: -8rem;
            background-color: #dbeafe;
        }

        .auth-intro::after {
            width: 8rem;
            height: 8rem;
            top: 3rem;
            right: 14%;
            border: 1px solid #bfdbfe;
        }

        .auth-product,
        .auth-copy,
        .auth-note-stack {
            position: relative;
            z-index: 1;
        }

        .auth-product {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            color: var(--text-primary);
            font-size: 0.9375rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .auth-product-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid #bfdbfe;
            border-radius: 0.625rem;
            background-color: var(--bg-surface);
            color: var(--primary);
        }

        .auth-copy {
            max-width: 31rem;
            margin: auto 0;
            padding: 3rem 0;
        }

        .auth-copy h1 {
            max-width: 11ch;
            margin: 0;
            color: var(--text-primary);
            font-size: clamp(2.125rem, 4vw, 3.125rem);
            line-height: 1.08;
            letter-spacing: -0.045em;
        }

        .auth-copy p {
            max-width: 28rem;
            margin: 1rem 0 0;
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.65;
        }

        .auth-note-stack {
            display: grid;
            width: min(100%, 20rem);
            gap: 0.75rem;
        }

        .auth-note {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border: 1px solid #dbeafe;
            border-radius: 0.625rem;
            background-color: rgba(255, 255, 255, 0.78);
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: 0 8px 16px -14px rgba(37, 99, 235, 0.45);
        }

        .auth-note:nth-child(2) {
            width: 86%;
            margin-left: 1.25rem;
        }

        .auth-note-icon {
            flex: 0 0 auto;
            width: 18px;
            height: 18px;
            color: var(--primary);
        }

        .auth-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(2rem, 5vw, 4.5rem);
        }

        .auth-form-wrap {
            width: min(100%, 25rem);
        }

        .auth-eyebrow {
            margin: 0 0 0.5rem;
            color: var(--primary);
            font-size: 0.8125rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .auth-form-wrap h1 {
            margin: 0;
            color: var(--text-primary);
            font-size: clamp(1.75rem, 3vw, 2rem);
            line-height: 1.2;
            letter-spacing: -0.03em;
        }

        .auth-form-intro {
            margin: 0.625rem 0 1.75rem;
            color: var(--text-secondary);
            font-size: 0.9375rem;
        }

        .auth-form {
            display: grid;
            gap: 1rem;
        }

        .auth-field {
            display: grid;
            gap: 0.5rem;
        }

        .auth-field label {
            color: var(--text-primary);
            font-size: 0.875rem;
            font-weight: 600;
        }

        .auth-field input {
            min-height: 48px;
            padding: 0.75rem 0.875rem;
            border-color: var(--border-color);
            font-size: 0.9375rem;
        }

        .auth-field input[aria-invalid='true'] {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .password-field {
            position: relative;
        }

        .password-field input {
            padding-right: 3.25rem;
        }

        .password-toggle {
            position: absolute;
            right: 0.25rem;
            top: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            padding: 0;
            border: 0;
            border-radius: 0.375rem;
            background-color: transparent;
            color: var(--text-secondary);
        }

        .password-toggle:hover {
            background-color: #f1f5f9;
            color: var(--text-primary);
        }

        .password-toggle svg {
            width: 19px;
            height: 19px;
        }

        .field-error {
            display: flex;
            align-items: flex-start;
            gap: 0.375rem;
            margin: 0;
            color: var(--danger);
            font-size: 0.8125rem;
            line-height: 1.45;
        }

        .field-error svg {
            flex: 0 0 auto;
            width: 16px;
            height: 16px;
            margin-top: 0.0625rem;
        }

        .auth-submit {
            width: 100%;
            min-height: 48px;
            margin-top: 0.5rem;
            box-shadow: 0 8px 14px -10px rgba(37, 99, 235, 0.65);
        }

        .auth-submit:active {
            background-color: var(--primary-hover);
            transform: translateY(1px);
        }

        .auth-footer {
            margin: 1.5rem 0 0;
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-align: center;
        }

        .auth-footer a {
            font-weight: 700;
        }
        @media (max-width: 767px) {
            .nav,
            main {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            main {
                padding-top: 1.5rem;
            }

            .dashboard-header {
                margin-bottom: 1.25rem;
            }

            .dashboard-header .button {
                width: 100%;
            }

            .search-form,
            .search-actions {
                width: 100%;
            }

            .search-actions .button,
            .search-actions button {
                flex: 1;
            }

            .note-footer {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .note-card-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <nav class="nav" aria-label="Main Navigation">
            <a class="brand" href="{{ auth()->check() ? route('notes.index') : route('login') }}">
                <span class="brand-icon-wrapper">
                    <svg class="brand-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </span>
                <span>{{ config('app.name', 'Notes') }}</span>
            </a>
            <div class="nav-actions">
                @auth
                    <div class="user-pill">
                        <span class="user-avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="button-secondary">Log out</button>
                    </form>
                @else
                    <a class="button-secondary button" href="{{ route('login') }}">Log in</a>
                    <a class="button" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        @if(session('status'))
            <p class="status" role="status">{{ session('status') }}</p>
        @endif

        @yield('content')
    </main>
</body>
</html>


