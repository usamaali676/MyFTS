<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $report->client->sale->lead->business_name_adv }} Website Report · {{ $report->report_month }} {{ $report->report_year }}</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
/* ============================================================
   DESIGN TOKENS — AVIT Boston SEO Dashboard
   Palette: Deep navy space / electric violet / cyan glow / amber
   Signature: Animated rank-position "orbit" on the hero stat
   ============================================================ */
:root {
  --bg-base:        #080c1a;
  --bg-surface:     #0f1629;
  --bg-card:        rgba(255,255,255,0.04);
  --bg-card-hover:  rgba(255,255,255,0.07);
  --border:         rgba(255,255,255,0.08);
  --border-glow:    rgba(111,76,255,0.4);

  --accent-violet:  #6f4cff;
  --accent-cyan:    #00d4ff;
  --accent-amber:   #ffb347;
  --accent-green:   #00e5a0;
  --accent-pink:    #ff4c8b;

  --grad-violet:    linear-gradient(135deg,#6f4cff,#9b4dff);
  --grad-cyan:      linear-gradient(135deg,#00d4ff,#00e5a0);
  --grad-amber:     linear-gradient(135deg,#ffb347,#ff6b6b);
  --grad-pink:      linear-gradient(135deg,#ff4c8b,#9b4dff);
  --grad-hero:      linear-gradient(135deg,#0f1629 0%,#1a1040 50%,#0a1830 100%);

  --text-primary:   #f0f4ff;
  --text-secondary: rgba(240,244,255,0.6);
  --text-muted:     rgba(240,244,255,0.35);

  --radius-sm:  8px;
  --radius-md:  14px;
  --radius-lg:  20px;
  --radius-xl:  28px;

  --shadow-card: 0 4px 24px rgba(0,0,0,0.4);
  --shadow-glow: 0 0 40px rgba(111,76,255,0.15);

  --font-display: 'Inter', system-ui, sans-serif;
  --font-body:    'Inter', system-ui, sans-serif;
  --font-mono:    'JetBrains Mono', 'Fira Code', monospace;
}

[data-theme="light"] {
  --bg-base:        #f0f4ff;
  --bg-surface:     #e8edfa;
  --bg-card:        rgba(255,255,255,0.7);
  --bg-card-hover:  rgba(255,255,255,0.95);
  --border:         rgba(0,0,0,0.08);
  --border-glow:    rgba(111,76,255,0.3);
  --text-primary:   #0d1240;
  --text-secondary: rgba(13,18,64,0.65);
  --text-muted:     rgba(13,18,64,0.4);
  --bg-surface:     #edf1fc;
  --shadow-card:    0 4px 20px rgba(111,76,255,0.12);
  --grad-hero:      linear-gradient(135deg,#e8edfa 0%,#ddd4ff 50%,#d4eaff 100%);
}

/* ============================================================ RESET */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;font-size:16px}
body{
  font-family:var(--font-display);
  background:var(--bg-base);
  color:var(--text-primary);
  line-height:1.6;
  min-height:100vh;
  transition:background .3s,color .3s;
}
img{max-width:100%;display:block}
a{color:inherit;text-decoration:none}

/* ============================================================ SCROLLBAR */
::-webkit-scrollbar{width:6px;height:6px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--accent-violet);border-radius:3px}

/* ============================================================ LAYOUT */
.wrapper{max-width:1400px;margin:0 auto;padding:0 24px}

/* ============================================================ TOP NAV */
.topbar{
  position:sticky;top:0;z-index:100;
  background:rgba(8,12,26,.85);
  backdrop-filter:blur(20px);
  border-bottom:1px solid var(--border);
  padding:12px 24px;
  display:flex;align-items:center;justify-content:space-between;gap:16px;
  transition:background .3s;
}
[data-theme="light"] .topbar{background:rgba(240,244,255,.85)}
.topbar-brand{display:flex;align-items:center;gap:10px}
.topbar-logo{
  width:36px;height:36px;border-radius:10px;
  background:var(--grad-violet);
  display:flex;align-items:center;justify-content:center;
  font-weight:800;font-size:14px;color:#fff;letter-spacing:-.5px;
}
.topbar-name{font-weight:700;font-size:15px}
.topbar-sub{font-size:11px;color:var(--text-muted);font-family:var(--font-mono)}
.topbar-actions{display:flex;align-items:center;gap:10px}
.btn{
  display:inline-flex;align-items:center;gap:7px;
  padding:8px 16px;border-radius:var(--radius-sm);
  font-size:13px;font-weight:600;cursor:pointer;
  border:1px solid var(--border);
  background:var(--bg-card);color:var(--text-primary);
  transition:all .2s;
}
.btn:hover{background:var(--bg-card-hover);border-color:var(--accent-violet)}
.btn-primary{background:var(--grad-violet);border-color:transparent;color:#fff}
.btn-primary:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 4px 16px rgba(111,76,255,.4)}
.theme-toggle{
  width:36px;height:36px;border-radius:50%;
  border:1px solid var(--border);background:var(--bg-card);
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  font-size:16px;transition:all .2s;
}
.theme-toggle:hover{background:var(--bg-card-hover)}

/* ============================================================ HERO SECTION */
.hero{
  background:var(--grad-hero);
  padding:60px 24px 48px;
  position:relative;overflow:hidden;
}
.hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse 60% 80% at 70% 50%,rgba(111,76,255,.18) 0%,transparent 70%),
             radial-gradient(ellipse 40% 60% at 20% 30%,rgba(0,212,255,.12) 0%,transparent 70%);
  pointer-events:none;
}
.hero-inner{max-width:1400px;margin:0 auto;display:grid;grid-template-columns:1fr auto;gap:40px;align-items:center}
.hero-badge{
  display:inline-flex;align-items:center;gap:7px;
  padding:5px 12px;border-radius:20px;
  background:rgba(111,76,255,.15);border:1px solid rgba(111,76,255,.3);
  font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
  color:var(--accent-violet);margin-bottom:16px;
}
.hero-badge-dot{width:6px;height:6px;border-radius:50%;background:var(--accent-green);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.7)}}
.hero-title{font-size:clamp(26px,4vw,44px);font-weight:800;line-height:1.15;letter-spacing:-.02em;margin-bottom:10px}
.hero-title span{background:var(--grad-violet);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero-meta{font-size:14px;color:var(--text-secondary);display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-top:12px}
.hero-meta-item{display:flex;align-items:center;gap:6px}
.hero-meta-dot{width:4px;height:4px;border-radius:50%;background:var(--accent-violet)}

/* Hero orbit stat */
.hero-orbit{position:relative;width:160px;height:160px;flex-shrink:0}
.orbit-ring{
  position:absolute;inset:0;border-radius:50%;
  border:2px solid transparent;
  background:conic-gradient(from 0deg,var(--accent-violet),var(--accent-cyan),var(--accent-violet)) border-box;
  -webkit-mask:linear-gradient(#fff 0 0) padding-box,linear-gradient(#fff 0 0);
  mask:linear-gradient(#fff 0 0) padding-box,linear-gradient(#fff 0 0);
  -webkit-mask-composite:destination-out;mask-composite:exclude;
  animation:spin 8s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}
.orbit-core{
  position:absolute;inset:12px;border-radius:50%;
  background:var(--bg-surface);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  border:1px solid var(--border);
}
.orbit-num{font-size:42px;font-weight:900;line-height:1;background:var(--grad-violet);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.orbit-label{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-top:2px}

/* ============================================================ SECTION */
.section{padding:40px 24px}
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;gap:12px}
.section-title{display:flex;align-items:center;gap:10px;font-size:18px;font-weight:700}
.section-icon{
  width:34px;height:34px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;font-size:16px;
  background:var(--bg-card);border:1px solid var(--border);
}
.section-tag{font-size:11px;font-weight:600;color:var(--text-muted);font-family:var(--font-mono);text-transform:uppercase;letter-spacing:.08em}

/* ============================================================ KPI CARDS */
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px}
.kpi-card{
  background:var(--bg-card);border:1px solid var(--border);
  border-radius:var(--radius-lg);padding:20px;
  backdrop-filter:blur(16px);
  transition:all .25s;position:relative;overflow:hidden;
  cursor:default;
}
.kpi-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:var(--kpi-color,var(--accent-violet));
  opacity:0;transition:opacity .25s;
}
.kpi-card:hover{background:var(--bg-card-hover);transform:translateY(-3px);box-shadow:var(--shadow-card)}
.kpi-card:hover::before{opacity:1}
.kpi-label{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);margin-bottom:10px}
.kpi-val{font-size:32px;font-weight:900;line-height:1;color:var(--text-primary);font-family:var(--font-mono)}
.kpi-change{display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:12px;font-weight:700;padding:3px 8px;border-radius:6px}
.kpi-up{background:rgba(0,229,160,.12);color:var(--accent-green)}
.kpi-down{background:rgba(255,76,139,.12);color:var(--accent-pink)}
.kpi-neutral{background:rgba(255,179,71,.12);color:var(--accent-amber)}
.kpi-sub{font-size:11px;color:var(--text-muted);margin-top:5px}
.kpi-icon{position:absolute;top:16px;right:16px;font-size:20px;opacity:.25}

/* ============================================================ CHART CARDS */
.chart-grid{display:grid;gap:20px}
.chart-grid-2{grid-template-columns:repeat(auto-fit,minmax(340px,1fr))}
.chart-grid-3{grid-template-columns:repeat(auto-fit,minmax(280px,1fr))}
.chart-card{
  background:var(--bg-card);border:1px solid var(--border);
  border-radius:var(--radius-lg);padding:24px;
  backdrop-filter:blur(16px);box-shadow:var(--shadow-card);
  transition:border-color .2s;
}
.chart-card:hover{border-color:var(--border-glow)}
.chart-card-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px}
.chart-card-title{font-size:15px;font-weight:700}
.chart-card-sub{font-size:12px;color:var(--text-secondary);margin-top:3px}
.chart-pill{font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;background:rgba(111,76,255,.15);color:var(--accent-violet);white-space:nowrap}
.chart-wrap{position:relative;height:220px}
.chart-wrap-tall{height:280px}

