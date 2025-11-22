<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Page • Wood &amp; Sea Salt</title>
    <style>
        :root {
            --bg-1: #020617;
            --bg-2: #0b1120;
            --bg-3: #111827;
            --accent: #38bdf8;
            --accent-soft: rgba(56, 189, 248, 0.3);
            --accent-2: #a855f7;
            --accent-3: #22c55e;
            --text-main: #e5e7eb;
            --text-muted: #9ca3af;
            --card-bg: rgba(15, 23, 42, 0.96);
            --border-subtle: rgba(148, 163, 184, 0.35);
            --radius-xl: 24px;
            --shadow-strong:
                0 24px 80px rgba(15, 23, 42, 0.9),
                0 0 60px rgba(56, 189, 248, 0.35);
        }

        * ,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        /* Body + animated background */
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: system-ui, -apple-system, BlinkMacSystemFont,
                         "Segoe UI", sans-serif;
            color: var(--text-main);
            background: linear-gradient(-45deg,
                        #020617,
                        #0f172a,
                        #111827,
                        #020617);
            background-size: 300% 300%;
            animation: bgShift 18s ease-in-out infinite;
            overflow: hidden;
            position: relative;
        }

        /* soft color clouds behind card */
        body::before,
        body::after {
            content: "";
            position: fixed;
            width: 120vmax;
            height: 120vmax;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background:
                radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.16) 0, transparent 50%),
                radial-gradient(circle at 80% 0%, rgba(168, 85, 247, 0.20) 0, transparent 45%),
                radial-gradient(circle at 0% 80%, rgba(34, 197, 94, 0.18) 0, transparent 55%);
            opacity: 0.8;
            filter: blur(50px);
            z-index: -2;
            pointer-events: none;
        }

        body::after {
            animation: glowDrift 38s linear infinite;
            mix-blend-mode: screen;
        }

        .container {
            max-width: 420px;
            width: 100%;
        }

        /* Card */
        .card {
            position: relative;
            border-radius: var(--radius-xl);
            padding: 22px 22px 24px;
            background:
                radial-gradient(circle at top left,
                    rgba(56, 189, 248, 0.12) 0,
                    transparent 55%),
                var(--card-bg);
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-strong);
            backdrop-filter: blur(18px) saturate(140%);
            -webkit-backdrop-filter: blur(18px) saturate(140%);
            overflow: hidden;
            transition:
                transform 0.45s ease,
                box-shadow 0.45s ease,
                border-color 0.35s ease,
                background 0.45s ease;
        }

        .card::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            background: linear-gradient(
                120deg,
                rgba(56, 189, 248, 0.6),
                rgba(129, 140, 248, 0.25),
                rgba(34, 197, 94, 0.4)
            );
            opacity: 0;
            mix-blend-mode: screen;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .card::after {
            content: "";
            position: absolute;
            top: -40%;
            left: -20%;
            width: 60%;
            height: 180%;
            background: linear-gradient(
                120deg,
                rgba(148, 163, 184, 0) 0%,
                rgba(148, 163, 184, 0.25) 40%,
                rgba(248, 250, 252, 0.14) 50%,
                rgba(148, 163, 184, 0) 70%
            );
            opacity: 0;
            transform: translateX(-60%);
            transition: opacity 0.6s ease, transform 0.6s ease;
            pointer-events: none;
        }

        .card:hover {
            transform: translateY(-10px) scale(1.01);
            border-color: rgba(148, 163, 184, 0.6);
            background:
                radial-gradient(circle at top left,
                    rgba(56, 189, 248, 0.18) 0,
                    transparent 55%),
                var(--card-bg);
            box-shadow:
                0 26px 80px rgba(15, 23, 42, 0.9),
                0 0 90px rgba(56, 189, 248, 0.5);
        }

        .card:hover::before {
            opacity: 0.9;
        }

        .card:hover::after {
            opacity: 1;
            transform: translateX(160%);
        }

        /* Image */
        .image-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 18px;
            background: radial-gradient(circle at 10% 0,
                        rgba(15, 23, 42, 0.9) 0,
                        #020617 55%);
        }

        .image-glow {
            position: absolute;
            inset: 2px;
            border-radius: inherit;
            background:
                radial-gradient(circle at 10% 10%, rgba(56, 189, 248, 0.65) 0, transparent 55%),
                radial-gradient(circle at 90% 90%, rgba(168, 85, 247, 0.55) 0, transparent 60%);
            opacity: 0.25;
            mix-blend-mode: screen;
            pointer-events: none;
        }

        .card img {
            position: relative;
            display: block;
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.6);
            box-shadow:
                0 22px 45px rgba(15, 23, 42, 0.9),
                0 0 40px rgba(56, 189, 248, 0.45);
            transition:
                transform 0.6s ease,
                filter 0.45s ease,
                box-shadow 0.45s ease;
            transform-origin: center center;
        }

        .card:hover img {
            transform: scale(1.04) translateY(-4px);
            filter: brightness(1.08) saturate(1.15);
            box-shadow:
                0 26px 60px rgba(15, 23, 42, 0.95),
                0 0 70px rgba(56, 189, 248, 0.7);
        }

        /* Header row */
        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            gap: 10px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px 4px;
            border-radius: 999px;
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 600;
            color: #ecfeff;
            background: linear-gradient(135deg,
                        rgba(56, 189, 248, 0.1),
                        rgba(59, 130, 246, 0.3));
            border: 1px solid rgba(56, 189, 248, 0.6);
            box-shadow: 0 0 18px rgba(56, 189, 248, 0.6);
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: radial-gradient(circle,
                        #a5f3fc 0,
                        #22d3ee 45%,
                        #0ea5e9 100%);
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.8);
        }

        .rating {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #e5e7eb;
        }

        .rating span.star {
            color: #fbbf24;
            filter: drop-shadow(0 0 6px rgba(250, 204, 21, 0.9));
        }

        /* Text */
        .name {
            font-size: 24px;
            font-weight: 650;
            letter-spacing: 0.02em;
            margin: 4px 0 4px;
            color: #f9fafb;
        }

        .subtitle {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: rgba(148, 163, 184, 0.9);
            margin-bottom: 10px;
        }

        .price-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .price {
            font-size: 22px;
            font-weight: 700;
            color: #bbf7d0;
            text-shadow: 0 0 18px rgba(34, 197, 94, 0.9);
        }

        .price-note {
            font-size: 11px;
            color: var(--text-muted);
        }

        .desc {
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 13px;
            margin-bottom: 14px;
        }

        /* Tags / scent profile */
        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 16px;
        }

        .tag-chip {
            font-size: 11px;
            padding: 3px 9px 4px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.5);
            color: #e5e7eb;
            background: radial-gradient(circle at top left,
                        rgba(31, 41, 55, 0.9) 0,
                        rgba(15, 23, 42, 0.9) 55%);
        }

        /* Footer / buttons */
        .footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 4px;
        }

        .btn-primary,
        .btn-ghost {
            cursor: pointer;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 9px 18px;
            border: 0;
            outline: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition:
                background 0.35s ease,
                color 0.35s ease,
                box-shadow 0.35s ease,
                transform 0.25s ease,
                border-color 0.35s ease;
            white-space: nowrap;
        }

        .btn-primary {
            color: #0b1120;
            background: linear-gradient(135deg,
                        #22c55e,
                        #4ade80,
                        #a3e635);
            box-shadow:
                0 12px 35px rgba(22, 163, 74, 0.65),
                0 0 35px rgba(45, 212, 191, 0.7);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg,
                        #4ade80,
                        #a3e635,
                        #facc15);
            box-shadow:
                0 16px 42px rgba(22, 163, 74, 0.9),
                0 0 50px rgba(52, 211, 153, 0.9);
        }

        .btn-ghost {
            color: var(--text-main);
            background: linear-gradient(135deg,
                        rgba(15, 23, 42, 0.9),
                        rgba(15, 23, 42, 0.7));
            border: 1px solid rgba(148, 163, 184, 0.7);
            box-shadow: 0 0 0 rgba(15, 23, 42, 0.8);
        }

        .btn-ghost:hover {
            transform: translateY(-1px);
            background: radial-gradient(circle at top left,
                        rgba(31, 41, 55, 0.95) 0,
                        rgba(15, 23, 42, 0.95) 55%);
            border-color: rgba(148, 163, 184, 1);
            box-shadow:
                0 12px 38px rgba(15, 23, 42, 0.95),
                0 0 40px rgba(148, 163, 184, 0.65);
        }

        /* Animations */
        @keyframes bgShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes glowDrift {
            0%   { transform: translate(-50%, -50%) rotate(0deg); }
            50%  { transform: translate(-49%, -51%) rotate(180deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 16px;
            }
            .card {
                padding: 18px 16px 20px;
                border-radius: 20px;
            }
            .card img {
                height: 240px;
            }
            .name {
                font-size: 20px;
            }
            .price {
                font-size: 20px;
            }
            .desc {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Image -->
            <div class="image-wrapper">
                <div class="image-glow"></div>
                <img src="{{ asset('images/sample-product.jpg') }}"
                     alt="Sample Product – Jo Malone Wood &amp; Sea Salt">
            </div>

            <!-- Header row -->
            <div class="header-row">
                <div class="badge">
                    <span class="badge-dot"></span>
                    New Arrival
                </div>
                <div class="rating">
                    <span class="star">★</span>
                    <span>4.9</span>
                    <span style="color: var(--text-muted);">/ 5</span>
                </div>
            </div>

            <div class="subtitle">Jo Malone • Eau de Cologne</div>
            <div class="name">Wood &amp; Sea Salt</div>

            <div class="price-row">
                <div class="price">₱5,000.00</div>
                <div class="price-note">Free shipping • Limited stock</div>
            </div>

            <p class="desc">
                Jo Malone Wood &amp; Sea Salt is a refreshing fragrance that captures
                the crispness of coastal woods and ocean air. Serene, sophisticated,
                and perfect for both everyday wear and special evenings.
            </p>

            <div class="tags">
                <span class="tag-chip">Unisex</span>
                <span class="tag-chip">Fresh</span>
                <span class="tag-chip">Woody</span>
                <span class="tag-chip">Marine Notes</span>
                <span class="tag-chip">Longevity: 6–8 hrs</span>
            </div>

            <div class="footer">
                <button class="btn-primary">
                    Add to Cart
                </button>
                <button class="btn-ghost">
                    Wishlist
                </button>
            </div>
        </div>
    </div>
</body>
</html>
