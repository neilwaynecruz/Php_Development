<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Product Page • Wood &amp; Sea Salt</title>
    <style>
        :root {
            --bg-1: #020617;
            --bg-2: #0f172a;
            --bg-3: #111827;
            --accent: #38bdf8;
            --accent-2: #22c55e;
            --text-main: #e5e7eb;
            --text-muted: #9ca3af;
        }

        * ,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        /* Minimal animated background */
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
                        var(--bg-1),
                        var(--bg-2),
                        var(--bg-3));
            background-size: 200% 200%;
            animation: bgShift 18s ease-in-out infinite;
        }

        .container {
            max-width: 420px;
            width: 100%;
        }

        /* Simple card */
        .card {
            border-radius: 16px;
            padding: 18px 18px 20px;
            background: #020617;
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.8);
            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }

        .card:hover {
            transform: translateY(-6px);
            border-color: rgba(148, 163, 184, 0.5);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.9);
        }

        /* Image */
        .image-wrapper {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 16px;
            background: #020617;
        }

        .card img {
            display: block;
            width: 100%;
            height: 260px;
            object-fit: cover;
            border-radius: 12px;
            transition: transform 0.35s ease;
        }

        .card:hover img {
            transform: scale(1.03);
        }

        /* Header row */
        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
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
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.7);
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: #38bdf8;
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
        }

        /* Text */
        .name {
            font-size: 22px;
            font-weight: 650;
            letter-spacing: 0.02em;
            margin: 3px 0 4px;
            color: #f9fafb;
        }

        .subtitle {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: rgba(148, 163, 184, 0.9);
            margin-bottom: 6px;
        }

        .price-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .price {
            font-size: 20px;
            font-weight: 700;
            color: #bbf7d0;
        }

        .price-note {
            font-size: 11px;
            color: var(--text-muted);
        }

        .desc {
            color: var(--text-muted);
            line-height: 1.5;
            font-size: 13px;
            margin-bottom: 12px;
        }

        /* Tags */
        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 14px;
        }

        .tag-chip {
            font-size: 11px;
            padding: 3px 9px 4px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            color: #e5e7eb;
            background: #020617;
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
            padding: 8px 16px;
            border: 0;
            outline: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition:
                background 0.25s ease,
                color 0.25s ease,
                box-shadow 0.25s ease,
                transform 0.2s ease,
                border-color 0.25s ease;
            white-space: nowrap;
        }

        /* UPDATED: animated gradient + glow for primary button */
        .btn-primary {
            color: #020617;
            background: linear-gradient(
                120deg,
                #16a34a,
                #22c55e,
                #4ade80,
                #a3e635,
                #22c55e
            );
            background-size: 300% 300%;
            box-shadow: 0 0 18px rgba(34, 197, 94, 0.55);
            animation: btnPrimaryShift 6s ease-in-out infinite;
        }

        .btn-primary:hover {
            transform: translateY(-1px) scale(1.02);
        }

        /* UPDATED: pulsing border + color for ghost button */
        .btn-ghost {
            color: var(--text-main);
            background: transparent;
            border: 1px solid rgba(148, 163, 184, 0.6);
            animation: btnGhostPulse 5s ease-in-out infinite;
        }

        .btn-ghost:hover {
            transform: translateY(-1px);
        }

        /* Background animation */
        @keyframes bgShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* NEW: primary button gradient animation */
        @keyframes btnPrimaryShift {
            0% {
                background-position: 0% 50%;
                box-shadow: 0 0 16px rgba(34, 197, 94, 0.5);
            }
            50% {
                background-position: 100% 50%;
                box-shadow: 0 0 26px rgba(52, 211, 153, 0.7);
            }
            100% {
                background-position: 0% 50%;
                box-shadow: 0 0 16px rgba(34, 197, 94, 0.5);
            }
        }

        /* NEW: wishlist border / text color animation */
        @keyframes btnGhostPulse {
            0% {
                border-color: rgba(148, 163, 184, 0.55);
                color: #e5e7eb;
                box-shadow: 0 0 0 rgba(56, 189, 248, 0);
            }
            50% {
                border-color: #38bdf8;
                color: #e0f2fe;
                box-shadow: 0 0 14px rgba(56, 189, 248, 0.45);
            }
            100% {
                border-color: rgba(148, 163, 184, 0.55);
                color: #e5e7eb;
                box-shadow: 0 0 0 rgba(56, 189, 248, 0);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 16px;
            }
            .card {
                padding: 16px;
                border-radius: 14px;
            }
            .card img {
                height: 220px;
            }
            .name {
                font-size: 19px;
            }
            .price {
                font-size: 18px;
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