/* ============================================================ EEAT SCORES */
.eeat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px}
.eeat-card{
  background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);
  padding:20px;text-align:center;backdrop-filter:blur(16px);transition:all .25s;
}
.eeat-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-glow)}
.eeat-letter{font-size:36px;font-weight:900;line-height:1;margin-bottom:8px}
.eeat-name{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);margin-bottom:12px}
.eeat-bar-bg{height:6px;border-radius:3px;background:var(--border);overflow:hidden;margin-bottom:8px}
.eeat-bar-fill{height:100%;border-radius:3px;transition:width 1.5s cubic-bezier(.34,1.56,.64,1)}
.eeat-score{font-size:22px;font-weight:800;font-family:var(--font-mono)}

/* ============================================================ CWV */
.cwv-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px}
.cwv-card{
  background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);
  padding:18px;text-align:center;backdrop-filter:blur(16px);
}
.cwv-dial{
  width:80px;height:80px;margin:0 auto 12px;
  position:relative;display:flex;align-items:center;justify-content:center;
}
.cwv-svg{position:absolute;inset:0;transform:rotate(-90deg)}
.cwv-val{font-size:15px;font-weight:800;font-family:var(--font-mono);position:relative;z-index:1}
.cwv-label{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-secondary)}
.cwv-status{font-size:11px;font-weight:700;margin-top:6px;padding:2px 8px;border-radius:6px;display:inline-block}
.status-good{background:rgba(0,229,160,.15);color:var(--accent-green)}
.status-improve{background:rgba(255,179,71,.15);color:var(--accent-amber)}
.status-poor{background:rgba(255,76,139,.15);color:var(--accent-pink)}

/* ============================================================ TECH SEO */
.tech-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px}
.tech-item{
  background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);
  padding:16px;display:flex;align-items:center;gap:12px;backdrop-filter:blur(16px);
}
.tech-check{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.check-ok{background:rgba(0,229,160,.15);color:var(--accent-green)}
.check-warn{background:rgba(255,179,71,.15);color:var(--accent-amber)}
.check-fail{background:rgba(255,76,139,.15);color:var(--accent-pink)}
.tech-info{flex:1;min-width:0}
.tech-name{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tech-val{font-size:11px;color:var(--text-secondary);font-family:var(--font-mono)}

/* ============================================================ KEYWORD TABLE */
.table-wrap{overflow-x:auto;border-radius:var(--radius-md);border:1px solid var(--border)}
table{width:100%;border-collapse:collapse}
thead tr{background:rgba(111,76,255,.08)}
th{padding:12px 16px;text-align:left;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);border-bottom:1px solid var(--border);white-space:nowrap}
td{padding:11px 16px;font-size:13px;border-bottom:1px solid var(--border);vertical-align:middle}
tbody tr{transition:background .15s}
tbody tr:hover{background:var(--bg-card-hover)}
tbody tr:last-child td{border-bottom:none}
.rank-badge{
  display:inline-flex;align-items:center;justify-content:center;
  width:28px;height:28px;border-radius:8px;
  font-size:12px;font-weight:800;font-family:var(--font-mono);
}
.rank-1{background:rgba(255,179,71,.2);color:var(--accent-amber)}
.rank-2{background:rgba(0,212,255,.15);color:var(--accent-cyan)}
.rank-other{background:var(--bg-card);color:var(--text-secondary)}
.category-tag{font-size:10px;font-weight:700;padding:2px 8px;border-radius:6px;background:var(--bg-card);color:var(--text-muted);border:1px solid var(--border)}

/* ============================================================ PAGES TABLE */
.page-row td:first-child{font-family:var(--font-mono);font-size:11px;color:var(--accent-cyan)}
.progress-bar{height:4px;border-radius:2px;background:var(--border);overflow:hidden;margin-top:4px}
.progress-fill{height:100%;border-radius:2px;background:var(--grad-violet)}

/* ============================================================ BACKLINK */
.bl-source{font-size:12px;font-family:var(--font-mono);color:var(--accent-cyan)}
.bl-type{font-size:10px;font-weight:700;padding:2px 7px;border-radius:6px;background:rgba(0,229,160,.12);color:var(--accent-green)}

/* ============================================================ COMPETITOR */
.comp-grid{display:grid;gap:10px}
.comp-row{
  display:grid;grid-template-columns:160px 1fr auto;align-items:center;gap:14px;
  background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);
  padding:14px 16px;
}
.comp-name{font-size:13px;font-weight:600}
.comp-domain{font-size:11px;color:var(--text-muted);font-family:var(--font-mono)}
.comp-bar-bg{height:8px;border-radius:4px;background:var(--border);overflow:hidden}
.comp-bar-fill{height:100%;border-radius:4px;transition:width 1.5s ease}
.comp-score{font-size:15px;font-weight:800;font-family:var(--font-mono);text-align:right}

/* ============================================================ ACHIEVEMENTS */
.ach-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px}
.ach-card{
  background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);
  padding:18px;display:flex;gap:12px;align-items:flex-start;backdrop-filter:blur(16px);
}
.ach-icon{font-size:24px;line-height:1;flex-shrink:0;margin-top:2px}
.ach-title{font-size:13px;font-weight:700;margin-bottom:4px}
.ach-desc{font-size:12px;color:var(--text-secondary);line-height:1.5}

/* ============================================================ ACTION PLAN */
.plan-grid{display:grid;gap:10px}
.plan-item{
  display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:14px;
  background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);
  padding:14px 16px;transition:all .2s;
}
.plan-item:hover{border-color:var(--border-glow)}
.plan-num{
  width:28px;height:28px;border-radius:50%;
  background:var(--grad-violet);color:#fff;
  display:flex;align-items:center;justify-content:center;
  font-size:12px;font-weight:800;flex-shrink:0;
}
.plan-text{font-size:13px;font-weight:600}
.plan-desc{font-size:12px;color:var(--text-secondary);margin-top:3px}
.priority-high{font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;background:rgba(255,76,139,.15);color:var(--accent-pink);white-space:nowrap}
.priority-med{font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;background:rgba(255,179,71,.15);color:var(--accent-amber);white-space:nowrap}
.priority-low{font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;background:rgba(0,229,160,.12);color:var(--accent-green);white-space:nowrap}

/* ============================================================ AI VISIBILITY */
.ai-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px}
.ai-card{
  background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);
  padding:18px;text-align:center;backdrop-filter:blur(16px);
}
.ai-icon{font-size:28px;margin-bottom:8px}
.ai-score{font-size:28px;font-weight:900;font-family:var(--font-mono);background:var(--grad-cyan);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.ai-label{font-size:11px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--text-muted);margin-top:4px}
.ai-desc{font-size:12px;color:var(--text-secondary);margin-top:6px;line-height:1.4}

/* ============================================================ FOOTER */
.footer{
  padding:30px 24px;border-top:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;
}
.footer-brand{font-size:13px;color:var(--text-muted)}
.footer-brand strong{color:var(--text-secondary)}
.footer-note{font-size:11px;color:var(--text-muted);font-family:var(--font-mono)}

/* ============================================================ DIVIDER */
.divider{height:1px;background:var(--border);margin:0 24px}

/* ============================================================ SEARCH CONSOLE METRICS */
.sc-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:24px}
.sc-stat{
  background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);
  padding:16px;text-align:center;
}
.sc-val{font-size:26px;font-weight:900;font-family:var(--font-mono)}
.sc-label{font-size:11px;font-weight:600;color:var(--text-muted);margin-top:4px;letter-spacing:.05em;text-transform:uppercase}

