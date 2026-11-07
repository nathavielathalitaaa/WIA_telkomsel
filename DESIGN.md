---
name: Sistem Tanggap Bencana
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#45464d'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#76777d'
  outline-variant: '#c6c6cd'
  surface-tint: '#565e74'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#131b2e'
  on-primary-container: '#7c839b'
  inverse-primary: '#bec6e0'
  secondary: '#bb0112'
  on-secondary: '#ffffff'
  secondary-container: '#e02928'
  on-secondary-container: '#fffbff'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#2f1500'
  on-tertiary-container: '#c76c00'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2fd'
  primary-fixed-dim: '#bec6e0'
  on-primary-fixed: '#131b2e'
  on-primary-fixed-variant: '#3f465c'
  secondary-fixed: '#ffdad6'
  secondary-fixed-dim: '#ffb4ab'
  on-secondary-fixed: '#410002'
  on-secondary-fixed-variant: '#93000b'
  tertiary-fixed: '#ffdcc3'
  tertiary-fixed-dim: '#ffb77d'
  on-tertiary-fixed: '#2f1500'
  on-tertiary-fixed-variant: '#6e3900'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  headline-xl:
    fontFamily: Public Sans
    fontSize: 36px
    fontWeight: '800'
    lineHeight: 44px
  headline-xl-mobile:
    fontFamily: Public Sans
    fontSize: 28px
    fontWeight: '800'
    lineHeight: 34px
  headline-lg:
    fontFamily: Public Sans
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
  headline-lg-mobile:
    fontFamily: Public Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 30px
  headline-md:
    fontFamily: Public Sans
    fontSize: 22px
    fontWeight: '700'
    lineHeight: 28px
  headline-sm:
    fontFamily: Public Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Public Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Public Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Public Sans
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
  label-lg:
    fontFamily: Space Grotesk
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 18px
  label-md:
    fontFamily: Space Grotesk
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
  label-sm:
    fontFamily: Space Grotesk
    fontSize: 10px
    fontWeight: '700'
    lineHeight: 14px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-mobile: 0.75rem
  margin: 1.5rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style
This design system governs an Indonesian public-sector disaster management and citizen incident reporting platform. The operational ethos centers on unconditional reliability, rapid situational awareness, and total civic trust under acute stress.

The aesthetic fuses **Corporate/Modern** structural clarity with **High-Contrast Utilitarianism**. Because citizens and first responders use this interface under extreme conditions (low connectivity, bright sunlight, physical peril, emotional panic), visual ambiguity is eliminated. Surfaces remain calm, legible, and uncluttered, allowing critical emergency statuses to register instantly across varying digital literacy levels.

Tone principles:
- **Authoritative & Calming:** Grounded slate surfaces eliminate visual noise, conveying institutional stability.
- **Immediate Triage:** Statuses require zero translation time. Alert states follow universal standard colors (Merah Darurat, Oranye Dalam Penanganan, Hijau Selesai, Biru Terverifikasi).
- **Direct & Action-Oriented:** Prominent interaction targets, clear linguistic cues in standard Indonesian (*Bahasa Indonesia yang lugas dan baku*), and zero superficial decorative ornaments.

## Colors
The color foundation utilizes a clinical, hyper-readable palette optimized for rapid scanning in critical situations.

### Core Palette
- **Primary (`#0F172A` - Slate 900):** Represents institutional authority, stability, and structure. Used for app chrome, primary headers, high-emphasis text, and core structural navigation.
- **Secondary / Merah Darurat (`#DC2626` - Crimson/Red 600):** Reserved exclusively for severe distress calls (Laporan Darurat), evacuation directives, life-threatening alerts, and destructive actions. Never used purely as an accent.
- **Tertiary / Oranye Dalam Penanganan (`#D97706` - Amber 600):** Designates active ongoing operations, rescue unit dispatch, pending verification, and advisory warnings.
- **Neutral (`#64748B` - Slate 500):** Handles structural borders, low-emphasis metadata, inactive icons, and timestamps.

