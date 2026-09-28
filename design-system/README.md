# Brand Design System

A design-token foundation derived directly from the brand logo. Everything traces
back to three colors pulled from [`logo.svg`](../logo.svg):

| Role        | Hex       | Token            |
|-------------|-----------|------------------|
| Navy        | `#093162` | `--navy-800`     |
| Teal        | `#3cb4b0` | `--teal-400`     |
| Off-white   | `#f7f8fa` | `--neutral-50`   |

**Tone:** refined fintech / trust — navy-led, calm, precise, with teal as a
focused accent and generous neutral space.

## Files

| File                                       | What it is |
|--------------------------------------------|------------|
| [`tokens.css`](tokens.css)                 | CSS custom properties. **Load this first.** Two layers: primitives + semantic roles, plus a dark theme. |
| [`design-tokens.json`](design-tokens.json) | Machine-readable source of truth. Style Dictionary–style `{primitive}` / `{semantic}` references — feed it to a build step to generate iOS/Android/JS/Tailwind outputs. |
| [`components.css`](components.css)          | Optional reference component recipes (buttons, inputs, cards, badges, alerts) built only from semantic tokens. |

## Quick start

```html
<link rel="stylesheet" href="design-system/tokens.css" />
<link rel="stylesheet" href="design-system/components.css" /> <!-- optional -->
```

```css
.checkout-cta {
  background: var(--color-primary);      /* navy 800 */
  color: var(--color-on-primary);
  padding: var(--space-3) var(--space-6);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  font-family: var(--font-sans);
}
```

## Token layers — the one rule

There are two layers, and the rule is simple:

1. **Primitives** (`--navy-800`, `--space-4`, `--text-lg`) — the raw scale. This
   is the source of truth. **Do not reference primitives in product code.**
2. **Semantics** (`--color-primary`, `--color-text`, `--color-border`) — role
   aliases that point at primitives. **Build all UI against these.**

Why: a re-theme, a dark mode, or a palette tweak becomes a change to the semantic
layer only — product code never moves. This is also why dark mode works without
touching a single component (see below).

## Theming / dark mode

The semantic layer is re-pointed for dark mode; primitives are untouched.

- **Automatic:** respects `prefers-color-scheme` out of the box.
- **Manual override:** set `data-theme` on the root element to force a mode.

```html
<html data-theme="dark">   <!-- or data-theme="light" -->
```

## What's in the system

- **Color** — navy / teal / neutral ramps (50–900), plus success / warning /
  danger / info status sets (base + subtle bg + border + text each).
- **Typography** — `--font-serif` (Newsreader) for display, `--font-sans`
  (Libre Franklin) for body/UI, `--font-mono` (IBM Plex Mono). Minor-third type
  scale (`--text-xs` → `--text-5xl`), weights, line-heights, tracking.
- **Spacing** — 4px base grid, `--space-0` → `--space-24`.
- **Radii** — `--radius-sm` → `--radius-2xl`, `--radius-full`.
- **Elevation** — five navy-tinted shadow levels.
- **Motion** — durations + easing curves.
- **Layering** — a named `--z-*` scale.

> The font families are a **recommended pairing**, not a mandate. Swap
> `--font-serif` / `--font-sans` for the brand's licensed typefaces in one place;
> everything downstream follows. Remember to actually load the web fonts (self-host
> or CDN) — the tokens only declare the stacks.

## Logo usage

- **Clear space:** keep padding of at least the logo's cap-height on all sides.
- **Minimum size:** don't render the wordmark below ~120px wide (icon-only below ~24px).
- **Backgrounds:** use the full-color logo on `--color-surface` or `--neutral-50`.
  On navy, use a single-color (off-white) treatment.
- **Don't:** recolor outside the brand palette, stretch, add effects, or place
  the full-color mark on a busy photo.

## Accessibility

- Body text on `--color-bg`/`--color-surface` uses `--color-text` (navy 900) —
  well above WCAG AA.
- Teal at `--teal-400` is a mid-tone: use `--color-text-accent` (teal 600) for
  accent **text** on light surfaces; reserve `--teal-400` for large text, icons,
  focus rings, and fills — verify contrast for any small text use.
- Every interactive component ships a visible focus state via `--focus-ring`.
- Never signal state with color alone — pair status colors with an icon or label.

## Regenerating platform outputs

`design-tokens.json` is the portable source. Point [Style Dictionary](https://amzn.github.io/style-dictionary/)
(or a similar transformer) at it to emit the CSS above plus Swift, Kotlin, JS,
or Tailwind theme files, keeping every platform in sync from one file.