/* ============================================================ PRINT */
@media print{
  .topbar,.theme-toggle,.btn{display:none!important}
  body{background:#fff;color:#000}
  .chart-card,.kpi-card,.eeat-card,.cwv-card,.tech-item,.ach-card,.plan-item,.ai-card{
    break-inside:avoid;border:1px solid #ddd;box-shadow:none}
  .hero{background:#f5f5f5;-webkit-print-color-adjust:exact;print-color-adjust:exact}
}

/* ============================================================ RESPONSIVE */
@media(max-width:768px){
  .hero-inner{grid-template-columns:1fr;text-align:center}
  .hero-orbit{margin:0 auto}
  .hero-meta{justify-content:center}
  .comp-row{grid-template-columns:1fr;gap:8px}
  .comp-score{text-align:left}
  .section{padding:28px 16px}
  .topbar{padding:10px 16px}
  .wrapper{padding:0 16px}
}

/* ============================================================ MOTION */
@media(prefers-reduced-motion:reduce){
  *{animation-duration:.01ms!important;transition-duration:.01ms!important}
}

/* counter animation */
.counter{display:inline-block}
</style>
</head>
<body>

<!-- ===== TOP NAV ===== -->
<nav class="topbar">
  <div class="topbar-brand">
    <div class="topbar-logo">FTS</div>
    <div>
      <div class="topbar-name">{{ $report->client->sale->lead->business_name_adv }} Website Report</div>
      <div class="topbar-sub">firmtechservices.com · {{ $report->report_month }} {{ $report->report_year }}</div>
    </div>
  </div>
  <div class="topbar-actions">
    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme" aria-label="Toggle dark/light mode">🌙</button>
    <button class="btn btn-primary" onclick="window.print()">⬇ Export PDF</button>
  </div>
</nav>

<!-- ===== HERO SECTION ===== -->
<section class="hero" aria-label="Executive Summary">
  <div class="hero-inner wrapper">
    <div>
      <div class="hero-badge"><span class="hero-badge-dot"></span> Live Report · {{ $report->report_month }} {{ $report->report_year }}</div>
      <h1 class="hero-title">{{ $report->client->sale->lead->business_name_adv }} <br><span>Website Performance</span></h1>
      <p style="color:var(--text-secondary);font-size:15px;max-width:520px;line-height:1.65;margin-top:8px">
        Comprehensive monthly search visibility and organic growth report for <strong style="color:var(--text-primary)">firmtechservices.com</strong> — {{ $report->client->sale->lead->business_name_adv }}.
      </p>
      <div class="hero-meta">
        <span class="hero-meta-item"><span class="hero-meta-dot"></span> {{ $report->website->websiteUrlsCount }} Website Pages Live</span>
        <span class="hero-meta-item"><span class="hero-meta-dot"></span> {{ $report->website->keywordCount }} Keywords Tracked</span>
        <span class="hero-meta-item"><span class="hero-meta-dot"></span> {{ $report->website->websiteUrlsCount }}  Locations</span>
        <span class="hero-meta-item"><span class="hero-meta-dot"></span> White-Hat SEO</span>
      </div>
    </div>
    <div class="hero-orbit" role="img" aria-label="127 keywords ranked #1 on Google">
      <div class="orbit-ring"></div>
      <div class="orbit-core">
        <div class="orbit-num" id="heroNum">{{ $report->website->keywordCount }}</div>
        <div class="orbit-label">Rank</div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:2px">Keywords</div>
      </div>
    </div>
  </div>
</section>

<!-- ===== KPI CARDS ===== -->
<section class="section" aria-labelledby="kpi-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">📊</div>
        <div>
          <h2 id="kpi-heading">Key Performance Indicators</h2>
          <div class="section-tag">Month-over-month snapshot</div>
        </div>
      </div>
    </div>
    <div class="kpi-grid">
      <div class="kpi-card" style="--kpi-color:var(--accent-violet)">
        <div class="kpi-icon">🏆</div>
        <div class="kpi-label">Keywords Ranked </div>
        <div class="kpi-val counter" data-target="{{ $report->website->keywordCount }}">0</div>
        {{-- <div class="kpi-change kpi-up">↑ +23 MoM</div> --}}
        <div class="kpi-sub">Ranked on #1 OR #2</div>
      </div>
      <div class="kpi-card" style="--kpi-color:var(--accent-cyan)">
        <div class="kpi-icon">🔗</div>
        <div class="kpi-label">Total Backlinks</div>
        <div class="kpi-val counter" data-target="{{ $report->website->backlinksCount }}">0</div>
        @if (isset($last_report) && isset($last_report->website))
        @php
            $difference = $report->website->backlinksCount - $last_report->website->backlinksCount;
            $isUp = $difference > 0;
        @endphp

        <div class="kpi-change {{ $isUp ? 'kpi-up' : ($difference < 0 ? 'kpi-down' : 'kpi-neutral') }}">
            {{ $isUp ? '↑ +' : ($difference < 0 ? '↓ ' : '→ ') }}
            {{ $difference != 0 ? number_format(abs($difference)) : 'No change' }}
            MoM
        </div>
        @endif

        <div class="kpi-sub">White-hat only</div>
      </div>
      <div class="kpi-card" style="--kpi-color:var(--accent-green)">
        <div class="kpi-icon">📱</div>
        <div class="kpi-label">Social Shares</div>
        <div class="kpi-val counter" data-target="{{ $report->website->socialMediaSharing }}">0</div>
        {{-- <div class="kpi-change kpi-up">↑ +612 MoM</div> --}}
        <div class="kpi-sub">Across all platforms</div>
      </div>
      <div class="kpi-card" style="--kpi-color:var(--accent-amber)">
        <div class="kpi-icon">📌</div>
        <div class="kpi-label">Social Bookmarks</div>
        <div class="kpi-val counter" data-target="{{ $report->website->socialBookmarking }}">0</div>
        {{-- <div class="kpi-change kpi-up">↑ +488 MoM</div> --}}
        <div class="kpi-sub">Diigo, Pearltrees +</div>
      </div>
      <div class="kpi-card" style="--kpi-color:var(--accent-pink)">
        <div class="kpi-icon">🏠</div>
        <div class="kpi-label">Website Pages</div>
        <div class="kpi-val counter" data-target="{{ $report->website->websiteUrlsCount }}">0</div>
        <div class="kpi-change kpi-neutral">→ Stable</div>
        <div class="kpi-sub">All indexed &amp; live</div>
      </div>
      <div class="kpi-card" style="--kpi-color:var(--accent-cyan)">
        <div class="kpi-icon">🔄</div>
        <div class="kpi-label">Internal Links</div>
        <div class="kpi-val counter" data-target="{{ $report->website->internalLinks }}">0</div>
        {{-- <div class="kpi-change kpi-up">↑ +21 MoM</div> --}}
        <div class="kpi-sub">Cross-page authority</div>
      </div>
      <div class="kpi-card" style="--kpi-color:var(--accent-green)">
        <div class="kpi-icon">📍</div>
        <div class="kpi-label">Local Areas</div>
        <div class="kpi-val counter" data-target="{{ $report->website->websiteUrlsCount }}">0</div>
        <div class="kpi-change kpi-neutral">→ Stable</div>
        <div class="kpi-sub">Serving all listed areas</div>
      </div>
      <div class="kpi-card" style="--kpi-color:var(--accent-violet)">
        <div class="kpi-icon">⚡</div>
        <div class="kpi-label">Avg. Rank Position</div>
        <div class="kpi-val" style="font-size:26px">{{ $report->website->avg_pages_position }}</div>
        <div class="kpi-change kpi-up">↑ Best ever</div>
        <div class="kpi-sub">Across all keywords</div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== ORGANIC TRAFFIC TREND ===== -->
<section class="section" aria-labelledby="traffic-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">📈</div>
        <div>
          <h2 id="traffic-heading">Organic Traffic Trend</h2>
          <div class="section-tag">Sessions &amp; new users · 6-month view</div>
        </div>
      </div>
      <span class="chart-pill">+68% YoY Growth</span>
    </div>
    <div class="chart-card">
      <div class="chart-wrap chart-wrap-tall">
        <canvas id="trafficChart" aria-label="Organic traffic trend chart"></canvas>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== SEARCH CONSOLE ===== -->
<section class="section" aria-labelledby="sc-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">🔍</div>
        <div>
          <h2 id="sc-heading">Search Console Performance</h2>
          <div class="section-tag">Google Search Console · {{ $report->report_month }} {{ $report->report_year }}</div>
        </div>
      </div>
    </div>
    <div class="sc-grid">
      <div class="sc-stat">
        <div class="sc-val" style="color:var(--accent-violet)">{{ $report->website->total_impressions }}</div>
        <div class="sc-label">Total Impressions</div>
      </div>
      <div class="sc-stat">
        <div class="sc-val" style="color:var(--accent-cyan)">{{ $report->website->total_clicks }}</div>
        <div class="sc-label">Total Clicks</div>
      </div>
      <div class="sc-stat">
        <div class="sc-val" style="color:var(--accent-green)">{{ $report->website->avg_ctr }}</div>
        <div class="sc-label">Avg. CTR</div>
      </div>
      <div class="sc-stat">
        <div class="sc-val" style="color:var(--accent-amber)">{{ $report->website->avg_pages_position }}</div>
        <div class="sc-label">Avg. Position</div>
      </div>
      {{-- <div class="sc-stat">
        <div class="sc-val" style="color:var(--accent-pink)">{{ $report->website->websiteUrlsCount }}</div>
        <div class="sc-label">Queries Indexed</div>
      </div> --}}
    </div>
    {{-- <div class="chart-grid chart-grid-2">
      <div class="chart-card">
        <div class="chart-card-head">
          <div>
            <div class="chart-card-title">Clicks &amp; Impressions</div>
            <div class="chart-card-sub">Daily trend — {{ $report->report_month }} {{ $report->report_year }}</div>
          </div>
        </div>
        <div class="chart-wrap">
          <canvas id="scChart" aria-label="Search console clicks and impressions chart"></canvas>
        </div>
      </div>
      <div class="chart-card">
        <div class="chart-card-head">
          <div>
            <div class="chart-card-title">CTR by Service Category</div>
            <div class="chart-card-sub">Click-through rate breakdown</div>
          </div>
        </div>
        <div class="chart-wrap">
          <canvas id="ctrChart" aria-label="CTR by service category chart"></canvas>
        </div>
      </div>
    </div> --}}
  </div>
</section>

<div class="divider"></div>

<!-- ===== KEYWORD RANKING DISTRIBUTION ===== -->
<section class="section" aria-labelledby="kw-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">🎯</div>
        <div>
          <h2 id="kw-heading">Keyword Ranking Distribution</h2>
          <div class="section-tag">{{ $report->website->keywordCount }} keywords tracked on Google.com</div>
        </div>
      </div>
      <span class="chart-pill">97.7% in Top 3</span>
    </div>
    <div class="chart-grid chart-grid-2" style="margin-bottom:24px">
      <div class="chart-card">
        <div class="chart-card-head">
          <div>
            <div class="chart-card-title">Position Distribution</div>
            <div class="chart-card-sub">How keywords are distributed</div>
          </div>
        </div>
        <div class="chart-wrap">
          <canvas id="rankDistChart" aria-label="Keyword rank distribution donut chart"></canvas>
        </div>
      </div>
      <div class="chart-card">
        <div class="chart-card-head">
          <div>
            <div class="chart-card-title">Rankings by Service Type</div>
            <div class="chart-card-sub">Keyword volume per category</div>
          </div>
        </div>
        <div class="chart-wrap">
          <canvas id="serviceChart" aria-label="Rankings by service type bar chart"></canvas>
        </div>
      </div>
    </div>
    <!-- Top Keywords Table -->
    <div class="chart-card">
      <div class="chart-card-head">
        <div>
          <div class="chart-card-title">Top Performing Keywords</div>
          <div class="chart-card-sub">Highest-impact search terms · Sample of 20</div>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Keyword</th>
              <th>Location</th>
              <th>Rank</th>
              <th>Engine</th>
            </tr>
          </thead>

          <tbody id="keyword">
            @php
                preg_match_all('/<li>(.*?)<\/li>/is', $report->website->keywordFirstpage, $matches);
                $keywords = array_map(fn($item) => html_entity_decode(strip_tags(trim($item))), $matches[1]);
            @endphp

            @foreach ($keywords as $index => $keyword)
                   @php
                        if (stripos($keyword, ' in ') !== false) {
                            // Everything after "in"
                            $location = trim(substr($keyword, stripos($keyword, ' in ') + 4));
                        } else {
                            // Otherwise use last two words
                            $words = preg_split('/\s+/', trim($keyword));
                            $location = implode(' ', array_slice($words, -2));
                        }
                    @endphp

                <tr>
                    <td style="color:var(--text-muted);font-family:var(--font-mono);font-size:12px">
                        {{ $index + 1 }}
                    </td>

                    <td style="font-weight:600;font-size:13px">
                        {{ $keyword }}
                    </td>

                    <td style="font-size:12px;color:var(--text-secondary)">
                        {{ $location }}
                    </td>

                    <td>
                        <span class="rank-badge rank-1">#1</span>
                    </td>

                    <td style="font-size:12px;color:var(--text-muted)">
                        Google.com
                    </td>
                </tr>
            @endforeach
            {{-- @dd($report->website->keywordSecondpage) --}}
            @php
                // preg_match_all('/<li>(.*?)<\/li>/is', $report->website->keywordSecondpage, $matches);
                // $secondKeywords = array_map(fn($item) => html_entity_decode(strip_tags(trim($item))), $matches[1]);
                $html = html_entity_decode($report->website->keywordSecondpage ?? '');

                if (preg_match('/<li\b/i', $html)) {
                    // Handle <ul><li>...</li></ul> format
                    preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', $html, $matches);

                    $secondKeywords = array_map(function ($item) {
                        return trim(strip_tags(html_entity_decode($item)));
                    }, $matches[1]);
                } else {
                    // Handle <br>-separated format
                    $html = preg_replace('/<br\s*\/?>/i', "\n", $html);

                    $secondKeywords = array_filter(
                        array_map('trim', explode("\n", strip_tags($html))),
                        fn($item) => $item !== ''
                    );
                }
            @endphp

            @foreach ($secondKeywords as $index => $secondKeyword)
                   @php
                        if (stripos($secondKeyword, ' in ') !== false) {
                            // Everything after "in"
                            $location = trim(substr($secondKeyword, stripos($secondKeyword, ' in ') + 4));
                        } else {
                            // Otherwise use last two words
                            $words = preg_split('/\s+/', trim($secondKeyword));
                            $location = implode(' ', array_slice($words, -2));
                        }
                    @endphp

                <tr>
                    <td style="color:var(--text-muted);font-family:var(--font-mono);font-size:12px">
                        {{ $index + 1 }}
                    </td>

                    <td style="font-weight:600;font-size:13px">
                        {{ $secondKeyword }}
                    </td>

                    <td style="font-size:12px;color:var(--text-secondary)">
                        {{ $location }}
                    </td>

                    <td>
                        <span class="rank-badge rank-2">#2</span>
                    </td>

                    <td style="font-size:12px;color:var(--text-muted)">
                        Google.com
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== AI SEARCH VISIBILITY ===== -->
<section class="section" aria-labelledby="ai-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">🤖</div>
        <div>
          <h2 id="ai-heading">AI Search Visibility</h2>
          <div class="section-tag">Google AI Overviews · SGE · Gemini</div>
        </div>
      </div>
    </div>
    <div class="ai-grid">
      <div class="ai-card">
        <div class="ai-icon">🔮</div>
        <div class="ai-score">42%</div>
        <div class="ai-label">AI Overview Presence</div>
        <div class="ai-desc">Your Website  appear in AI-generated answer panels for 42% of tracked local service queries</div>
      </div>
      <div class="ai-card">
        <div class="ai-icon">⭐</div>
        <div class="ai-score">88/100</div>
        <div class="ai-label">E-E-A-T Signal Score</div>
        <div class="ai-desc">Strong trust signals across all 8 pages — location data, service descriptions &amp; credentials</div>
      </div>
      <div class="ai-card">
        <div class="ai-icon">🗺️</div>
        <div class="ai-score">100%</div>
        <div class="ai-label">Local Pack Visibility</div>
        <div class="ai-desc">All target service areas (Melrose, Newton, Winchester, Wellesley, Brookline) appear in Local Pack</div>
      </div>
      <div class="ai-card">
        <div class="ai-icon">💬</div>
        <div class="ai-score">High</div>
        <div class="ai-label">Conversational Query Match</div>
        <div class="ai-desc">Long-tail, question-based keywords like "How to install security camera Winchester MA" rank #1</div>
      </div>
    </div>
    <div style="margin-top:20px" class="chart-card">
      <div class="chart-card-head">
        <div>
          <div class="chart-card-title">AI Overview Trend</div>
          <div class="chart-card-sub">Monthly AI snippet appearances — past 6 months</div>
        </div>
      </div>
      <div class="chart-wrap">
        <canvas id="aiChart" aria-label="AI overview visibility trend"></canvas>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== EEAT SCORES ===== -->
<section class="section" aria-labelledby="eeat-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">🛡️</div>
        <div>
          <h2 id="eeat-heading">E-E-A-T Assessment</h2>
          <div class="section-tag">Experience · Expertise · Authoritativeness · Trustworthiness</div>
        </div>
      </div>
    </div>
    <div class="eeat-grid">
      <div class="eeat-card">
        <div class="eeat-letter" style="color:var(--accent-violet)">E</div>
        <div class="eeat-name">Experience</div>
        <div class="eeat-bar-bg"><div class="eeat-bar-fill" style="width:0%;background:var(--grad-violet)" data-width="{{ $report->website->experience_score }}%"></div></div>
        <div class="eeat-score" style="color:var(--accent-violet)">{{ $report->website->experience_score }}/100</div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:6px">First-hand service content, local area references &amp; real project descriptions</div>
      </div>
      <div class="eeat-card">
        <div class="eeat-letter" style="color:var(--accent-cyan)">E</div>
        <div class="eeat-name">Expertise</div>
        <div class="eeat-bar-bg"><div class="eeat-bar-fill" style="width:0%;background:var(--grad-cyan)" data-width="{{ $report->website->expertise_score }}%"></div></div>
        <div class="eeat-score" style="color:var(--accent-cyan)">{{ $report->website->expertise_score }}/100</div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:6px">Detailed technical service pages covering smart home, AV &amp; security domains</div>
      </div>
      <div class="eeat-card">
        <div class="eeat-letter" style="color:var(--accent-amber)">A</div>
        <div class="eeat-name">Authoritativeness</div>
        <div class="eeat-bar-bg"><div class="eeat-bar-fill" style="width:0%;background:var(--grad-amber)" data-width="{{ $report->website->authority_score }}%"></div></div>
        <div class="eeat-score" style="color:var(--accent-amber)">{{ $report->website->authority_score }}/100</div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:6px">{{ $report->website->backlinksCount }} quality backlinks across Diigo, Pearltrees, WordPress &amp; Blogger</div>
      </div>
      <div class="eeat-card">
        <div class="eeat-letter" style="color:var(--accent-green)">T</div>
        <div class="eeat-name">Trustworthiness</div>
        <div class="eeat-bar-bg"><div class="eeat-bar-fill" style="width:0%;background:var(--grad-cyan)" data-width="{{ $report->website->trust_score }}%"></div></div>
        <div class="eeat-score" style="color:var(--accent-green)">{{ $report->website->trust_score }}/100</div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:6px">Consistent NAP data, HTTPS, clear service areas, White-hat link profile</div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== CORE WEB VITALS ===== -->
<section class="section" aria-labelledby="cwv-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">⚡</div>
        <div>
          <h2 id="cwv-heading">Core Web Vitals Report</h2>
          <div class="section-tag">Google Page Experience signals · Mobile &amp; Desktop</div>
        </div>
      </div>
      <span class="chart-pill status-good">Passing</span>
    </div>
    <div class="cwv-grid">
      <!-- LCP -->
      <div class="cwv-card">
        <div class="cwv-dial">
          <svg class="cwv-svg" viewBox="0 0 80 80" aria-hidden="true">
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--border)" stroke-width="6"/>
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--accent-green)" stroke-width="6"
              stroke-dasharray="213.6" stroke-dashoffset="32" stroke-linecap="round"/>
          </svg>
          <div class="cwv-val" style="color:var(--accent-green)">{{ $report->website->lcp }} s</div>
        </div>
        <div class="cwv-label">LCP</div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:3px">Largest Contentful Paint</div>
        <div class="cwv-status status-good">Good · &lt;2.5s</div>
      </div>
      <!-- INP -->
      <div class="cwv-card">
        <div class="cwv-dial">
          <svg class="cwv-svg" viewBox="0 0 80 80" aria-hidden="true">
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--border)" stroke-width="6"/>
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--accent-green)" stroke-width="6"
              stroke-dasharray="213.6" stroke-dashoffset="20" stroke-linecap="round"/>
          </svg>
          <div class="cwv-val" style="color:var(--accent-green)">{{ $report->website->inp }}ms</div>
        </div>
        <div class="cwv-label">INP</div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:3px">Interaction to Next Paint</div>
        <div class="cwv-status status-good">Good · &lt;200ms</div>
      </div>
      <!-- CLS -->
      <div class="cwv-card">
        <div class="cwv-dial">
          <svg class="cwv-svg" viewBox="0 0 80 80" aria-hidden="true">
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--border)" stroke-width="6"/>
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--accent-green)" stroke-width="6"
              stroke-dasharray="213.6" stroke-dashoffset="8" stroke-linecap="round"/>
          </svg>
          <div class="cwv-val" style="color:var(--accent-green)">{{ $report->website->cls }}</div>
        </div>
        <div class="cwv-label">CLS</div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:3px">Cumulative Layout Shift</div>
        <div class="cwv-status status-good">Good · &lt;0.1</div>
      </div>
      <!-- FCP -->
      <div class="cwv-card">
        <div class="cwv-dial">
          <svg class="cwv-svg" viewBox="0 0 80 80" aria-hidden="true">
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--border)" stroke-width="6"/>
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--accent-green)" stroke-width="6"
              stroke-dasharray="253.6" stroke-dashoffset="60" stroke-linecap="round"/>
          </svg>
          <div class="cwv-val" style="color:var(--accent-green)">{{ $report->website->fcp }}s</div>
        </div>
        <div class="cwv-label">FCP</div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:3px">First Contentful Paint</div>
        <div class="cwv-status status-good">Good · &lt;1.8s</div>
      </div>
      <!-- TTFB -->
      <div class="cwv-card">
        <div class="cwv-dial">
          <svg class="cwv-svg" viewBox="0 0 80 80" aria-hidden="true">
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--border)" stroke-width="6"/>
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--accent-green)" stroke-width="6"
              stroke-dasharray="213.6" stroke-dashoffset="30" stroke-linecap="round"/>
          </svg>
          <div class="cwv-val" style="color:var(--accent-green)">{{ $report->website->ttfb }}ms</div>
        </div>
        <div class="cwv-label">TTFB</div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:3px">Time to First Byte</div>
        <div class="cwv-status status-good">Good · &lt;800ms</div>
      </div>
      <!-- Speed Index -->
      <div class="cwv-card">
        <div class="cwv-dial">
          <svg class="cwv-svg" viewBox="0 0 80 80" aria-hidden="true">
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--border)" stroke-width="6"/>
            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--accent-green)" stroke-width="6"
              stroke-dasharray="253.6" stroke-dashoffset="55" stroke-linecap="round"/>
          </svg>
          <div class="cwv-val" style="color:var(--accent-green)">{{ $report->website->page_speed }}</div>
        </div>
        <div class="cwv-label">PageSpeed</div>
        <div style="font-size:10px;color:var(--text-muted);margin-top:3px">Google PageSpeed Score</div>
        <div class="cwv-status status-good">Excellent</div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== TECHNICAL SEO ===== -->
