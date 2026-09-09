/**
 * EA2000 · theme scripts
 */
(function () {
	'use strict';

	/* Shared namespace (spec 0.3) · home.js and the footer modules read window.ea2000 */
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
	var noop = { stop: function () {} };
	function raf(fn) {
		return window.requestAnimationFrame(fn);
	}
	var observers = {};

	/* One IntersectionObserver per threshold · cb(entry) once then unobserve · opts.repeat keeps observing until stop() */
	function observe(el, cb, opts) {
		opts = opts || {};
		if (!el) {
			return noop;
		}
		if (!('IntersectionObserver' in window) || reduced) {
			cb({ target: el, isIntersecting: true });
			return noop;
		}
		var key = String(typeof opts.threshold === 'number' ? opts.threshold : 0.12);
		var io = observers[key] || (observers[key] = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				var task = entry.target.ea2000Task;
				if (!entry.isIntersecting || !task) {
					return;
				}
				if (!task.repeat) {
					io.unobserve(entry.target);
					entry.target.ea2000Task = null;
				}
				task.cb(entry);
			});
		}, { threshold: Number(key), rootMargin: '0px 0px -40px 0px' }));
		el.ea2000Task = { cb: cb, repeat: !!opts.repeat };
		io.observe(el);
		return {
			stop: function () {
				el.ea2000Task = null;
				io.unobserve(el);
			}
		};
	}

	/* Grapheme split so Thai vowels and tone marks never appear half-typed */
	var segmenter = null;
	try {
		segmenter = new Intl.Segmenter('th', { granularity: 'grapheme' });
	} catch (e) {}
	function graphemes(str) {
		return segmenter ? Array.from(segmenter.segment(str), function (s) { return s.segment; }) : str.split('');
	}

	/* Typewriter (spec 6.1) · lines = string | { t, c } · opts: cps, linePause, wrap ('span.term-line'), onDone · returns { stop } */
	function typewriter(out, lines, opts) {
		opts = opts || {};
		var stopped = false;
		var done = function () {
			if (opts.onDone) {
				opts.onDone();
			}
		};
		if (!out || !lines || !lines.length) {
			done();
			return noop;
		}
		var cps = opts.cps || 32;
		var wrap = opts.wrap ? opts.wrap.replace(/^span\./, '') : '';
		var items = lines.map(function (l) {
			return typeof l === 'string' ? { t: l, c: '' } : { t: String(l.t || ''), c: l.c || '' };
		});
		var el = out;
		var prefix = '';
		out.textContent = '';

		/* Wrap mode: one span per line, newline text nodes between · plain mode: prefix keeps finished lines */
		function open(i) {
			if (!wrap) {
				prefix = i ? el.textContent + '\n' : '';
				return;
			}
			if (i) {
				out.appendChild(document.createTextNode('\n'));
			}
			el = document.createElement('span');
			el.className = wrap + (items[i].c ? ' ' + items[i].c : '');
			out.appendChild(el);
		}

		if (reduced) {
			items.forEach(function (item, i) {
				open(i);
				el.textContent = prefix + item.t;
			});
			done();
			return noop;
		}

		var li = 0;
		var ci = 0;
		var chars = graphemes(items[0].t);
		var budget = 0;
		var last = 0;
		var waitUntil = 0;
		open(0);

		function frame(now) {
			if (stopped) {
				return;
			}
			budget += last ? Math.min(now - last, 250) * cps / 1000 : 0;
			last = now;
			if (now >= waitUntil) {
				while (budget >= 1 && ci < chars.length) {
					ci += 1;
					budget -= 1;
					el.textContent = prefix + chars.slice(0, ci).join('');
				}
				if (ci >= chars.length) {
					li += 1;
					if (li >= items.length) {
						done();
						return;
					}
					chars = graphemes(items[li].t);
					ci = 0;
					budget = 0;
					waitUntil = now + (opts.linePause || 0);
					open(li);
				}
			}
			raf(frame);
		}
		raf(frame);
		return { stop: function () { stopped = true; } };
	}

	window.ea2000 = { reduced: reduced, fine: fine, observe: observe, typewriter: typewriter };

	/* Header scrolled state */
	var header = document.querySelector('.site-header');
	function onScroll() {
		if (header) {
			header.classList.toggle('scrolled', window.scrollY > 10);
		}
	}
	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });

	/* Mobile navigation */
	var toggle = document.querySelector('.nav-toggle');
	var nav = document.getElementById('site-nav');

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = document.body.classList.toggle('nav-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});

		nav.addEventListener('click', function (e) {
			var link = e.target.closest('a');
			if (link && !link.parentNode.classList.contains('menu-item-has-children')) {
				document.body.classList.remove('nav-open');
				toggle.setAttribute('aria-expanded', 'false');
			}
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && document.body.classList.contains('nav-open')) {
				document.body.classList.remove('nav-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.focus();
			}
		});
	}

	/* Parent menu items with a submenu toggle the dropdown instead of navigating */
	var parentLinks = document.querySelectorAll('.nav-list .menu-item-has-children > a');
	parentLinks.forEach(function (link) {
		link.setAttribute('aria-haspopup', 'true');
		link.setAttribute('aria-expanded', 'false');
		link.addEventListener('click', function (e) {
			e.preventDefault();
			var li = link.parentNode;
			var willOpen = !li.classList.contains('is-open');

			var siblings = li.parentNode.querySelectorAll('.menu-item-has-children.is-open');
			siblings.forEach(function (other) {
				if (other !== li) {
					other.classList.remove('is-open');
					var otherLink = other.querySelector(':scope > a');
					if (otherLink) {
						otherLink.setAttribute('aria-expanded', 'false');
					}
				}
			});

			li.classList.toggle('is-open', willOpen);
			link.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
		});
	});

	document.addEventListener('click', function (e) {
		if (e.target.closest('.nav-list .menu-item-has-children')) {
			return;
		}
		document.querySelectorAll('.nav-list .menu-item-has-children.is-open').forEach(function (li) {
			li.classList.remove('is-open');
			var openLink = li.querySelector(':scope > a');
			if (openLink) {
				openLink.setAttribute('aria-expanded', 'false');
			}
		});
	});

	/* Mobile app-style bottom navigation */
	var mobileNav = document.querySelector('.mobile-app-nav');
	if (mobileNav) {
		document.body.classList.add('has-mobile-app-nav');

		var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
		var activeItem = null;
		var activeLength = 0;
		var mobileItems = mobileNav.querySelectorAll('.mobile-app-nav-item');

		mobileItems.forEach(function (item) {
			var itemUrl = new URL(item.href, window.location.origin);
			var itemPath = itemUrl.pathname.replace(/\/+$/, '') || '/';
			var isMatch = itemPath === '/' ? currentPath === '/' : (currentPath === itemPath || currentPath.indexOf(itemPath + '/') === 0);

			if (!item.classList.contains('is-action') && isMatch && itemPath.length >= activeLength) {
				activeItem = item;
				activeLength = itemPath.length;
			}

			item.addEventListener('pointerdown', function () {
				item.classList.add('is-pressing');
			});

			item.addEventListener('pointerup', function () {
				item.classList.remove('is-pressing');
			});

			item.addEventListener('pointerleave', function () {
				item.classList.remove('is-pressing');
			});
		});

		if (activeItem) {
			activeItem.classList.add('is-active');
		}
	}

	/* Analytics · dedicated LINE click event for GA4/Site Kit */
	document.addEventListener('click', function (e) {
		var link = e.target.closest('a[href]');
		if (!link) {
			return;
		}

		var href = link.getAttribute('href') || '';
		var absoluteUrl;
		try {
			absoluteUrl = new URL(href, window.location.href);
		} catch (err) {
			return;
		}

		var host = absoluteUrl.hostname.replace(/^www\./, '').toLowerCase();
		var isLineLink = host === 'line.me' || host === 'lin.ee' || host === 'liff.line.me' || href.indexOf('line://') === 0;
		if (!isLineLink) {
			return;
		}

		var label = (link.textContent || link.getAttribute('aria-label') || 'LINE').replace(/\s+/g, ' ').trim();
		var payload = {
			link_url: absoluteUrl.href,
			link_text: label,
			page_path: window.location.pathname,
			page_title: document.title,
			link_pos: link.dataset.linePos || ''
		};

		if (typeof window.gtag === 'function') {
			window.gtag('event', 'line_click', payload);
		} else if (Array.isArray(window.dataLayer)) {
			window.dataLayer.push(Object.assign({ event: 'line_click' }, payload));
		}
	});

	/* Reveal on scroll · .reveal (inner pages, fade-up) and .watch (state only) both get .in once */
	document.querySelectorAll('.reveal, .watch').forEach(function (el) {
		observe(el, function (entry) {
			entry.target.classList.add('in');
		});
	});

	/* Footer · typing prompt (spec 4.3) */
	var promptEl = document.querySelector('[data-prompt]');
	var promptOut = promptEl && promptEl.querySelector('[data-prompt-out]');
	var promptLines = [];
	try {
		promptLines = JSON.parse(promptEl ? promptEl.getAttribute('data-lines') : '[]').filter(function (l) { return typeof l === 'string' && l; });
	} catch (e) {}
	if (promptOut && promptLines.length) {
		if (reduced) {
			promptOut.textContent = promptLines.join(' · ');
		} else {
			var loops = parseInt(promptEl.getAttribute('data-loops'), 10) || 3;
			var pi = 0;
			var pCount = 0;
			/* จองความสูงของบรรทัดที่สูงที่สุดไว้ก่อน (วัดจริงทุกบรรทัด ไม่ใช่นับตัวอักษร) กัน index/statusbar ขยับตอนพิมพ์ · วัดใหม่เมื่อฟอนต์มาและเมื่อ resize */
			var promptLine = promptOut.parentNode;
			var promptRaf = 0;
			var reservePrompt = function () {
				var keep = promptOut.textContent;
				var max = 0;
				promptLine.style.minHeight = '';
				promptLines.forEach(function (l) {
					promptOut.textContent = l;
					max = Math.max(max, promptLine.offsetHeight);
				});
				promptOut.textContent = keep;
				promptLine.style.minHeight = max + 'px';
			};
			reservePrompt();
			if (document.fonts && document.fonts.ready) {
				document.fonts.ready.then(reservePrompt);
			}
			window.addEventListener('resize', function () {
				if (!promptRaf) {
					promptRaf = raf(function () {
						promptRaf = 0;
						reservePrompt();
					});
				}
			});
			var cycle = function () {
				typewriter(promptOut, [promptLines[pi]], {
					cps: 30,
					onDone: function () {
						setTimeout(function () {
							pi = (pi + 1) % promptLines.length;
							if (++pCount < loops * promptLines.length) {
								cycle();
							}
						}, 4000);
					}
				});
			};
			observe(promptEl, cycle);
		}
	}

	/* Footer · pointer spotlight · bound only for hover-capable fine pointers, never under reduced motion */
	var spot = document.querySelector('.site-footer[data-spotlight]');
	if (spot && fine && !reduced) {
		var spotRaf = 0;
		var setSpot = function (x, y) {
			if (!spotRaf) {
				spotRaf = raf(function () {
					spotRaf = 0;
					spot.style.setProperty('--mx', x + 'px');
					spot.style.setProperty('--my', y + 'px');
				});
			}
		};
		spot.addEventListener('pointermove', function (e) {
			var r = spot.getBoundingClientRect();
			setSpot(e.clientX - r.left, e.clientY - r.top);
		}, { passive: true });
		spot.addEventListener('pointerleave', function () {
			setSpot(-999, -999);
		});
	}

	/* Footer · Bangkok clock · minutes only, first tick then on the minute */
	var clockEl = document.querySelector('[data-clock-out]');
	if (clockEl) {
		try {
			var clockFmt = new Intl.DateTimeFormat('th-TH', { timeZone: clockEl.getAttribute('data-tz') || 'Asia/Bangkok', hour: '2-digit', minute: '2-digit', hour12: false });
			var tick = function () {
				clockEl.textContent = clockFmt.format(new Date());
			};
			tick();
			setTimeout(function () {
				tick();
				setInterval(tick, 60000);
			}, 60000 - (Date.now() % 60000));
		} catch (e) {}
	}

	/* Related posts rail · arrow scroll + show controls only when overflowing */
	document.querySelectorAll('.related-section').forEach(function (section) {
		var rail = section.querySelector('.related-rail');
		if (!rail) {
			return;
		}

		function updateControls() {
			var scrollable = rail.scrollWidth > rail.clientWidth + 4;
			section.classList.toggle('is-scrollable', scrollable);
		}

		section.querySelectorAll('.rail-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var dir = parseInt(btn.getAttribute('data-dir'), 10) || 1;
				var card = rail.querySelector('.post-card');
				var step = card ? card.offsetWidth + 18 : rail.clientWidth * 0.8;
				rail.scrollBy({ left: dir * step, behavior: 'smooth' });
			});
		});

		updateControls();
		window.addEventListener('resize', updateControls);
	});

	/* Share · copy link to clipboard */
	document.querySelectorAll('.share-copy').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var url = btn.getAttribute('data-url') || window.location.href;
			var done = function () {
				btn.classList.add('is-copied');
				setTimeout(function () {
					btn.classList.remove('is-copied');
				}, 1600);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done).catch(function () {});
			} else {
				var ta = document.createElement('textarea');
				ta.value = url;
				ta.setAttribute('readonly', '');
				ta.style.position = 'absolute';
				ta.style.left = '-9999px';
				document.body.appendChild(ta);
				ta.select();
				try {
					document.execCommand('copy');
					done();
				} catch (e) {}
				document.body.removeChild(ta);
			}
		});
	});

	/* Reading progress bar */
	var progress = document.querySelector('.reading-progress span');
	var progressArticle = document.querySelector('.single-article');
	if (progress && progressArticle) {
		var onProgress = function () {
			var total = progressArticle.offsetHeight - window.innerHeight;
			var scrolled = Math.min(Math.max(-progressArticle.getBoundingClientRect().top, 0), Math.max(total, 0));
			progress.style.width = (total > 0 ? (scrolled / total) * 100 : 0) + '%';
		};
		window.addEventListener('scroll', onProgress, { passive: true });
		window.addEventListener('resize', onProgress);
		onProgress();
	}

	/* Load more posts (AJAX) */
	var loadMoreBtn = document.querySelector('.load-more-btn');
	if (loadMoreBtn && window.ea2000LoadMore) {
		var loadMoreLabel = loadMoreBtn.textContent.trim();
		loadMoreBtn.addEventListener('click', function () {
			var page = parseInt(loadMoreBtn.getAttribute('data-page'), 10) || 1;
			var max = parseInt(loadMoreBtn.getAttribute('data-max'), 10) || 1;
			var next = page + 1;
			if (loadMoreBtn.classList.contains('is-loading') || next > max) {
				return;
			}
			loadMoreBtn.classList.add('is-loading');
			loadMoreBtn.textContent = 'กำลังโหลด…';

			var data = new FormData();
			data.append('action', 'ea2000_load_more');
			data.append('nonce', window.ea2000LoadMore.nonce);
			data.append('page', next);
			data.append('query', loadMoreBtn.getAttribute('data-query') || '');

			fetch(window.ea2000LoadMore.ajaxUrl, {
				method: 'POST',
				body: data,
				credentials: 'same-origin'
			})
				.then(function (res) { return res.text(); })
				.then(function (html) {
					var grid = document.querySelector('.posts-grid');
					if (grid && html.trim()) {
						grid.insertAdjacentHTML('beforeend', html);
					}
					loadMoreBtn.setAttribute('data-page', String(next));
					loadMoreBtn.classList.remove('is-loading');
					loadMoreBtn.textContent = loadMoreLabel;
					if (next >= max) {
						var wrap = loadMoreBtn.closest('.load-more');
						if (wrap) {
							wrap.parentNode.removeChild(wrap);
						}
					}
				})
				.catch(function () {
					loadMoreBtn.classList.remove('is-loading');
					loadMoreBtn.textContent = loadMoreLabel;
				});
		});
	}

	/* Cookie consent + gated tracking (โหลด GA/Pixel เฉพาะหลังยอมรับ; ผู้ที่เคยยอมรับโหลดต่อแม้ปิดแบนเนอร์) */
	var trackingCfg = window.ea2000Tracking || {};
	var cookieBar = document.querySelector('.cookie-consent');

	if (trackingCfg.ga || trackingCfg.pixel || cookieBar) {
		var getConsent = function () {
			var m = document.cookie.match(/(?:^|;\s*)ea2000_consent=([^;]+)/);
			return m ? m[1] : '';
		};
		var setConsent = function (value) {
			var d = new Date();
			d.setTime(d.getTime() + 365 * 24 * 60 * 60 * 1000);
			document.cookie = 'ea2000_consent=' + value + '; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
		};
		var loadTrackers = function () {
			if (trackingCfg.ga) {
				var g = document.createElement('script');
				g.async = true;
				g.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(trackingCfg.ga);
				document.head.appendChild(g);
				window.dataLayer = window.dataLayer || [];
				window.gtag = function () { window.dataLayer.push(arguments); };
				window.gtag('js', new Date());
				window.gtag('config', trackingCfg.ga);
			}
			if (trackingCfg.pixel) {
				!function (f, b, e, v, n, t, s) {
					if (f.fbq) return;
					n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
					if (!f._fbq) f._fbq = n;
					n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = [];
					t = b.createElement(e); t.async = !0; t.src = v;
					s = b.getElementsByTagName(e)[0];
					if (s && s.parentNode) { s.parentNode.insertBefore(t, s); } else { (b.head || b.documentElement).appendChild(t); }
				}(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
				window.fbq('init', trackingCfg.pixel);
				window.fbq('track', 'PageView');
			}
		};

		if (getConsent() === 'accepted') {
			loadTrackers();
		}

		if (cookieBar) {
			var sessionDismissed = function () {
				try { return sessionStorage.getItem('ea2000ConsentDismissed') === '1'; } catch (e) { return false; }
			};
			var setSessionDismissed = function () {
				try { sessionStorage.setItem('ea2000ConsentDismissed', '1'); } catch (e) {}
			};

			var consent = getConsent();
			if (consent !== 'accepted' && consent !== 'declined' && !sessionDismissed()) {
				cookieBar.classList.add('is-visible');
			}

			var acceptBtn = cookieBar.querySelector('.cookie-accept');
			var declineBtn = cookieBar.querySelector('.cookie-decline');
			var closeBtn = cookieBar.querySelector('.cookie-consent-close');
			if (acceptBtn) {
				acceptBtn.addEventListener('click', function () {
					setConsent('accepted');
					cookieBar.classList.remove('is-visible');
					loadTrackers();
				});
			}
			if (declineBtn) {
				declineBtn.addEventListener('click', function () {
					setConsent('declined');
					cookieBar.classList.remove('is-visible');
				});
			}
			if (closeBtn) {
				closeBtn.addEventListener('click', function () {
					setSessionDismissed();
					cookieBar.classList.remove('is-visible');
				});
			}
		}
	}
})();
