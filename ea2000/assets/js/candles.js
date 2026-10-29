/*! EA2000 · ฉากหลังแท่งเทียนของหน้าแรก
 * วาดด้วย canvas ล้วน ไม่มีไลบรารี ไม่มีคำขอเครือข่าย
 * ตัวเลขเป็นการสุ่มด้วยเมล็ดคงที่ ไม่ใช่ราคาจริงและไม่ใช่ผลการเทรด
 * หยุดวาดเมื่อเลื่อนพ้นจอหรือสลับแท็บ และหยุดสนิทเมื่อผู้ใช้ตั้งลดการเคลื่อนไหว */
(function () {
  var host = document.querySelector('.hero-bg');
  if (!host) return;
  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var cv = document.createElement('canvas');
  cv.setAttribute('aria-hidden', 'true');
  host.innerHTML = '';
  host.appendChild(cv);
  var ctx = cv.getContext('2d');

  var UP = '#2FD16A', DOWN = '#FF5A4E';
  var SPAN = 2600;        // how long one candle takes to form, ms
  var BODY = 11, GAP = 9; // candle body width and gap, css px
  var W = 0, H = 0, dpr = 1, cols = 0;

  // deterministic noise so every load and every screenshot starts the same
  var seed = 20261029;
  function rnd() { seed |= 0; seed = seed + 0x6D2B79F5 | 0; var t = Math.imul(seed ^ seed >>> 15, 1 | seed); t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t; return ((t ^ t >>> 14) >>> 0) / 4294967296; }

  var price = 100, candles = [], live = null, t = 0;

  function newCandle() { return { o: price, c: price, h: price, l: price }; }
  function tick() {
    // random walk with a gentle pull back to the middle of the range
    var drift = (100 - price) * 0.004;
    price += (rnd() - 0.5) * 1.5 + drift;
    live.c = price;
    if (price > live.h) live.h = price;
    if (price < live.l) live.l = price;
  }

  function measure() {
    var r = host.getBoundingClientRect();
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    W = Math.max(1, Math.round(r.width)); H = Math.max(1, Math.round(r.height));
    cv.width = W * dpr; cv.height = H * dpr;
    cv.style.width = W + 'px'; cv.style.height = H + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    cols = Math.ceil(W / (BODY + GAP)) + 2;
    while (candles.length > cols) candles.shift();
    while (candles.length < cols) { // backfill history so it never starts empty
      var c0 = newCandle();
      for (var i = 0; i < 26; i++) { tickInto(c0); }
      candles.push(c0);
    }
  }
  function tickInto(c) {
    var drift = (100 - price) * 0.004;
    price += (rnd() - 0.5) * 1.5 + drift;
    c.c = price; if (price > c.h) c.h = price; if (price < c.l) c.l = price;
  }

  var lo = 94, hi = 106;
  function draw(progress) {
    ctx.clearRect(0, 0, W, H);
    var all = candles.concat([live]);
    var mn = Infinity, mx = -Infinity;
    for (var i = 0; i < all.length; i++) { if (all[i].l < mn) mn = all[i].l; if (all[i].h > mx) mx = all[i].h; }
    var pad = (mx - mn) * 0.18 || 1;
    // ease the scale so the chart never jumps
    lo += ((mn - pad) - lo) * 0.06;
    hi += ((mx + pad) - hi) * 0.06;
    var top0 = H * 0.17, band = H * 0.64;
    var y = function (p) { return top0 + (1 - (p - lo) / (hi - lo)) * band; };

    var step = BODY + GAP;
    var shift = progress * step;                 // slides left as the live candle fills
    var x = W - step * 0.5 - shift + step;       // live candle sits just past the right edge

    for (var k = all.length - 1; k >= 0; k--) {
      var c = all[k], up = c.c >= c.o, col = up ? UP : DOWN;
      var cx = x - (all.length - 1 - k) * step;
      if (cx < -step || cx > W + step) continue;
      ctx.strokeStyle = col; ctx.fillStyle = col; ctx.lineWidth = 2;
      ctx.beginPath(); ctx.moveTo(cx, y(c.h)); ctx.lineTo(cx, y(c.l)); ctx.stroke();
      var top = y(Math.max(c.o, c.c)), h = Math.max(2, Math.abs(y(c.o) - y(c.c)));
      ctx.fillRect(cx - BODY / 2, top, BODY, h);
    }
  }

  live = newCandle();
  measure();
  var ro = window.ResizeObserver ? new ResizeObserver(measure) : null;
  if (ro) ro.observe(host); else window.addEventListener('resize', measure);

  function advance(dt) {
    t += dt;
    var ticks = Math.floor(dt / 55);
    for (var i = 0; i < ticks; i++) tick();
    while (t >= SPAN) {
      t -= SPAN;
      candles.push(live); if (candles.length > cols) candles.shift();
      live = newCandle();
    }
    draw(t / SPAN);
  }

  // manual mode lets a screenshot run drive exact frames
  window.__candleFrame = function (dt) { advance(dt); };

  if (reduced) { advance(0); return; }
  if (window.__manualCandles) { advance(0); return; }

  // stop drawing when the hero is off screen or the tab is in the background
  var running = true, raf = 0, last = performance.now();
  function loop(now) {
    var dt = Math.min(now - last, 120); last = now;
    advance(dt);
    raf = requestAnimationFrame(loop);
  }
  function start() { if (running || document.hidden) return; running = true; last = performance.now(); raf = requestAnimationFrame(loop); }
  function stop() { running = false; cancelAnimationFrame(raf); }
  raf = requestAnimationFrame(loop);
  document.addEventListener('visibilitychange', function () { document.hidden ? stop() : (running = false, start()); });
  if (window.IntersectionObserver) {
    new IntersectionObserver(function (e) { e[0].isIntersecting ? (running = false, start()) : stop(); }, { threshold: 0 }).observe(host);
  }
})();