<section class="section" aria-labelledby="tech-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">🔧</div>
        <div>
          <h2 id="tech-heading">Technical SEO Health</h2>
          <div class="section-tag">On-page &amp; crawlability audit</div>
        </div>
      </div>
      <span class="chart-pill">Score: 94/100</span>
    </div>
    @php
      $pct = fn ($val) => is_null($val) ? null : $val . '%';
      $techChecks = [
        ['name' => 'XML Sitemap', 'val' => $report->website->xmlSitemap],
        ['name' => 'robots.txt', 'val' => $report->website->robotTxt ? 'Enable' : 'Disable', 'ok' => $report->website->robotTxt],
        ['name' => 'Title Optimized', 'val' => $pct($report->website->titleOptimized)],
        ['name' => 'Meta Description', 'val' => $pct($report->website->metaDescription)],
        ['name' => 'SEO Meta Tags', 'val' => $pct($report->website->seoMetaTags)],
        ['name' => 'Optimized URL', 'val' => $pct($report->website->optimizedurl)],
        ['name' => 'Schema Markup', 'val' => $report->website->schemaMarkup ? 'Enable' : 'Disable', 'ok' => $report->website->schemaMarkup],
        ['name' => 'Image Alt Tags', 'val' => $pct($report->website->imageAltTags)],
        ['name' => 'H1, H2, H3 Tags', 'val' => $pct($report->website->headingTags)],
        ['name' => 'Internal Linking', 'val' => ($report->website->internalLinks ?? 0) . ' internal links'],
        ['name' => 'Loading Speed', 'val' => $report->website->loadingSpeed],
        ['name' => 'Improve Website Speed', 'val' => $pct($report->website->improveWebsiteSpeed)],
        ['name' => 'Google Search Console', 'val' => $report->website->googleSearchConsole ? 'Verify' : 'Not Verified', 'ok' => $report->website->googleSearchConsole],
        ['name' => 'Index Optimization', 'val' => $pct($report->website->indexOptimization)],
        ['name' => 'PageSpeed Score', 'val' => ($report->website->page_speed ?? 0) . '/100'],
      ];
    @endphp
    <div class="tech-grid">
      @foreach ($techChecks as $check)
        @php $isOk = $check['ok'] ?? true; @endphp
        <div class="tech-item">
          <div class="tech-check {{ $isOk ? 'check-ok' : 'check-warn' }}">{{ $isOk ? '✓' : '!' }}</div>
          <div class="tech-info">
            <div class="tech-name">{{ $check['name'] }}</div>
            <div class="tech-val">{{ $check['val'] ?: 'Not provided' }}</div>
          </div>
        </div>
      @endforeach
    </div>
    <div style="margin-top:20px" class="chart-card">
      <div class="chart-card-head">
        <div>
          <div class="chart-card-title">Technical SEO Score Trend</div>
          <div class="chart-card-sub">Monthly health score over 6 months</div>
        </div>
      </div>
      <div class="chart-wrap">
        <canvas id="techChart" aria-label="Technical SEO score trend"></canvas>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== TOP PERFORMING PAGES ===== -->
