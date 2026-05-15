(function () {
  'use strict';

  // Theme
  const html = document.documentElement;
  html.setAttribute('data-theme', localStorage.getItem('mm-theme') || 'dark');
  const themeToggle = document.getElementById('themeToggle');
  if (themeToggle) themeToggle.addEventListener('click', () => {
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('mm-theme', next);
  });

  // Navbar scroll
  const navbar = document.getElementById('navbar');
  if (navbar) window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 50));

  // Mobile nav
  const navToggle = document.getElementById('navToggle');
  const navLinks = document.getElementById('navLinks');
  const navOverlay = document.getElementById('navOverlay');
  const sidebarClose = document.getElementById('sidebarClose');
  function toggleNav() {
    if (!navLinks || !navbar) return;
    const open = !navLinks.classList.contains('open');
    navToggle && navToggle.classList.toggle('active', open);
    navLinks.classList.toggle('open', open);
    navOverlay && navOverlay.classList.toggle('active', open);
    navbar.classList.toggle('nav-open', open);
    document.body.style.overflow = open ? 'hidden' : '';
    navbar.style.zIndex = open ? '10002' : '';
    const cw = document.querySelector('.ai-chat-widget');
    if (cw) cw.style.zIndex = open ? '999' : '';
  }
  if (navToggle) navToggle.addEventListener('click', toggleNav);
  if (navOverlay) navOverlay.addEventListener('click', toggleNav);
  if (sidebarClose) sidebarClose.addEventListener('click', toggleNav);
  if (navLinks) navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => { if (navLinks.classList.contains('open')) toggleNav(); }));

  // Scroll reveal
  const revealObs = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); revealObs.unobserve(e.target); } });
  }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
  document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right').forEach(el => revealObs.observe(el));

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      e.preventDefault();
      const t = document.querySelector(href);
      if (t) window.scrollTo({ top: t.getBoundingClientRect().top + window.scrollY - 80, behavior: 'smooth' });
    });
  });

  // Cursor glow + section shine
  const cursorGlow = document.getElementById('cursorGlow');
  const shines = document.querySelectorAll('.section-shine');
  document.addEventListener('mousemove', (e) => {
    if (cursorGlow) { cursorGlow.style.left = e.clientX + 'px'; cursorGlow.style.top = e.clientY + 'px'; }
    shines.forEach(s => {
      const r = s.parentElement.getBoundingClientRect();
      s.style.setProperty('--orb-x', (e.clientX - r.left) + 'px');
      s.style.setProperty('--orb-y', (e.clientY - r.top) + 'px');
    });
  });

  // Load more
  document.querySelectorAll('.load-more-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const sec = document.getElementById(btn.getAttribute('data-target'));
      if (sec) sec.querySelectorAll('.hidden-item').forEach(i => i.classList.remove('hidden-item'));
      btn.closest('.load-more-container').style.display = 'none';
    });
  });

  // Ember canvas
  const ec_canvas = document.getElementById('emberCanvas');
  if (ec_canvas) {
    const ec = ec_canvas.getContext('2d');
    const resize = () => { ec_canvas.width = window.innerWidth; ec_canvas.height = window.innerHeight; };
    resize(); window.addEventListener('resize', resize);
    const N = window.innerWidth < 768 ? 55 : 110;
    class Spark {
      constructor(i) { this.reset(i < Math.floor(N * 0.9)); }
      reset(instant) {
        this.x = Math.random() * window.innerWidth;
        this.y = instant ? Math.random() * window.innerHeight : window.innerHeight + Math.random() * 40;
        this.vx = (Math.random() - 0.5) * 0.5; this.vy = -(Math.random() * 1.6 + 0.7);
        this.r = Math.random() * 0.9 + 0.3; this.hue = 18 + Math.random() * 34;
        this.life = 120 + Math.floor(Math.random() * 200);
        this.age = instant ? Math.floor(Math.random() * this.life) : 0;
        this.wA = 0.15 + Math.random() * 0.35; this.wF = 0.02 + Math.random() * 0.03;
        this.wO = Math.random() * Math.PI * 2; this.peak = 0.7 + Math.random() * 0.3; this.alpha = 0;
      }
      update() {
        this.age++; this.x += this.vx + Math.sin(this.age * this.wF + this.wO) * this.wA;
        this.y += this.vy; this.vy *= 0.9985;
        const p = this.age / this.life;
        const env = p < 0.1 ? p / 0.1 : p > 0.75 ? (1 - p) / 0.25 : 1;
        this.alpha = env * this.peak * (0.78 + 0.22 * Math.sin(this.age * 0.45 + this.wO * 2.3));
        if (this.age >= this.life || this.y < -30) this.reset(false);
      }
      draw() {
        if (this.alpha < 0.02) return;
        const gr = this.r * 5.5, g = ec.createRadialGradient(this.x, this.y, 0, this.x, this.y, gr);
        g.addColorStop(0, `hsla(${this.hue+20},100%,90%,${this.alpha*.9})`);
        g.addColorStop(0.35, `hsla(${this.hue},100%,65%,${this.alpha*.45})`);
        g.addColorStop(0.7, `hsla(${this.hue-10},90%,40%,${this.alpha*.12})`);
        g.addColorStop(1, 'transparent');
        ec.beginPath(); ec.arc(this.x, this.y, gr, 0, Math.PI*2); ec.fillStyle = g; ec.fill();
        ec.beginPath(); ec.arc(this.x, this.y, this.r, 0, Math.PI*2);
        ec.fillStyle = `hsla(55,100%,97%,${this.alpha})`; ec.fill();
      }
    }
    const sparks = Array.from({ length: N }, (_, i) => new Spark(i));
    (function loop() { ec.clearRect(0, 0, ec_canvas.width, ec_canvas.height); sparks.forEach(s => { s.update(); s.draw(); }); requestAnimationFrame(loop); })();
  }

  // Chat widget
  const chatWidget = document.querySelector('.ai-chat-widget');
  const chatBubble = document.getElementById('chatBubble');
  const chatWindow = document.getElementById('chatWindow');
  const chatForm = document.getElementById('chatForm');
  const userInput = document.getElementById('userInput');
  const chatMessages = document.getElementById('chatMessages');
  const chatHeader = document.getElementById('chatHeader');
  const chatCloseBtn = document.getElementById('chatCloseBtn');

  function positionCW() {
    if (!chatWidget || !chatWindow) return;
    const mg = 10, wW = Math.min(360, window.innerWidth - mg * 2), wH = Math.min(500, window.innerHeight - 120);
    chatWindow.style.width = wW + 'px'; chatWindow.style.height = wH + 'px';
    const r = chatWidget.getBoundingClientRect();
    if (r.right - wW >= mg) { chatWindow.style.right = '0'; chatWindow.style.left = 'auto'; }
    else { chatWindow.style.left = (mg - r.left) + 'px'; chatWindow.style.right = 'auto'; }
    chatWindow.style.bottom = r.top >= wH + 20 ? '74px' : 'auto';
    chatWindow.style.top = r.top >= wH + 20 ? 'auto' : '74px';
  }

  if (chatBubble && chatWindow && chatWidget) {
    let drg = false, mv = false, dX, dY, iR, iB, lt = 0;
    chatBubble.addEventListener('mousedown', (e) => { if (Date.now()-lt<600) return; drg=true; mv=false; dX=e.clientX; dY=e.clientY; iR=parseFloat(chatWidget.style.right)||30; iB=parseFloat(chatWidget.style.bottom)||30; chatWidget.classList.add('dragging'); e.preventDefault(); });
    document.addEventListener('mousemove', (e) => { if(!drg) return; const dx=e.clientX-dX,dy=e.clientY-dY; if(Math.abs(dx)+Math.abs(dy)>5) mv=true; chatWidget.style.right=Math.max(10,Math.min(window.innerWidth-80,iR-dx))+'px'; chatWidget.style.bottom=Math.max(10,Math.min(window.innerHeight-80,iB-dy))+'px'; if(chatWindow.classList.contains('active')) positionCW(); });
    document.addEventListener('mouseup', () => { if(Date.now()-lt<600||!drg) return; chatWidget.classList.remove('dragging'); if(!mv){chatWindow.classList.toggle('active'); document.querySelector('.notification-dot').style.display='none'; chatWidget.classList.toggle('chat-open',chatWindow.classList.contains('active')); if(chatWindow.classList.contains('active')) positionCW();} drg=false; });
    chatBubble.addEventListener('touchstart', (e) => { const t=e.touches[0]; drg=true; mv=false; dX=t.clientX; dY=t.clientY; iR=parseFloat(chatWidget.style.right)||30; iB=parseFloat(chatWidget.style.bottom)||30; chatWidget.classList.add('dragging'); }, {passive:true});
    document.addEventListener('touchmove', (e) => { if(!drg) return; const t=e.touches[0]; const dx=t.clientX-dX,dy=t.clientY-dY; if(Math.abs(dx)+Math.abs(dy)>5) mv=true; chatWidget.style.right=Math.max(10,Math.min(window.innerWidth-80,iR-dx))+'px'; chatWidget.style.bottom=Math.max(10,Math.min(window.innerHeight-80,iB-dy))+'px'; if(chatWindow.classList.contains('active')) positionCW(); e.preventDefault(); }, {passive:false});
    document.addEventListener('touchend', () => { lt=Date.now(); if(!drg) return; chatWidget.classList.remove('dragging'); if(!mv){chatWindow.classList.toggle('active'); document.querySelector('.notification-dot').style.display='none'; chatWidget.classList.toggle('chat-open',chatWindow.classList.contains('active')); if(chatWindow.classList.contains('active')) positionCW();} drg=false; });
    window.addEventListener('resize', () => { if(chatWindow.classList.contains('active')) positionCW(); });
  }
  if (chatCloseBtn && chatWindow) chatCloseBtn.addEventListener('click', () => { chatWindow.classList.remove('active'); if(chatWidget) chatWidget.classList.remove('chat-open'); });
  if (chatHeader && chatWidget && chatWindow && chatCloseBtn) {
    let cd=false, cx,cy,cr,cb;
    chatHeader.addEventListener('mousedown',(e)=>{if(e.target===chatCloseBtn||chatCloseBtn.contains(e.target)) return; cd=true; cx=e.clientX; cy=e.clientY; cr=parseFloat(chatWidget.style.right)||30; cb=parseFloat(chatWidget.style.bottom)||30; chatHeader.classList.add('dragging'); e.preventDefault();});
    document.addEventListener('mousemove',(e)=>{if(!cd) return; chatWidget.style.right=Math.max(10,Math.min(window.innerWidth-80,cr-(e.clientX-cx)))+'px'; chatWidget.style.bottom=Math.max(10,Math.min(window.innerHeight-80,cb-(e.clientY-cy)))+'px'; positionCW();});
    document.addEventListener('mouseup',()=>{if(!cd) return; cd=false; chatHeader.classList.remove('dragging');});
    chatHeader.addEventListener('touchstart',(e)=>{const t=e.touches[0]; cd=true; cx=t.clientX; cy=t.clientY; cr=parseFloat(chatWidget.style.right)||30; cb=parseFloat(chatWidget.style.bottom)||30; chatHeader.classList.add('dragging');},{passive:true});
    document.addEventListener('touchmove',(e)=>{if(!cd) return; const t=e.touches[0]; chatWidget.style.right=Math.max(10,Math.min(window.innerWidth-80,cr-(t.clientX-cx)))+'px'; chatWidget.style.bottom=Math.max(10,Math.min(window.innerHeight-80,cb-(t.clientY-cy)))+'px'; positionCW(); e.preventDefault();},{passive:false});
    document.addEventListener('touchend',()=>{if(!cd) return; cd=false; chatHeader.classList.remove('dragging');});
  }
  if (chatForm && userInput && chatMessages) {
    chatForm.addEventListener('submit', (e) => {
      e.preventDefault(); const text = userInput.value.trim(); if (!text) return;
      addMsg(text,'user'); userInput.value=''; setTimeout(()=>addMsg(reply(text.toLowerCase()),'bot'),1000);
    });
    function addMsg(t,cls){const d=document.createElement('div'); d.className='message '+cls; d.textContent=t; chatMessages.appendChild(d); chatMessages.scrollTop=chatMessages.scrollHeight;}
    function reply(q){
      if(q.includes('netsuite')) return "Mike's 21-Day NetSuite Clarity Sprint locks KPIs and builds exec-ready dashboards fast.";
      if(q.includes('cto')||q.includes('fractional')) return "Mike offers Fractional CTO services — senior leadership for 7- and 8-figure companies, part-time cost.";
      if(q.includes('dcat')||q.includes('bottleneck')||q.includes('team')) return "The DCAT Method removes approval bottlenecks so your team thinks, decides, and executes autonomously.";
      if(q.includes('podcast')||q.includes('gtle')) return "'Gaining the Technology Leadership Edge' — 100+ episodes for CTOs. Listen at gtle.show";
      if(q.includes('blog')) return "Mike's blog covers NetSuite, tech leadership, and the DCAT method. Check out the latest posts!";
      if(q.includes('contact')||q.includes('hire')||q.includes('book')) return "Best way: LinkedIn DM with 'DASHBOARD', 'FIREFIGHTER', or 'PODCAST' — or book a Strategy Session.";
      if(q.match(/^(hi|hello|hey)/)) return "Hello! What can I help you learn about Mike's CTO services or NetSuite expertise?";
      return "Great question! Mike specializes in NetSuite, CTO leadership, and the DCAT team method. What would you like to know?";
    }
  }

  // FAQ accordion
  document.querySelectorAll('.faq-item').forEach(item => {
    const q = item.querySelector('.faq-question');
    if (q) q.addEventListener('click', () => {
      const was = item.classList.contains('active');
      document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
      if (!was) item.classList.add('active');
    });
  });

})();
