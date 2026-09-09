/**
 * EA2000 · home page v3 "Control Room" · front page only, after main.js
 */
(function () {
	'use strict';

	var root = document.querySelector('.home-v3');
	var E = window.ea2000;
	if (!root || !E) {
		return;
	}
	function raf(fn) {
		return window.requestAnimationFrame(fn);
	}

	/* Boot · HUD brackets draw, then each HUD value decodes (Latin letters and digits only, 400ms each, 60ms gap) */
	var frame = root.querySelector('[data-hud-frame]');
	var hud = root.querySelector('[data-hud]');
	var GLYPHS = '#/\\|<>=+*';
	var queue = hud ? Array.prototype.slice.call(hud.querySelectorAll('.hud-val[data-text]')) : [];

	function decode() {
		var el = queue.shift();
		if (!el) {
			return;
		}
		var text = el.getAttribute('data-text');
		var parts = text.split('');
		var t0 = 0;
		raf(function step(now) {
			t0 = t0 || now;
			var p = Math.min(1, (now - t0) / 400);
			var n = Math.floor(p * parts.length);
			el.textContent = p < 1 ? parts.map(function (ch, i) {
				return i < n || !/[A-Za-z0-9]/.test(ch) ? ch : GLYPHS[Math.floor(Math.random() * GLYPHS.length)];
			}).join('') : text;
			if (p < 1) {
				raf(step);
			} else {
				setTimeout(decode, 60);
			}
		});
	}

	function boot() {
		if (frame) {
			frame.classList.add('on');
		}
		if (hud) {
			hud.classList.add('on');
		}
		if (!E.reduced) {
			decode();
		}
	}
	if (E.reduced) {
		boot();
	} else {
		raf(function () { raf(boot); });
	}

	/* System log · terminal typewriter, plays up to data-plays times whenever 40% visible */
	var term = root.querySelector('[data-term]');
	var termOut = term && term.querySelector('[data-term-out]');
	var lines = [];
	try {
		lines = JSON.parse(term.getAttribute('data-lines')).filter(function (l) { return l && typeof l.t === 'string' && l.t; });
	} catch (e) {}
	if (termOut && lines.length) {
		if (E.reduced) {
			termOut.textContent = lines.map(function (l) { return l.t; }).join('\n');
		} else {
			var plays = 0;
			var max = parseInt(term.getAttribute('data-plays'), 10) || 2;
			var typing = null;
			var watch = E.observe(term, function () {
				if (typing) {
					typing.stop();
				}
				if (++plays >= max) {
					watch.stop();
				}
				typing = E.typewriter(termOut, lines, { cps: 36, linePause: 320, wrap: 'span.term-line' });
			}, { threshold: 0.4, repeat: true });
			document.addEventListener('visibilitychange', function () {
				if (document.hidden && typing) {
					typing.stop();
				}
			});
		}
	}

	/* Chapter rail · --p scroll progress when CSS scroll timelines are missing (state, so it runs under reduced too) */
	var html = document.documentElement;
	if (document.querySelector('.rail, .rail-bar') && !(window.CSS && CSS.supports && CSS.supports('animation-timeline: scroll()'))) {
		var pRaf = 0;
		var queueP = function () {
			pRaf = pRaf || raf(function () {
				pRaf = 0;
				var max = html.scrollHeight - window.innerHeight;
				html.style.setProperty('--p', max > 0 ? String(Math.min(1, Math.max(0, window.scrollY / max))) : '0');
			});
		};
		window.addEventListener('scroll', queueP, { passive: true });
		window.addEventListener('resize', queueP);
		queueP();
	}

	/* Chapter rail · current chapter number, tick animation, aria-current on the rail link */
	var railN = document.querySelector('[data-rail-n]');
	var links = document.querySelectorAll('[data-rail-link]');
	var sections = root.querySelectorAll('section[data-chapter]');
	if (railN && sections.length && 'IntersectionObserver' in window) {
		var chapterIO = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				var n = entry.target.getAttribute('data-chapter') || '00';
				if (!entry.isIntersecting || railN.textContent === n) {
					return;
				}
				railN.textContent = n;
				if (!E.reduced) {
					railN.removeAttribute('data-tick');
					raf(function () { railN.setAttribute('data-tick', ''); });
				}
				links.forEach(function (a) {
					if (a.getAttribute('data-rail-link') === n) {
						a.setAttribute('aria-current', 'true');
					} else {
						a.removeAttribute('aria-current');
					}
				});
			});
		}, { rootMargin: '-45% 0px -45% 0px', threshold: 0 });
		sections.forEach(function (s) {
			chapterIO.observe(s);
		});
	}
})();
