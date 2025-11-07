# 🔷 PRISM Inventory System (Azure Essence Theme)

Modern minimalist inventory system for Users and Admin — may light/dark mode, responsive UI, interactive tables, alerts, at dashboard. Built as a clean HTML/CSS/JS demo (no backend required), pero ready for integration sa PHP/Node kung gusto mong i-expand.

![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)
![Responsive](https://img.shields.io/badge/Responsive-Yes-34D058)
![Dark Mode](https://img.shields.io/badge/Dark%20Mode-Supported-000)
![Status](https://img.shields.io/badge/Status-Demo-blue)

---

## Table of Contents

- [Project Overview](#project-overview)
- [Features](#features)
- [Installation & Setup](#installation--setup)
- [Demo / Usage](#demo--usage)
- [File Structure](#file-structure)
- [UI / UX Guidelines](#ui--ux-guidelines)
- [Theme & Design](#theme--design)
- [Accessibility & Responsiveness](#accessibility--responsiveness)
- [Known Issues](#known-issues)
- [Credits](#credits)

---

## Project Overview

PRISM is a modern minimalist inventory system para sa users at admin. Tinutulungan ka nitong i-manage ang products, users, at activity logs efficiently. May support for light/dark mode, responsive design (desktop to mobile), at interactive tables, alerts, at dashboards.

Main goal: gawing simple at mabilis ang inventory management, improve user experience, at bigyan ka ng malinaw na visibility sa system activity.

---

## Features

- Login / Authentication na may password visibility toggle
- Dashboard na may key metrics at recent activities
- Products Management: add, update, delete, archive items + low-stock alerts
- Users Management: manage roles, reset passwords, delete/archive users
- Activity Log: color-coded badges para sa create, update, delete, login, logout, archive, restore
- Alerts & Notifications: animated alerts na may icon, type, at auto-dim
- Theme Toggle: switch light/dark mode anytime
- Responsive Design: optimized for desktop, tablet, at mobile
- Brand Enhancements: animated PRISM logo at gradient brand text

---

## Installation & Setup

1. Clone the repository

   ```bash
   git clone https://github.com/yourusername/prism-inventory.git
   ```

2. Open the app

   - Buksan ang `index.html` sa browser para i-run ang system (static demo).

3. Siguraduhing naka-link ang core CSS/JS files

   - `theme.css` → styling, colors, at dark/light mode
   - `ui-enhance.js` → alerts, icons, animations
   - `theme-toggle.js` → theme toggle functionality

   Minimal sample (inside index.html):

   ```html
   <link rel="stylesheet" href="theme.css" />
   <script defer src="ui-enhance.js"></script>
   <script defer src="theme-toggle.js"></script>
   ```

4. Server? Optional
   - Walang server setup needed kung local HTML demo lang.
   - For a backend version, integrate with PHP / Node.js or your preferred server.

---

## Demo / Usage

- Login gamit ang default credentials (kung available).
- Dashboard: tingnan ang key metrics at recent activity.
- Products Table: add, edit, delete, archive items.
- Users Table: manage roles, reset passwords, delete/archive users.
- Activity Log: lahat ng actions may colored badges for quick scanning.
- Theme Toggle Button: nasa bottom-right — switch light/dark anytime.
- Alerts: lumalabas for success, error, warning, or info actions (may icons + animation).
- Tip: Sa mobile, auto-adapt ang layout — tables stack, buttons resize for easier taps.

---

## 👤 Default Account (for demo)

**Username:** admin  
**Password:** admin123

---

## File Structure

```
/prism-inventory/
├─ index.html               # Main entry page
├─ theme.css                # Theme styling (light/dark mode)
├─ ui-enhance.js            # Enhances alerts, icons, and animations
├─ theme-toggle.js          # Floating theme toggle functionality
├─ assets/
│  ├─ images/               # Logos, icons
│  └─ fonts/                # Poppins or other fonts
└─ README.md
```

---

## UI / UX Guidelines

- Cards & Dashboard: shadow, hover effects, at light lift animation
- Tables: row hover effect, low-stock highlights, responsive layout
- Forms: malinaw na labels at placeholders, focus indicators, theme-aware input backgrounds
- Buttons: primary, secondary, update, at visibility toggle buttons (with hover/focus effects)
- Alerts: animated entrance, auto-dim, color-coded per type (success, error, warning, info)

---

## Theme & Design

- Light Mode: neutral backgrounds, subtle surfaces, readable text, soft shadows
- Dark Mode: dark background na may high-contrast text + theme-aware shadows/borders
- Floating Theme Toggle Button: bottom-right, adaptive sa mobile; sun/moon icon
- Branding: animated PRISM logo with gradient brand text (Azure Essence vibe)

---

## Accessibility & Responsiveness

- Fully responsive tables, forms, buttons, and dashboards
- Focus indicators para sa keyboard navigation
- Respects prefers-reduced-motion para sa users na gusto ng minimal animation
- Adaptive layout sa mobile, tablet, at desktop screens

---

## Known Issues

- None sa local demo version.
- For backend integration: kailangan pa ng authentication at database setup.
- Siguraduhing loaded lahat ng font files (e.g., Poppins) para iwas style break.

---

## Credits

- Developer / Designer: Neil Wayne Cruz, Charles Gabriel Rola, Charlie Magne Rola, Christian Colita, Jenero Santos
- Fonts: Poppins
- Icons: FontAwesome
- Inspired by: Modern minimalist UI principles

---

## 🎬 Demo

1. Open login page → toggle password visibility
2. Navigate to Dashboard → highlight key metrics
3. Products Table → add, edit, delete, archive, low-stock alert
4. Users Table → edit roles, reset passwords, delete/archive, show badges
5. Activity Log → scroll and explain color-coded badges
6. Trigger alerts → success, warning, error, info
7. Toggle theme → light/dark mode
8. Resize browser → show responsive table and form adjustments

---

| Feature             | Description                                   |
| ------------------- | --------------------------------------------- |
| Login               | Password toggle, authentication-ready         |
| Dashboard           | Key metrics + recent activities               |
| Products Management | Add, edit, delete, archive, low-stock alerts  |
| Users Management    | Manage roles, reset passwords, delete/archive |
| Activity Log        | Color-coded badges for actions                |
| Theme Toggle        | Switch between light/dark mode                |
| Responsive          | Works on desktop, tablet, mobile              |

---

## 📄 License

MIT License © 2025 Neil Wayne Cruz, Charles Gabriel Rola, Charlie Magne Rola, Christian Colita, Jenero Santos
