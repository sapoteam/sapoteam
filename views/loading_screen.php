<?php
/**
 * Oemah Keboen - Global Loading Screen v5
 * Card = full tinggi area konten (splash screen style)
 *
 * User  : <body> → <?php include '../loading_screen.php'; ?> → <div id="app">
 * Admin : <body> → <?php include '../../views/loading_screen.php'; ?> → <div id="app">
 */
?>

<div id="ok-page-loader">
  <div id="ok-loader-box">

    <svg width="0" height="0" style="position:absolute">
      <defs>
        <style>
          @keyframes ok-sway  { 0%,100%{transform:rotate(-6deg)} 50%{transform:rotate(6deg)} }
          @keyframes ok-sway2 { 0%,100%{transform:rotate(5deg)}  50%{transform:rotate(-5deg)} }
          @keyframes ok-fall  {
            0%  {transform:translateY(-20px) rotate(0deg)    translateX(0);    opacity:1}
            100%{transform:translateY(110vh) rotate(400deg)  translateX(30px); opacity:0}
          }
          @keyframes ok-fall2 {
            0%  {transform:translateY(-20px) rotate(20deg)   translateX(0);    opacity:1}
            100%{transform:translateY(105vh) rotate(-340deg) translateX(-25px);opacity:0}
          }
          @keyframes ok-fall3 {
            0%  {transform:translateY(-15px) rotate(-8deg)   translateX(0);    opacity:1}
            100%{transform:translateY(115vh) rotate(460deg)  translateX(15px); opacity:0}
          }
          @keyframes ok-pulse {
            0%,100%{transform:scale(1)}
            50%    {transform:scale(1.06)}
          }
          @keyframes ok-dot {
            0%,80%,100%{transform:scale(0.55);opacity:0.25}
            40%        {transform:scale(1);   opacity:1}
          }
          @keyframes ok-bird {
            0%   {transform:translateX(-80px) translateY(0px)}
            25%  {transform:translateX(25vw)  translateY(-30px)}
            50%  {transform:translateX(50vw)  translateY(-10px)}
            75%  {transform:translateX(75vw)  translateY(-40px)}
            100% {transform:translateX(110vw) translateY(-20px)}
          }
          @keyframes ok-wing {
            0%,100%{transform:scaleY(1)}
            50%    {transform:scaleY(-0.5)}
          }
          @keyframes ok-cloud {
            0%  {transform:translateX(0)}
            100%{transform:translateX(60px)}
          }
        </style>
      </defs>
    </svg>

    <div class="ok-sky"></div>

    <div class="ok-cloud" style="top:8%;left:12%;animation:ok-cloud 8s ease-in-out infinite alternate;">
      <svg width="90" height="36" viewBox="0 0 90 36"><ellipse cx="45" cy="24" rx="40" ry="14" fill="rgba(255,255,255,0.55)"/><ellipse cx="30" cy="20" rx="22" ry="16" fill="rgba(255,255,255,0.55)"/><ellipse cx="60" cy="18" rx="18" ry="14" fill="rgba(255,255,255,0.55)"/></svg>
    </div>
    <div class="ok-cloud" style="top:14%;right:10%;animation:ok-cloud 11s ease-in-out infinite alternate-reverse;">
      <svg width="70" height="28" viewBox="0 0 70 28"><ellipse cx="35" cy="18" rx="30" ry="11" fill="rgba(255,255,255,0.4)"/><ellipse cx="22" cy="15" rx="16" ry="12" fill="rgba(255,255,255,0.4)"/><ellipse cx="50" cy="13" rx="14" ry="11" fill="rgba(255,255,255,0.4)"/></svg>
    </div>

    <div class="ok-bird" style="top:18%;animation:ok-bird 9s linear infinite 2s;">
      <svg width="28" height="14" viewBox="0 0 28 14">
        <path class="ok-wing-l" d="M14 7 C10 2 4 0 0 3" stroke="#5a8c35" stroke-width="1.5" fill="none" stroke-linecap="round" style="transform-origin:14px 7px;animation:ok-wing 0.5s ease-in-out infinite"/>
        <path class="ok-wing-r" d="M14 7 C18 2 24 0 28 3" stroke="#5a8c35" stroke-width="1.5" fill="none" stroke-linecap="round" style="transform-origin:14px 7px;animation:ok-wing 0.5s ease-in-out infinite 0.25s"/>
      </svg>
    </div>

    <div class="ok-leaf" style="left:6%;  animation:ok-fall  4s   ease-in infinite 0s">
      <svg width="18" height="22" viewBox="0 0 20 24"><path d="M10 2C10 2 2 8 2 15c0 4.4 3.6 7 8 7s8-2.6 8-7C18 8 10 2 10 2z" fill="#4a7c25" opacity=".75"/><line x1="10" y1="8" x2="10" y2="21" stroke="#2d5016" stroke-width="1.2"/></svg>
    </div>
    <div class="ok-leaf" style="left:22%; animation:ok-fall2 5s   ease-in infinite 0.6s">
      <svg width="14" height="18" viewBox="0 0 20 24"><path d="M10 2C10 2 2 8 2 15c0 4.4 3.6 7 8 7s8-2.6 8-7C18 8 10 2 10 2z" fill="#5a8c35" opacity=".6"/><line x1="10" y1="8" x2="10" y2="21" stroke="#2d5016" stroke-width="1.2"/></svg>
    </div>
    <div class="ok-leaf" style="left:42%; animation:ok-fall3 4.5s ease-in infinite 1.4s">
      <svg width="12" height="16" viewBox="0 0 20 24"><path d="M10 2C10 2 2 8 2 15c0 4.4 3.6 7 8 7s8-2.6 8-7C18 8 10 2 10 2z" fill="#3d6b1e" opacity=".55"/><line x1="10" y1="8" x2="10" y2="21" stroke="#2d5016" stroke-width="1.2"/></svg>
    </div>
    <div class="ok-leaf" style="left:65%; animation:ok-fall  5.5s ease-in infinite 0.3s">
      <svg width="16" height="20" viewBox="0 0 20 24"><path d="M10 2C10 2 2 8 2 15c0 4.4 3.6 7 8 7s8-2.6 8-7C18 8 10 2 10 2z" fill="#4a7c25" opacity=".65"/><line x1="10" y1="8" x2="10" y2="21" stroke="#2d5016" stroke-width="1.2"/></svg>
    </div>
    <div class="ok-leaf" style="left:82%; animation:ok-fall2 4.2s ease-in infinite 2.1s">
      <svg width="13" height="17" viewBox="0 0 20 24"><path d="M10 2C10 2 2 8 2 15c0 4.4 3.6 7 8 7s8-2.6 8-7C18 8 10 2 10 2z" fill="#5a8c35" opacity=".7"/><line x1="10" y1="8" x2="10" y2="21" stroke="#2d5016" stroke-width="1.2"/></svg>
    </div>
    <div class="ok-leaf" style="left:55%; animation:ok-fall3 4.8s ease-in infinite 3.5s">
      <svg width="11" height="14" viewBox="0 0 20 24"><path d="M10 2C10 2 2 8 2 15c0 4.4 3.6 7 8 7s8-2.6 8-7C18 8 10 2 10 2z" fill="#3d6b1e" opacity=".5"/><line x1="10" y1="8" x2="10" y2="21" stroke="#2d5016" stroke-width="1.2"/></svg>
    </div>

    <div class="ok-tree" style="left:1%;   bottom:90px; animation:ok-sway  3.2s ease-in-out infinite;      transform-origin:bottom center">
      <svg width="55" height="110" viewBox="0 0 55 120"><rect x="24" y="65" width="7" height="53" fill="#7a5c3a" rx="3"/><path d="M27 65C12 48 9 22 20 10 20 34 27 50 27 65z" fill="#3d6b1e"/><path d="M27 62C42 45 45 19 34 7 34 31 27 47 27 62z" fill="#4a7c25"/><path d="M27 50C14 36 12 14 22 4 22 25 27 38 27 50z" fill="#5a8c35" opacity=".7"/></svg>
    </div>
    <div class="ok-tree" style="right:2%;  bottom:85px; animation:ok-sway2 3.8s ease-in-out infinite;      transform-origin:bottom center">
      <svg width="48" height="96" viewBox="0 0 55 120"><rect x="24" y="65" width="7" height="53" fill="#7a5c3a" rx="3"/><path d="M27 65C12 48 9 22 20 10 20 34 27 50 27 65z" fill="#2d5016"/><path d="M27 62C42 45 45 19 34 7 34 31 27 47 27 62z" fill="#3d6b1e"/></svg>
    </div>
    <div class="ok-tree" style="left:12%;  bottom:88px; animation:ok-sway  4.2s ease-in-out infinite 0.5s; transform-origin:bottom center">
      <svg width="36" height="72" viewBox="0 0 40 80"><rect x="18" y="44" width="4" height="34" fill="#7a5c3a" rx="2"/><path d="M20 44C9 32 7 14 15 7 15 22 20 32 20 44z" fill="#4a7c25"/><path d="M20 42C31 30 33 12 25 5 25 20 20 30 20 42z" fill="#5a8c35"/></svg>
    </div>
    <div class="ok-tree" style="right:13%; bottom:88px; animation:ok-sway2 3s   ease-in-out infinite 1s;   transform-origin:bottom center">
      <svg width="30" height="60" viewBox="0 0 40 80"><rect x="18" y="44" width="4" height="34" fill="#7a5c3a" rx="2"/><path d="M20 44C9 32 7 14 15 7 15 22 20 32 20 44z" fill="#3d6b1e"/><path d="M20 42C31 30 33 12 25 5 25 20 20 30 20 42z" fill="#4a7c25"/></svg>
    </div>
    <div class="ok-tree" style="left:26%;  bottom:86px; animation:ok-sway  5s   ease-in-out infinite 0.3s; transform-origin:bottom center">
      <svg width="22" height="46" viewBox="0 0 40 80"><rect x="18" y="48" width="4" height="30" fill="#7a5c3a" rx="2"/><path d="M20 48C11 38 10 22 17 14 17 28 20 38 20 48z" fill="#5a8c35"/></svg>
    </div>
    <div class="ok-tree" style="right:25%; bottom:86px; animation:ok-sway2 4.5s ease-in-out infinite 1.5s; transform-origin:bottom center">
      <svg width="20" height="42" viewBox="0 0 40 80"><rect x="18" y="48" width="4" height="30" fill="#7a5c3a" rx="2"/><path d="M20 48C29 38 30 22 23 14 23 28 20 38 20 48z" fill="#4a7c25"/></svg>
    </div>

    <div class="ok-ground-far"></div>
    <div class="ok-ground-mid"></div>
    <div class="ok-ground"></div>

    <div class="ok-center">
      <div style="animation:ok-pulse 2s ease-in-out infinite">
        <svg width="80" height="80" viewBox="0 0 80 80">
          <rect x="4" y="4" width="72" height="72" rx="18" fill="#2d5016"/>
          <path d="M40 14C40 14 20 26 20 43c0 10 9 18 20 18s20-8 20-18C60 26 40 14 40 14z" fill="#fff" opacity=".92"/>
          <path d="M40 23C40 23 28 32 28 42c0 6.5 5.4 11 12 11s12-4.5 12-11C52 32 40 23 40 23z" fill="#4a7c25"/>
          <line x1="40" y1="30" x2="40" y2="54" stroke="#fff" stroke-width="1.8" opacity=".8"/>
          <path d="M40 38C36 33 34 27 38 22" stroke="#fff" stroke-width="1.4" fill="none" opacity=".6"/>
          <path d="M40 45C44 40 46 34 42 29" stroke="#fff" stroke-width="1.4" fill="none" opacity=".6"/>
        </svg>
      </div>
      <p class="ok-brand">Oemah Keboen</p>
      <p class="ok-sub">Wisata Alam Samarinda</p>
      <div class="ok-dots">
        <span style="animation:ok-dot 1.4s ease-in-out infinite 0s"></span>
        <span style="animation:ok-dot 1.4s ease-in-out infinite 0.2s"></span>
        <span style="animation:ok-dot 1.4s ease-in-out infinite 0.4s"></span>
      </div>
    </div>

  </div>