### System Verification & Resolution Tokens
- **Biru Terverifikasi (`#2563EB` - Blue 600):** Official agency validation badges, verified authority accounts (BPBD, Basarnas, BMKG), and confirmed field reports.
- **Hijau Aman & Selesai (`#059669` - Emerald 600):** Resolved incident status, safe muster zones, confirmed headcount safety reports, and successful data synchronization.

### Semantic Backgrounds & Surfaces
- Background Primary: `#F8FAFC` (Slate 50) for reduced glare in high sunlight.
- Surface Elevated: `#FFFFFF` (White) with strict high-contrast borders (`#E2E8F0`).
- Emergency Surface Tints:
  - Red Tint: `#FEF2F2` (Red 50) with border `#FCA5A5` (Red 300).
  - Amber Tint: `#FFFBEB` (Amber 50) with border `#FCD34D` (Amber 300).
  - Blue Tint: `#EFF6FF` (Blue 50) with border `#93C5FD` (Blue 300).
  - Green Tint: `#ECFDF5` (Emerald 50) with border `#6EE7B7` (Emerald 300).

## Typography
Typography is configured for uncompromising legibility under duress.

**Public Sans** serves as the primary family for all headlines, informational narratives, and instructional body text. Derived from institutional government design standards, its neutral grotesque architecture offers generous apertures and distinct letterforms, preventing misreading during high-stress scenarios.

**Space Grotesk** is deployed across labels, categorical tags, telemetry (GPS coordinates, timestamps, casualties counters), and status chips. Its technical, monospaced-adjacent proportions provide tabular balance and distinguish situational data points from general textual descriptions.

Rules for typographic execution:
- Never set alert copy below `14px` (`body-md`).
- Telemetry coordinates, disaster status badges, and rapid timestamps must use uppercase `Space Grotesk` (`label-md` or `label-sm`) with `letterSpacing: 0.04em`.
- Emergency banner titles utilize `headline-sm` with a direct, unambiguous tone (e.g., "PERINGATAN DINI TSUNAMI").

## Layout & Spacing
The layout adheres to a strict responsive fluid grid. It enforces touch target safety for panic encounters (minimum interactive touch area: 48px).

### Screen Layout Principles
- **Mobile Handheld (360px - 767px):** Single-column layout. Gutters set to `gutter-mobile` (12px), canvas boundary margins to `margin-mobile` (16px). All primary action triggers (Panic Button, Laporkan Kejadian) stay anchored in the lower thumb zone.
- **Tablet & Field Terminal (768px - 1023px):** 6-column grid with `gutter` (16px) and `margin` (24px). Split-pane triage view (list on left, interactive geospatial map on right).
- **Desktop Command Center (1024px+):** 12-column grid. Used by emergency operators and government officials. Fixed side panel navigation with persistent active incident boards.

### Internal Spacing Rhythm
- Compact data clusters (coordinates, metadata lines): `space-xs` (4px) to `space-sm` (8px).
- Incident card internal padding: `space-md` (16px).
- Structural separations between incident feed blocks and alert banners: `space-lg` (24px).
- Section grouping dividers: `space-xl` (40px).

## Elevation & Depth
In disaster mitigation interfaces, artificial heavy shadows degrade screen contrast under direct sunlight and waste rendering cycles on low-end hardware. The system relies primarily on **Tonal Layers** combined with **Low-Contrast Outlines**, reserving shallow ambient elevation exclusively for floating emergency actions.

### Elevation Hierarchy
- **Level 0 (Base Canvas):** Background at `#F8FAFC`. Zero elevation, non-interactive base.
- **Level 1 (Card & Modular Modules):** `#FFFFFF` surface enclosed by a 1px border of `#E2E8F0`. No shadow. Crisp, architectural edge definition.
- **Level 2 (Active Incident / Pinned Cards):** `#FFFFFF` surface with a 1.5px border matching its status (e.g., `#FCA5A5` for emergency), accompanied by a tight ambient shadow: `box-shadow: 0 2px 4px -1px rgba(15, 23, 42, 0.06), 0 1px 2px -1px rgba(15, 23, 42, 0.04)`.
- **Level 3 (Sticky Alert Banners & Modal Dialogues):** Persistent top warning strips or critical full-screen action bottom sheets. Raised with: `box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.04)`.
- **Level 4 (Floating Emergency Panic Button):** High-priority fixed action button. High-contrast perimeter with focused drop shadow: `box-shadow: 0 12px 24px -4px rgba(220, 38, 38, 0.35)`.