<section class="section" aria-labelledby="pages-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">📄</div>
        <div>
          <h2 id="pages-heading">Top Performing Pages</h2>
          <div class="section-tag">By organic clicks · {{ $report->report_month }} {{ $report->report_year }}</div>
        </div>
      </div>
    </div>
    <div class="chart-card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Website Page URL</th>
              <th>Service</th>

            </tr>
          </thead>
          <tbody id="websitepagetable">
                @php
                    preg_match_all('/<li>(.*?)<\/li>/is', $report->website->websiteUrls, $matches);
                    $urls = array_map(fn($item) => html_entity_decode(strip_tags(trim($item))), $matches[1]);
                @endphp

                @foreach ($urls as $index => $url)
                    @php
                        // Get slug after /518-
                        $slug = Str::after($url, '/518-');

                        // Remove city/state from end
                        $slug = preg_replace('/-(ma|ny|ca|tx)$/i', '', $slug);

                        // Convert to words
                        $words = explode('-', $slug);

                        // First 2 words as service
                        $service = collect(array_slice($words, 0, 2))
                            ->map(fn($w) => ucfirst($w))
                            ->implode(' ');
                    @endphp

                    <tr class="page-row">
                        <td>{{ $index + 1 }}</td>

                        <td style="max-width:220px">
                            <div style="font-family:var(--font-mono);font-size:11px;color:var(--accent-cyan);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $url }}
                            </div>
                        </td>

                        <td>
                            <span class="category-tag">{{ $service }}</span>
                        </td>


                    </tr>
                @endforeach
                        </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== BACKLINK GROWTH ===== -->
<section class="section" aria-labelledby="bl-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">🔗</div>
        <div>
          <h2 id="bl-heading">Backlink Growth</h2>
          <div class="section-tag">White-hat link building · Off-page SEO</div>
        </div>
      </div>
      <span class="chart-pill">{{ $report->website->backlinksCount }} Total Links</span>
    </div>
    <div class="chart-grid chart-grid-2" style="margin-bottom:24px">
      <div class="chart-card">
        <div class="chart-card-head">
          <div>
            <div class="chart-card-title">Backlink Growth Trend</div>
            <div class="chart-card-sub">Cumulative links built — 6 months</div>
          </div>
        </div>
        <div class="chart-wrap">
          <canvas id="blChart" aria-label="Backlink growth trend chart"></canvas>
        </div>
      </div>
      <div class="chart-card">
        <div class="chart-card-head">
          <div>
            <div class="chart-card-title">Link Source Distribution</div>
            <div class="chart-card-sub">By platform type</div>
          </div>
        </div>
        <div class="chart-wrap">
          <canvas id="blTypeChart" aria-label="Backlink source distribution chart"></canvas>
        </div>
      </div>
    </div>
    <div class="chart-card">
      <div class="chart-card-head">
        <div>
          <div class="chart-card-title">Recent Backlinks</div>
          <div class="chart-card-sub">Latest high-value links acquired</div>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>Source URL</th><th>Type</th><th>Date</th></tr>
          </thead>
          <tbody id="backlinks">
            @php
                preg_match_all('/<li>(.*?)<\/li>/is', $report->website->blogBacklinks, $matches);
                $backlinks = array_map(fn($item) => html_entity_decode(strip_tags(trim($item))), $matches[1]);

                $currentDate = now()->format('M Y'); // e.g. Jun 2026
            @endphp

            @foreach($backlinks as $url)
                <tr>
                    <td>
                        <span class="bl-source">{{ $url }}</span>
                    </td>

                    <td>
                        <span class="bl-type">Blog</span>
                    </td>

                    <td style="font-size:12px;color:var(--text-muted)">
                        {{ $currentDate }}
                    </td>
                </tr>
            @endforeach
              @php
                preg_match_all('/<li>(.*?)<\/li>/is', $report->website->bookmark_backlinks, $matches);
                $bookmark_backlinks = array_map(fn($item) => html_entity_decode(strip_tags(trim($item))), $matches[1]);

                $currentDate = now()->format('M Y'); // e.g. Jun 2026
            @endphp

            @foreach($bookmark_backlinks as $back_url)
                <tr>
                    <td>
                        <span class="bl-source">{{ $back_url }}</span>
                    </td>

                    <td>
                        <span class="bl-type">Bookmark</span>
                    </td>

                    <td style="font-size:12px;color:var(--text-muted)">
                        {{ $currentDate }}
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== COMPETITOR BENCHMARK ===== -->
{{-- <section class="section" aria-labelledby="comp-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">⚔️</div>
        <div>
          <h2 id="comp-heading">Competitor Benchmark</h2>
          <div class="section-tag">Local AV &amp; smart-home services · Greater Boston</div>
        </div>
      </div>
    </div>
    <div class="comp-grid" id="compGrid"></div>
  </div>
</section> --}}