</div>

<style>
/* ── Overlay ── */
#ok-page-loader {
  position: fixed;
  top: 72px;
  left: 0; right: 0; bottom: 0;
  z-index: 1034;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  transition: opacity 0.5s ease, visibility 0.5s ease;
  visibility: visible;
  opacity: 1;
  pointer-events: all;
}

#ok-page-loader.ok-admin {
  top: 0;
  left: 260px;
  z-index: 999;
}

@media (max-width: 768px) {
  #ok-page-loader.ok-admin {
    left: 0; top: 0;
  }
}

#ok-page-loader.ok-hiding {
  opacity: 0;
  visibility: hidden;
}

/* ── Card = full size of overlay ── */
#ok-loader-box {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    180deg,
    #c8e6b0 0%,
    #d8eebc 25%,
    #e8f3d5 55%,
    #f0f5e4 75%,
    #f5f0e8 100%
  );
  border-radius: 0;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ── Sky ── */
.ok-sky {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 45%;
  background: linear-gradient(180deg, #d4eef5 0%, #e8f5e4 100%);
  z-index: 0;
}

/* ── Clouds ── */
.ok-cloud {
  position: absolute;
  z-index: 1;
  pointer-events: none;
}

/* ── Bird ── */
.ok-bird {
  position: absolute;
  z-index: 2;
  pointer-events: none;
}

/* ── Leaves ── */
.ok-leaf {
  position: absolute;
  top: -25px;
  z-index: 3;
  pointer-events: none;
}

/* ── Trees ── */
.ok-tree {
  position: absolute;
  z-index: 4;
  pointer-events: none;
}

/* ── Ground layers ── */
.ok-ground-far {
  position: absolute;
  bottom: 60px; left: 0; width: 100%; height: 40px;
  background: #6aaa3a;
  border-radius: 60% 60% 0 0/100% 100% 0 0;
  z-index: 3;
  opacity: .6;
}
.ok-ground-mid {
  position: absolute;
  bottom: 40px; left: 0; width: 100%; height: 35px;
  background: #5a9a2a;
  border-radius: 50% 50% 0 0/100% 100% 0 0;
  z-index: 4;
}
.ok-ground {
  position: absolute;
  bottom: 0; left: 0; width: 100%; height: 55px;
  background: #c8a870;
  z-index: 5;
}
.ok-ground::before {
  content: '';
  position: absolute;
  top: -18px; left: 0;
  width: 100%; height: 26px;
  background: #4a8a22;
  border-radius: 55% 55% 0 0/100% 100% 0 0;
}

/* ── Center content ── */
.ok-center {
  position: relative;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.35);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  padding: 32px 48px;
  border-radius: 24px;
  box-shadow: 0 8px 32px rgba(45,80,22,.12), 0 1px 4px rgba(45,80,22,.08);
  border: 1px solid rgba(255,255,255,0.5);
}