## Shapes
The design system adopts **Soft (Level 1)** roundedness (`rounded = 4px`, `rounded-lg = 8px`, `rounded-xl = 12px`).

Curvature rules:
- **Functional Corners:** 4px to 8px radii provide modern ergonomics while maintaining an authoritative, serious, and structured visual posture.
- **Strictly No Pill Shapes for General UI:** Cards, inputs, and functional banners utilize 8px corners (`rounded-lg`) to maximize text area efficiency.
- **Status Badges & Chips:** Enclosed strictly with 4px radii (`rounded`) to communicate formal data taxonomy rather than casual consumer labeling.
- **Emergency Action Triggers:** Large call-to-action cards and location pins use 12px (`rounded-xl`) for tactile differentiation under emergency thumb-reach.

## Components

### 1. Alert Banners (Spanduk Peringatan)
- **Structure:** Full-width or boxed edge-to-edge container with an active 4px left-border indicator.
- **Styling:**
  - *Darurat (Severe):* `#FEF2F2` background, `#DC2626` left stripe, `#991B1B` text header, accompanied by a pulsing warning icon.
  - *Waspada (Warning):* `#FFFBEB` background, `#D97706` left stripe, `#92400E` text header.
- **Behavior:** Pinned directly below system bars. Must not be dismissible if flagged as an active regional evacuation or weather advisory.

### 2. Status Chips & Badges
- **Tokens:**
  - **Merah Darurat:** Text `#DC2626`, Background `#FEF2F2`, Border `#FCA5A5`.
  - **Oranye Dalam Penanganan:** Text `#D97706`, Background `#FFFBEB`, Border `#FCD34D`.
  - **Biru Terverifikasi:** Text `#2563EB`, Background `#EFF6FF`, Border `#93C5FD`.
  - **Hijau Selesai:** Text `#059669`, Background `#ECFDF5`, Border `#6EE7B7`.
- **Typography:** `label-sm` in `Space Grotesk`, uppercase, medium weight with a prefixed micro-dot indicator (6px).

### 3. Emergency & Standard Buttons
- **Critical SOS / Tombol Darurat:** Solid `#DC2626` fill, `#FFFFFF` text, `headline-sm` font, min-height 54px. Includes continuous subtle ring ping animation for quick spatial orientation.
- **Primary Action (Kirim Laporan):** Solid `#0F172A` fill, `#FFFFFF` text, `label-lg` font, 48px height, 8px radius.
- **Secondary Action:** Outlined 1.5px `#CBD5E1`, text `#0F172A`, background transparent, hover/pressed state `#F1F5F9`.

### 4. Incident Feed Cards (Kartu Laporan Warga)
- **Base:** `#FFFFFF` background, 1px `#E2E8F0` border, 8px radius, `space-md` padding.
- **Header:** Displays citizen anonymity state or verified badge on the left, timestamp (using `label-sm` Space Grotesk) on the right.
- **Body:** Concise incident summary (`body-md`), accompanied by thumbnail photographic proof with a clear 1:1 aspect ratio anchor.
- **Footer:** Exact geographical administrative label (e.g., "Kec. Cugenang, Kab. Cianjur") with an active map-pin icon and current triage status chip.

### 5. Input Fields & Form Controls
- **Inputs:** 44px min-height, 1.5px `#CBD5E1` border, 8px radius, `#FFFFFF` background. Focused state shifts border to `#0F172A` with zero glow.
- **Offline / Low-Connection Indicator Field:** Persistent bottom banner indicating queued local caching (`Laporan disimpan lokal - Menunggu koneksi`), rendered in `#334155` dark slate.
- **Selection Checks & Radios:** High-contrast 20px squares/circles, active fill `#0F172A` with bold white inner checks to ensure clarity for elderly and visually impaired citizens.