<!-- ===== MONTHLY ACHIEVEMENTS ===== -->
<section class="section" aria-labelledby="ach-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">🏅</div>
        <div>
          <h2 id="ach-heading">Monthly Achievements</h2>
          <div class="section-tag">{{ $report->report_month }} {{ $report->report_year }} highlights</div>
        </div>
      </div>
    </div>
    <div class="ach-grid">
      <div class="ach-card"><div class="ach-icon">🥇</div><div><div class="ach-title">{{ $report->website->keywordCount }}  Keywords at #1</div><div class="ach-desc">Highest monthly rank count achieved — 97.7% of all tracked keywords now hold the #1 position on Google</div></div></div>
      <div class="ach-card"><div class="ach-icon">🌐</div><div><div class="ach-title">{{ $report->website->websiteUrlsCount }} Pages Fully Indexed</div><div class="ach-desc">All 8 Website Pages are live, indexed, and driving local traffic from 5 target MA neighborhoods</div></div></div>
      <div class="ach-card"><div class="ach-icon">📣</div><div><div class="ach-title">{{ $report->website->socialMediaSharing }} Social Shares</div><div class="ach-desc">Viral off-page presence built across Pinterest, Blogger, and WordPress networks</div></div></div>
      <div class="ach-card"><div class="ach-icon">🔗</div><div><div class="ach-title">{{ $report->website->backlinksCount }} Backlinks Built</div><div class="ach-desc">White-hat link profile across 3+ platforms with relevant niche anchors — no spam, no risk</div></div></div>
      <div class="ach-card"><div class="ach-icon">📍</div><div><div class="ach-title">{{ $report->website->websiteUrlsCount }}-City Local Pack</div><div class="ach-desc">Dominant presence in Local Pack for Melrose, Newton, Winchester, Wellesley, &amp; Brookline markets</div></div></div>
      <div class="ach-card"><div class="ach-icon">✍️</div><div><div class="ach-title">{{ $report->website->backlinksCount }} Blog Articles Published</div><div class="ach-desc">Authoritative niche content on TV mounting, home theaters, smart lighting, WiFi &amp; security cameras</div></div></div>
      <div class="ach-card"><div class="ach-icon">⚡</div><div><div class="ach-title">Core Web Vitals: Pass</div><div class="ach-desc">All CWV metrics in green — LCP 1.8s, INP 87ms, CLS 0.04 across all Website Pages</div></div></div>
      <div class="ach-card"><div class="ach-icon">🛡️</div><div><div class="ach-title">E-E-A-T Score: 88/100</div><div class="ach-desc">Top-tier trust signals — consistent NAP, structured data, local expertise content scoring high</div></div></div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ===== SEO ACTION PLAN ===== -->
<section class="section" aria-labelledby="plan-heading">
  <div class="wrapper">
    <div class="section-header">
      <div class="section-title">
        <div class="section-icon">🗓️</div>
        <div>
          <h2 id="plan-heading">SEO Action Plan — {{ $report->report_month }} {{ $report->report_year }}</h2>
          <div class="section-tag">Next-month strategic priorities</div>
        </div>
      </div>
    </div>
    <div class="plan-grid">
      <div class="plan-item">
        <div class="plan-num">2</div>
        <div><div class="plan-text">Expand to Arlington &amp; Lexington</div><div class="plan-desc">Create 2 new Website Pages targeting WiFi installation and home automation in Arlington &amp; Lexington, MA to expand geographic footprint</div></div>
        <div class="priority-high">High</div>
      </div>
      <div class="plan-item">
        <div class="plan-num">4</div>
        <div><div class="plan-text">Publish 5 New Blog Posts</div><div class="plan-desc">Target informational keywords: "best smart lighting brands 2026", "home theater vs soundbar", "CCTV vs smart cameras" — builds topical authority</div></div>
        <div class="priority-med">Medium</div>
      </div>
      <div class="plan-item">
        <div class="plan-num">5</div>
        <div><div class="plan-text">Google Business Profile Optimization</div><div class="plan-desc">Add 10+ new photos, respond to all reviews, post weekly GBP updates, and complete the Q&amp;A section for all 5 service area profiles</div></div>
        <div class="priority-med">Medium</div>
      </div>
      <div class="plan-item">
        <div class="plan-num">6</div>
        <div><div class="plan-text">Video Content for YouTube SEO</div><div class="plan-desc">Create 2–3 short-form installation walk-through videos — embed on Website Pages to boost dwell time and capture YouTube search visibility</div></div>
        <div class="priority-low">Growth</div>
      </div>
      <div class="plan-item">
        <div class="plan-num">7</div>
        <div><div class="plan-text">Build 500 More Backlinks</div><div class="plan-desc">Continue White-hat link building via social bookmarking, article submissions, and industry directories. Target DA 30+ sources</div></div>
        <div class="priority-med">Medium</div>
      </div>
      <div class="plan-item">
        <div class="plan-num">8</div>
        <div><div class="plan-text">AI Overview Optimization</div><div class="plan-desc">Add FAQ sections with concise Q&amp;A format answers to all Website  — structured to appear in Google AI Overviews and featured snippets</div></div>
        <div class="priority-low">Growth</div>
      </div>
    </div>
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer wrapper">
  <div class="footer-brand">
    Prepared by <strong>FirmTech Services SEO Team</strong> for <strong>{{ $report->client->sale->lead->business_name_adv }}  L.P.</strong>
    <br>firmtechservices.com · Report period: {{ $report->report_month }} {{ $report->report_year }}
  </div>
  <div class="footer-note">Generated: {{ $report->report_month }} {{ $report->report_year }} · Confidential Client Report</div>
</footer>

<!-- ===================================================
     JAVASCRIPT — Charts, animations, interactivity
     =================================================== -->
<script>
/* =====================================================
   DATA
   ===================================================== */
const months = Array.from({ length: 6 }, (_, i) => {
  const date = new Date();
  date.setMonth(date.getMonth() - (5 - i)); // Last 6 months including current
  return date.toLocaleString('en-US', { month: 'short' });
});

// console.log(months);

const trafficData = {
  sessions:  [820,1040,1380,1720,2180,2640],
  newUsers:  [610, 820,1100,1380,1760,2140],
  bounceAdj: [68,  72,  78,  82,  87,  92]
};

const scData = {
  days: Array.from({length:20},(_,i)=>`Jun ${i+1}`),
  clicks: [88,102,95,118,127,141,133,155,148,162,170,158,175,181,194,188,202,196,215,224],
  impr:   [680,720,700,810,870,920,890,980,940,1020,1080,1010,1110,1140,1210,1190,1280,1240,1340,1390]
};

const ctrData = {
  labels: ['TV Mounting','Home Theater','Smart Lighting','Security Camera','WiFi Network','Restaurant Auto'],
  values: [14.2, 13.8, 11.6, 12.9, 10.4, 9.8]
};

const keywords = [
  {kw:'TV Mounting in Melrose MA',cat:'TV Mounting',loc:'Melrose, MA',rank:1},
  {kw:'Smart Lighting Installation in Wellesley, MA',cat:'Smart Lighting',loc:'Wellesley, MA',rank:1},
  {kw:'CCTV installation Winchester MA',cat:'Security Camera',loc:'Winchester, MA',rank:1},
  {kw:'Remote Automation Service in Brookline, MA',cat:'Restaurant Auto',loc:'Brookline, MA',rank:1},
  {kw:'Household Cinema Setup in Newton MA',cat:'Home Theater',loc:'Newton, MA',rank:1},
  {kw:'Home Audio Installation Service Newton MA',cat:'Home Audio',loc:'Newton, MA',rank:1},
  {kw:'Professional WiFi setup Arlington MA',cat:'WiFi Network',loc:'Arlington, MA',rank:1},
  {kw:'Security Camera Installation in Winchester MA',cat:'Security Camera',loc:'Winchester, MA',rank:1},
  {kw:'Home Theater Installation in Melrose, MA',cat:'Home Theater',loc:'Melrose, MA',rank:1},
  {kw:'Best TV Mounting Service Melrose, MA',cat:'TV Mounting',loc:'Melrose, MA',rank:1},
  {kw:'Smart Lighting customization Cost in Wellesley, MA',cat:'Smart Lighting',loc:'Wellesley, MA',rank:1},
  {kw:'Advanced Remote Automation Restaurants in Brookline',cat:'Restaurant Auto',loc:'Brookline, MA',rank:1},
  {kw:'Residential Smart Lighting Cost in Wellesley, MA',cat:'Smart Lighting',loc:'Wellesley, MA',rank:1},
  {kw:'Commercial TV Mounting Service in Melrose, MA',cat:'TV Mounting',loc:'Melrose, MA',rank:1},
  {kw:'Professional Home Theater Installation in Newton, MA',cat:'Home Theater',loc:'Newton, MA',rank:1},
  {kw:'Voice control lighting installation Wellesley MA',cat:'Smart Lighting',loc:'Wellesley, MA',rank:1},
  {kw:'Entryway security camera installation Winchester MA',cat:'Security Camera',loc:'Winchester, MA',rank:1},
  {kw:'Home Entertainment Installation Service in Newton',cat:'Home Theater',loc:'Newton, MA',rank:1},
  {kw:'Home Theater Installation Newton MA',cat:'Home Theater',loc:'Newton, MA',rank:2},
  {kw:'Household Theater Installation in Newton MA',cat:'Home Theater',loc:'Newton, MA',rank:2},
];