.ok-brand {
  font-family: Georgia, serif;
  font-size: 22px;
  font-weight: 700;
  color: #2d5016;
  margin: 0;
  letter-spacing: .5px;
}
.ok-sub {
  font-size: 11px;
  color: #5a7a3a;
  margin: 0;
  letter-spacing: 2.5px;
  text-transform: uppercase;
}
.ok-dots { display:flex; gap:6px; align-items:center; margin-top:4px; }
.ok-dots span {
  display:inline-block; width:8px; height:8px;
  border-radius:50%; background:#4a7c25;
}

/* ── Hide konten saat loading ── */
body.ok-loading {
  overflow: hidden !important; 
}

body.ok-loading .page-content,
body.ok-loading .content-wrapper {
  opacity: 0 !important;
  pointer-events: none;
}
</style>

<script>
(function () {
  var HIDE_DELAY    = 280;
  var FADE_DURATION = 500;
  var TRIGGER_DELAY = 60;

  document.body.classList.add('ok-loading');

  function applyMode() {
    var loader  = document.getElementById('ok-page-loader');
    if (!loader) return;
    if (document.querySelector('.sidebar')) {
      loader.classList.add('ok-admin');
    }
  }

  function triggerContentAnimation() {
    var page = document.querySelector('.page-content');
    if (page) {
      page.style.opacity = '';
      page.classList.remove('fade-enter');
      void page.offsetWidth;
      page.classList.add('fade-enter');
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          page.classList.remove('fade-enter');
        });
      });
    }
    window.dispatchEvent(new CustomEvent('ok-loader-done'));
  }

  function hideLoader() {
    var loader = document.getElementById('ok-page-loader');
    if (!loader) return;
    applyMode();
    loader.classList.add('ok-hiding');
    setTimeout(function () {
      if (loader) loader.style.display = 'none';
      document.body.classList.remove('ok-loading');
      setTimeout(triggerContentAnimation, TRIGGER_DELAY);
    }, FADE_DURATION + 50);
  }

  if (document.readyState !== 'loading') {
    applyMode();
  } else {
    document.addEventListener('DOMContentLoaded', applyMode);
  }

  if (document.readyState === 'complete') {
    setTimeout(hideLoader, HIDE_DELAY);
  } else {
    window.addEventListener('load', function () {
      setTimeout(hideLoader, HIDE_DELAY);
    });
  }
})();
</script>