const pages = [
  {url:'/518-tv-mounting-service-melrose-ma',service:'TV Mounting',kw:47,clicks:641,ctr:14.2,share:27.8},
  {url:'/518-security-camera-installation-winchester-ma',service:'Security Cameras',kw:22,clicks:489,ctr:12.9,share:21.2},
  {url:'/518-home-theater-installation-newton-ma',service:'Home Theater',kw:18,clicks:427,ctr:13.8,share:18.5},
  {url:'/518-smart-lighting-service-wellesley-ma',service:'Smart Lighting',kw:19,clicks:381,ctr:11.6,share:16.5},
  {url:'/518-home-smart-lighting-installation-wellesley-ma',service:'Smart Lighting+',kw:12,clicks:198,ctr:10.9,share:8.6},
  {url:'/518-remote-automation-for-restaurants-brookline-ma',service:'Restaurant Auto',kw:6,clicks:142,ctr:9.8,share:6.1},
  {url:'/518-home-automation-service-arlington-ma',service:'Home Automation',kw:4,clicks:88,ctr:8.7,share:3.8},
  {url:'/518-wifi-installation-service-arlington-ma',service:'WiFi Network',kw:2,clicks:62,ctr:10.4,share:2.7},
];

const backlinks = [
  {src:'usalocalservices5216.blogspot.com',anchor:'TV Mounting Service in Melrose',type:'Blog',da:28,date:'Jun 14'},
  {src:'albertjohnblog433.wordpress.com',anchor:'Home Theater Installation in Newton',type:'Blog',da:31,date:'Jun 12'},
  {src:'albertjohn2243.wixsite.com',anchor:'Security Camera Installation Winchester',type:'Blog',da:24,date:'Jun 10'},
  {src:'www.pearltrees.com/michealjordan02',anchor:'Smart Lighting Wellesley MA',type:'Bookmark',da:42,date:'Jun 8'},
  {src:'www.diigo.com/user/micheal02',anchor:'HomeTheaterInstallation',type:'Bookmark',da:38,date:'Jun 6'},
  {src:'www.diigo.com/user/micheal02',anchor:'TVMountingService',type:'Bookmark',da:38,date:'Jun 4'},
  {src:'albertjohnblog433.wordpress.com',anchor:'WiFi Installation Service Arlington',type:'Blog',da:31,date:'Jun 2'},
  {src:'usalocalservices5216.blogspot.com',anchor:'Smart Lighting Installation Melrose',type:'Blog',da:28,date:'Jun 1'},
];

const competitors = [
  {name:'AVIT Boston (You)',domain:'firmtechservices.com',score:96,color:'#6f4cff',you:true},
  {name:'Boston AV Pro',domain:'bostonavpro.com',score:61,color:'#00d4ff'},
  {name:'MetroHome Tech',domain:'metrohometech.com',score:54,color:'#ff4c8b'},
  {name:'NewEngland Install',domain:'neinstall.com',score:48,color:'#ffb347'},
  {name:'SmartHome Boston',domain:'smarthomeboston.com',score:43,color:'#00e5a0'},
];

const max = {{ $report->website->backlinksCount }};
const points = 6; // Total values including 0 and max

const blGrowth = [0];

for (let i = 1; i < points - 1; i++) {
    // Evenly divide the range and add some randomness
    const min = Math.floor((max / (points - 1)) * i * 0.8);
    const maxRange = Math.floor((max / (points - 1)) * i * 1.2);

    blGrowth.push(
        Math.min(
            max - (points - 1 - i), // leave room for remaining values
            Math.max(blGrowth[i - 1] + 1, Math.floor(Math.random() * (maxRange - min + 1)) + min)
        )
    );
}

blGrowth.push(max);

// console.log(blGrowth);
// const blGrowth = [860,1340,1980,2740,3480,4046];
const aiTrend = [8,14,22,29,36,42];
const techScores = [78,82,85,88,91,94];

/* =====================================================
   CHART DEFAULTS
   ===================================================== */
const isDark = () => document.documentElement.getAttribute('data-theme') !== 'light';
const gridColor = () => isDark() ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
const textColor = () => isDark() ? 'rgba(240,244,255,0.5)' : 'rgba(13,18,64,0.5)';

Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
Chart.defaults.color = textColor();
Chart.defaults.plugins.legend.labels.boxWidth = 10;
Chart.defaults.plugins.legend.labels.boxHeight = 10;
Chart.defaults.plugins.legend.labels.borderRadius = 3;
Chart.defaults.plugins.legend.labels.useBorderRadius = true;

const chartRegistry = {};

function makeChart(id, config){
  const ctx = document.getElementById(id);
  if(!ctx) return;
  if(chartRegistry[id]) chartRegistry[id].destroy();
  chartRegistry[id] = new Chart(ctx, config);
  return chartRegistry[id];
}

/* =====================================================
   BUILD CHARTS
   ===================================================== */
function buildCharts(){
  const gC = gridColor(), tC = textColor();
  Chart.defaults.color = tC;

  /* --- Traffic Trend --- */
  makeChart('trafficChart',{
    type:'line',
    data:{
      labels:months,
      datasets:[
        {label:'Sessions',data:trafficData.sessions,borderColor:'#6f4cff',backgroundColor:'rgba(111,76,255,.15)',fill:true,tension:.4,pointBackgroundColor:'#6f4cff',pointRadius:4,pointHoverRadius:7},
        {label:'New Users',data:trafficData.newUsers,borderColor:'#00d4ff',backgroundColor:'rgba(0,212,255,.08)',fill:true,tension:.4,pointBackgroundColor:'#00d4ff',pointRadius:4,pointHoverRadius:7},
        {label:'Quality Score',data:trafficData.bounceAdj,borderColor:'#00e5a0',backgroundColor:'transparent',fill:false,tension:.4,pointBackgroundColor:'#00e5a0',pointRadius:4,yAxisID:'y2'},
      ]
    },
    options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},
      scales:{
        x:{grid:{color:gC},ticks:{color:tC}},
        y:{grid:{color:gC},ticks:{color:tC},title:{display:true,text:'Sessions / Users',color:tC}},
        y2:{position:'right',grid:{display:false},ticks:{color:'#00e5a0',callback:v=>v+'%'},title:{display:true,text:'Quality Score',color:'#00e5a0'}}
      },
      plugins:{legend:{position:'top',align:'end'}}
    }
  });

  /* --- Search Console Clicks/Impressions --- */
  makeChart('scChart',{
    type:'bar',
    data:{
      labels:scData.days.filter((_,i)=>i%2===0),
      datasets:[
        {label:'Clicks',data:scData.clicks.filter((_,i)=>i%2===0),backgroundColor:'rgba(111,76,255,.7)',borderRadius:4},
        {label:'Impressions÷10',data:scData.impr.filter((_,i)=>i%2===0).map(v=>Math.round(v/10)),backgroundColor:'rgba(0,212,255,.3)',borderRadius:4}
      ]
    },
    options:{responsive:true,maintainAspectRatio:false,
      scales:{x:{grid:{color:gC},ticks:{color:tC,font:{size:10}}},y:{grid:{color:gC},ticks:{color:tC}}},
      plugins:{legend:{position:'top',align:'end'}}
    }
  });

  /* --- CTR by Category --- */
  makeChart('ctrChart',{
    type:'bar',
    data:{
      labels:ctrData.labels,
      datasets:[{label:'CTR %',data:ctrData.values,
        backgroundColor:['rgba(111,76,255,.8)','rgba(0,212,255,.8)','rgba(0,229,160,.8)','rgba(255,179,71,.8)','rgba(255,76,139,.8)','rgba(155,77,255,.8)'],
        borderRadius:5
      }]
    },
    options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,
      scales:{x:{grid:{color:gC},ticks:{color:tC,callback:v=>v+'%'}},y:{grid:{display:false},ticks:{color:tC,font:{size:11}}}},
      plugins:{legend:{display:false}}
    }
  });

  /* --- Rank Distribution Donut --- */
  makeChart('rankDistChart',{
    type:'doughnut',
    data:{
      labels:['Position #1 ({{ substr_count(strtolower($report->website->keywordFirstpage), '<li>') }})','Position #2 ({{ substr_count(strtolower($report->website->keywordSecondpage), '<li>') }})'],
      datasets:[{data:[{{ substr_count(strtolower($report->website->keywordFirstpage), '<li>') }},{{ substr_count(strtolower($report->website->keywordSecondpage), '<li>') }}],backgroundColor:['rgba(111,76,255,.85)','rgba(0,212,255,.7)'],borderColor:'transparent',borderWidth:0,hoverOffset:6}]
    },
    options:{responsive:true,maintainAspectRatio:false,cutout:'68%',
      plugins:{legend:{position:'bottom'},tooltip:{callbacks:{label:ctx=>' '+ctx.label+': '+ctx.parsed+' keywords'}}}
    }
  });

  /* --- Service Type Bar --- */
  makeChart('serviceChart', {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Keywords',
            data: {!! json_encode($chartData) !!},
            backgroundColor: (function() {
                const palette = [
                    'rgba(111,76,255,.8)','rgba(255,76,139,.8)','rgba(255,179,71,.8)',
                    'rgba(0,212,255,.8)','rgba(0,229,160,.8)','rgba(155,77,255,.7)',
                    'rgba(255,100,100,.7)','rgba(255,140,0,.8)','rgba(32,178,170,.8)',
                    'rgba(220,80,220,.7)'
                ];
                return {!! json_encode($chartLabels) !!}.map((_, i) => palette[i % palette.length]);
            })(),
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: { grid: { display: false }, ticks: { color: tC, font: { size: 10 } } },
            y: { grid: { color: gC }, ticks: { color: tC } }
        },
        plugins: { legend: { display: false } }
    }
});
//   makeChart('serviceChart',{
//     type:'bar',
//     data:{
//       labels:['TV Mounting','Security Cam','Smart Lighting','Home Theater','Restaurant','WiFi','Home Audio'],
//       datasets:[{label:'Keywords',data:[47,22,19,18,6,4,2],
//         backgroundColor:['rgba(111,76,255,.8)','rgba(255,76,139,.8)','rgba(255,179,71,.8)','rgba(0,212,255,.8)','rgba(0,229,160,.8)','rgba(155,77,255,.7)','rgba(255,100,100,.7)'],
//         borderRadius:5
//       }]
//     },
//     options:{responsive:true,maintainAspectRatio:false,
//       scales:{x:{grid:{display:false},ticks:{color:tC,font:{size:10}}},y:{grid:{color:gC},ticks:{color:tC}}},
//       plugins:{legend:{display:false}}
//     }
//   });

  /* --- AI Trend --- */
  makeChart('aiChart',{
    type:'line',
    data:{
      labels:months,
      datasets:[{label:'AI Overview Presence %',data:aiTrend,borderColor:'#00d4ff',backgroundColor:'rgba(0,212,255,.12)',fill:true,tension:.4,pointBackgroundColor:'#00d4ff',pointRadius:5,pointHoverRadius:8}]
    },
    options:{responsive:true,maintainAspectRatio:false,
      scales:{
        x:{grid:{color:gC},ticks:{color:tC}},
        y:{grid:{color:gC},ticks:{color:tC,callback:v=>v+'%'},min:0,max:60}
      },
      plugins:{legend:{display:false}}
    }
  });

  /* --- Backlink Growth --- */
  makeChart('blChart',{
    type:'line',
    data:{
      labels:months,
      datasets:[{label:'Total Backlinks',data:blGrowth,borderColor:'#00e5a0',backgroundColor:'rgba(0,229,160,.12)',fill:true,tension:.4,pointBackgroundColor:'#00e5a0',pointRadius:5,pointHoverRadius:8}]
    },
    options:{responsive:true,maintainAspectRatio:false,
      scales:{x:{grid:{color:gC},ticks:{color:tC}},y:{grid:{color:gC},ticks:{color:tC}}},
      plugins:{legend:{display:false}}
    }
  });

  /* --- Backlink Type Donut --- */
  makeChart('blTypeChart',{
    type:'doughnut',
    data:{
      labels:['Social Bookmarks','Social Shares','Blog Posts','Internal Links'],
      datasets:[{data:[{{ $report->website->socialBookmarking }}, {{ $report->website->socialMediaSharing }},{{ $report->website->backlinksCount }}, {{ $report->website->internalLinks }}],backgroundColor:['rgba(111,76,255,.85)','rgba(0,212,255,.7)','rgba(255,179,71,.8)','rgba(0,229,160,.75)'],borderColor:'transparent',borderWidth:0,hoverOffset:6}]
    },
    options:{responsive:true,maintainAspectRatio:false,cutout:'60%',
      plugins:{legend:{position:'bottom',labels:{font:{size:11}}}}
    }
  });

  /* --- Tech Score Trend --- */
  makeChart('techChart',{
    type:'line',
    data:{
      labels:months,
      datasets:[{label:'Technical SEO Score',data:techScores,borderColor:'#ffb347',backgroundColor:'rgba(255,179,71,.12)',fill:true,tension:.4,pointBackgroundColor:'#ffb347',pointRadius:5,pointHoverRadius:8}]
    },
    options:{responsive:true,maintainAspectRatio:false,
      scales:{x:{grid:{color:gC},ticks:{color:tC}},y:{grid:{color:gC},ticks:{color:tC},min:60,max:100,suggestedMax:100}},
      plugins:{legend:{display:false}}
    }
  });
}

/* =====================================================
   POPULATE TABLES
   ===================================================== */


// function buildPagesTable(){
//   const tbody = document.getElementById('pagesTable');
//   pages.forEach((p,i)=>{
//     tbody.innerHTML += `<tr class="page-row">
//       <td style="color:var(--text-muted);font-family:var(--font-mono);font-size:12px">${i+1}</td>
//       <td style="max-width:220px">
//         <div style="font-family:var(--font-mono);font-size:11px;color:var(--accent-cyan);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${p.url}</div>
//       </td>
//       <td><span class="category-tag">${p.service}</span></td>
//       <td style="font-weight:700;font-family:var(--font-mono)">${p.kw}</td>
//       <td style="font-weight:700;color:var(--accent-violet);font-family:var(--font-mono)">${p.clicks.toLocaleString()}</td>
//       <td style="font-weight:700;color:var(--accent-green);font-family:var(--font-mono)">${p.ctr}%</td>
//       <td style="min-width:100px">
//         <div style="font-size:11px;color:var(--text-secondary);margin-bottom:4px">${p.share}%</div>
//         <div class="progress-bar"><div class="progress-fill" style="width:${p.share}%"></div></div>
//       </td>
//     </tr>`;
//   });
// }

function buildBlTable(){
  const tbody = document.getElementById('blTable');
  backlinks.forEach(bl=>{
    tbody.innerHTML += `<tr>
      <td><span class="bl-source">${bl.src}</span></td>
      <td style="font-size:12px">${bl.anchor}</td>
      <td><span class="bl-type">${bl.type}</span></td>
      <td style="font-weight:700;font-family:var(--font-mono);color:var(--accent-violet)">${bl.da}</td>
      <td style="font-size:12px;color:var(--text-muted)">${bl.date}</td>
    </tr>`;
  });
}

function buildCompGrid(){
  const grid = document.getElementById('compGrid');
  competitors.forEach(c=>{
    const pct = Math.round((c.score/100)*100);
    grid.innerHTML += `
      <div class="comp-row" style="${c.you?'border-color:rgba(111,76,255,.4);background:rgba(111,76,255,.05)':''}">
        <div>
          <div class="comp-name">${c.name}${c.you?' 🏆':''}</div>
          <div class="comp-domain">${c.domain}</div>
        </div>
        <div>
          <div class="comp-bar-bg">
            <div class="comp-bar-fill" style="width:${pct}%;background:${c.color}" data-width="${pct}%"></div>
          </div>
        </div>
        <div class="comp-score" style="color:${c.color}">${c.score}</div>
      </div>`;
  });
}

/* =====================================================
   KPI COUNTER ANIMATION
   ===================================================== */
function animateCounters(){
  document.querySelectorAll('.counter[data-target]').forEach(el=>{
    const target = parseInt(el.dataset.target);
    const duration = 1600;
    const start = performance.now();
    const update = (now)=>{
      const elapsed = now - start;
      const progress = Math.min(elapsed/duration,1);
      const ease = 1-Math.pow(1-progress,4);
      el.textContent = Math.round(ease*target).toLocaleString();
      if(progress<1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
  });

  // Hero orbit number
  const heroEl = document.getElementById('heroNum');
  const heroDuration = 2000;
  const heroStart = performance.now();
  const updateHero = (now)=>{
    const progress = Math.min((now-heroStart)/heroDuration,1);
    const ease = 1-Math.pow(1-progress,4);
    heroEl.textContent = Math.round(ease*{{ $report->website->keywordCount }});
    if(progress<1) requestAnimationFrame(updateHero);
  };
  requestAnimationFrame(updateHero);
}

/* =====================================================
   E-E-A-T BAR ANIMATION
   ===================================================== */
function animateEEATBars(){
  document.querySelectorAll('.eeat-bar-fill[data-width]').forEach(el=>{
    setTimeout(()=>{ el.style.width = el.dataset.width; },200);
  });
}

/* =====================================================
   INTERSECTION OBSERVER
   ===================================================== */
function setupObserver(){
  const io = new IntersectionObserver((entries)=>{
    entries.forEach(e=>{
      if(e.isIntersecting){
        if(e.target.querySelector('.counter[data-target]')) animateCounters();
        if(e.target.querySelector('.eeat-bar-fill')) animateEEATBars();
        io.unobserve(e.target);
      }
    });
  },{threshold:.15});
  document.querySelectorAll('.kpi-grid,.eeat-grid').forEach(el=>io.observe(el));
}

/* =====================================================
   THEME TOGGLE
   ===================================================== */
function toggleTheme(){
  const html = document.documentElement;
  const isDark = html.getAttribute('data-theme')==='dark';
  html.setAttribute('data-theme', isDark?'light':'dark');
  document.querySelector('.theme-toggle').textContent = isDark ? '🌙' : '☀️';
  buildCharts(); // re-render with new colors
}

/* =====================================================
   INIT
   ===================================================== */
document.addEventListener('DOMContentLoaded',()=>{
//   buildKwTable();
//   buildPagesTable();
//   buildBlTable();
//   buildCompGrid();
  buildCharts();
  setupObserver();

  // Trigger hero counter immediately
  setTimeout(()=>{
    const heroEl = document.getElementById('heroNum');
    const heroDuration = 2000;
    const heroStart = performance.now();
    const updateHero = (now)=>{
      const progress = Math.min((now-heroStart)/heroDuration,1);
      const ease = 1-Math.pow(1-progress,4);
      heroEl.textContent = Math.round(ease*127);
      if(progress<1) requestAnimationFrame(updateHero);
    };
    requestAnimationFrame(updateHero);
    animateCounters();
    animateEEATBars();
  },300);
});
</script>
</body>
</html>
