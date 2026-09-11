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

	/* บาร์ล่างมือถือ · แผงคอนโซล (Console Deck)
	   PHP ส่ง is-active / aria-current / --dock-x มากับ HTML แล้ว บล็อกนี้มีหน้าที่เพิ่มลูกเล่นอย่างเดียว
	   ปิด JS แล้วบาร์ยังถูกต้องครบ: ลิงก์กดได้ คีย์ของหน้าปัจจุบันติดไฟ ไฟบนรางจอดถูกช่อง
	   ทุกช่องกว้างเท่ากัน (grid 1fr) ตำแหน่งไฟจึงเป็นเปอร์เซ็นต์ ไม่ต้องวัดขนาดจริง
	   จึงไม่ต้องคำนวณใหม่ตอนหมุนจอหรือเปลี่ยนขนาดหน้าต่าง */
	var dock = document.querySelector('.mobile-app-nav');

	if (dock) {
		var dockKeys = Array.prototype.slice.call(dock.querySelectorAll('.dock-key'));
		var dockSeats = Math.max(1, dockKeys.length);
		var dockNarrow = window.matchMedia('(max-width: 760px)');
		var dockWait = 0;
		var dockLastY = Math.max(0, window.pageYOffset || 0);
		var dockDrift = 0;

		/* คลาสเดิมของธีม เก็บไว้เป็นจุดเกาะให้โค้ดอื่น · การเว้นที่ท้ายหน้าเป็นงานของ div.dock-spacer แล้ว */
		document.body.classList.add('has-mobile-app-nav');

		/* ย้ายไฟบนรางไปจอดช่องที่ระบุ */
		function dockLight(seat) {
			if (seat < 0 || seat >= dockSeats) {
				return;
			}
			dock.style.setProperty('--dock-x', ((seat + 0.5) * (100 / dockSeats)).toFixed(3) + '%');
			dock.classList.add('is-lit');
		}

		/* เลิกสถานะกำลังไปหน้าใหม่ */
		function dockRest() {
			window.clearTimeout(dockWait);
			dock.classList.remove('is-going');
			dockKeys.forEach(function (key) {
				key.classList.remove('is-going');
				key.classList.remove('is-press');
			});
		}

		/* กันเหนียว: ถ้าฝั่ง PHP หาแท็บของหน้านี้ไม่เจอ (ปลั๊กอินเปลี่ยน URL หรือแคชแปลก) ให้ JS หาให้
		   นี่เป็นที่เดียวที่ JS แตะ aria-current เพราะเป็นการบอกตำแหน่งจริง ไม่ใช่ผลของการกด */
		if (!dock.querySelector('.dock-key.is-active')) {
			var dockHere = window.location.pathname.replace(/\/+$/, '') || '/';
			var dockBest = -1;
			var dockPick = null;
			var dockSeat = -1;

			dockKeys.forEach(function (key, seat) {
				if (key.classList.contains('is-action')) {
					return;
				}

				var path;
				try {
					path = new URL(key.href, window.location.href).pathname.replace(/\/+$/, '') || '/';
				} catch (err) {
					return;
				}

				var hit = path === '/' ? dockHere === '/' : (dockHere === path || dockHere.indexOf(path + '/') === 0);
				if (hit && path.length > dockBest) {
					dockBest = path.length;
					dockPick = key;
					dockSeat = seat;
				}
			});

			if (dockPick) {
				dockPick.classList.add('is-active');
				dockPick.setAttribute('aria-current', 'page');
				dockLight(dockSeat);
			}
		}

		/* แรงกด · CSS :active ทำงานอยู่แล้ว คลาสนี้ช่วยให้คีย์เด้งกลับตอนเลื่อนนิ้วออกหรือการแตะถูกยกเลิก
		   และช่วยให้ iOS ยอมติด :active ด้วย เพราะมีตัวรับ pointer อยู่บนอิลิเมนต์ */
		dockKeys.forEach(function (key) {
			key.addEventListener('pointerdown', function () {
				key.classList.add('is-press');
			});

			['pointerup', 'pointercancel', 'pointerleave', 'blur'].forEach(function (evt) {
				key.addEventListener(evt, function () {
					key.classList.remove('is-press');
				});
			});
		});

		/* แตะบาร์ตอนที่มันย่ออยู่ = คลี่กลับมาเต็มก่อน จะได้อ่านป้ายก่อนเลือก */
		dock.addEventListener('pointerdown', function () {
			dock.classList.remove('is-slim');
			dockDrift = 0;
		});

		/* กดคีย์แล้วไฟวิ่งไปจอดช่องนั้นทันที พร้อมแถบสแกนบนราง ค้างไว้จนหน้าใหม่มาแทน
		   ย้ายแค่ภาพ ไม่แตะ aria-current เพราะหน้ายังไม่เปลี่ยน */
		dock.addEventListener('click', function (e) {
			var key = e.target && e.target.closest ? e.target.closest('.dock-key') : null;
			if (!key || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button > 0) {
				return;
			}
			if ('_blank' === key.target) {
				return; // ปุ่ม LINE เปิดแท็บใหม่ หน้านี้ไม่ได้ไปไหน
			}

			var href = key.getAttribute('href') || '';
			if ('' === href || '#' === href.charAt(0) || key.href === window.location.href) {
				return;
			}

			var seat = parseInt(key.getAttribute('data-slot'), 10);
			if (!isNaN(seat)) {
				dockLight(seat);
			}

			key.classList.add('is-going');
			dock.classList.add('is-going');
			window.clearTimeout(dockWait);
			dockWait = window.setTimeout(dockRest, 8000);
		});

		/* กลับมาด้วยปุ่มย้อนกลับ (bfcache) ต้องไม่ค้างสถานะกำลังโหลด */
		window.addEventListener('pageshow', dockRest);

		/* ย่อบาร์เมื่อเลื่อนอ่านลงต่อเนื่อง คลี่กลับทันทีเมื่อเลื่อนขึ้น · ย่อ ไม่ใช่ ซ่อน
		   ปุ่ม LINE จึงอยู่บนจอตลอด เพราะคนที่กำลังอ่านกลางหน้าคือคนที่พร้อมทักที่สุด
		   ติดหัวหน้าและท้ายหน้าให้กางไว้เสมอ */
		function dockScroll() {
			if (!dockNarrow.matches || reduced) {
				return;
			}

			var y = Math.max(0, window.pageYOffset || 0);
			var step = y - dockLastY;
			dockLastY = y;
			if (Math.abs(step) < 2) {
				return;
			}

			var toEnd = document.documentElement.scrollHeight - window.innerHeight - y;
			if (y < 220 || toEnd < 140) {
				dockDrift = 0;
				dock.classList.remove('is-slim');
				return;
			}

			dockDrift = (dockDrift > 0) === (step > 0) ? dockDrift + step : step;
			if (dockDrift > 56) {
				dockDrift = 0;
				dock.classList.add('is-slim');
			} else if (dockDrift < -18) {
				dockDrift = 0;
				dock.classList.remove('is-slim');
			}
		}

		window.addEventListener('scroll', dockScroll, { passive: true });

		/* หมุนจอหรือแถบ URL ของเบราว์เซอร์ยืดหด: ตั้งจุดวัดใหม่
		   คลี่บาร์กลับเฉพาะตอนหน้าเลื่อนแทบไม่ได้แล้วหรือออกจากช่วงจอมือถือ */
		function dockRecalibrate() {
			dockLastY = Math.max(0, window.pageYOffset || 0);
			dockDrift = 0;
			if (!dockNarrow.matches || document.documentElement.scrollHeight - window.innerHeight < 260) {
				dock.classList.remove('is-slim');
			}
		}

		window.addEventListener('resize', dockRecalibrate, { passive: true });
		window.addEventListener('orientationchange', dockRecalibrate);

		if (dockNarrow.addEventListener) {
			dockNarrow.addEventListener('change', dockRecalibrate);
		} else if (dockNarrow.addListener) {
			dockNarrow.addListener(dockRecalibrate);
		}
	}

	/* นับคลิก LINE · แยกบัญชี LINE OA (line_click) กับกลุ่ม OpenChat (openchat_click)
	   ส่งเฉพาะเมื่อแท็กถูกโหลดแล้ว ซึ่งเกิดหลังผู้ใช้ยินยอมหมวดนั้นเท่านั้น (บล็อกความยินยอมท้ายไฟล์)
	   Meta ได้ Contact เฉพาะการทัก LINE OA · ตำแหน่งปุ่มอ่านจาก data-line-pos ถ้าไม่มีใช้ id ของกล่องที่ใกล้ที่สุด */
	document.addEventListener('click', function (e) {
		var link = e.target && e.target.closest ? e.target.closest('a[href]') : null;
		if (!link) {
			return;
		}

		var absoluteUrl;
		try {
			absoluteUrl = new URL(link.getAttribute('href') || '', window.location.href);
		} catch (err) {
			return;
		}

		var host = absoluteUrl.hostname.replace(/^www\./, '').toLowerCase();
		var isLineLink = host === 'line.me' || host === 'lin.ee' || host === 'liff.line.me' || absoluteUrl.protocol === 'line:';
		if (!isLineLink) {
			return;
		}

		var isOpenChat = host === 'line.me' && /^\/ti\/g2\//.test(absoluteUrl.pathname);
		var copy = link.cloneNode(true);
		Array.prototype.forEach.call(copy.querySelectorAll('.sr-only'), function (node) {
			node.parentNode.removeChild(node);
		});
		var label = (link.getAttribute('aria-label') || copy.textContent || 'LINE').replace(/\s+/g, ' ').trim();
		var box = link.parentNode && link.parentNode.closest ? link.parentNode.closest('[id]') : null;
		var payload = {
			link_url: absoluteUrl.href,
			link_text: label,
			link_pos: link.getAttribute('data-line-pos') || (box ? box.id : ''),
			page_path: window.location.pathname,
			page_title: document.title
		};
		if (link.getAttribute('data-line-pkg')) {
			payload.package_name = link.getAttribute('data-line-pkg');
		}

		if (typeof window.gtag === 'function') {
			window.gtag('event', isOpenChat ? 'openchat_click' : 'line_click', payload);
		}
		if (!isOpenChat && typeof window.fbq === 'function') {
			window.fbq('track', 'Contact', { content_name: payload.link_pos || 'line' });
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

	/* ความยินยอมคุกกี้ (PDPA) · inc/consent.php
	   - การ์ดพิมพ์เหมือนกันทุกหน้า ที่นี่ตัดสินใจเองว่าจะแสดงไหม เพราะแคชของโฮสต์เสิร์ฟ HTML ชุดเดียวให้ทุกคน
	   - ไม่โหลด gtag.js หรือ fbevents.js ก่อนผู้ใช้ยินยอมหมวดนั้น · กดปิด (X) = ไม่ยินยอมสำหรับรอบนั้น แล้วถามใหม่รอบหน้า
	   - คุกกี้ ea2000_consent = v<รุ่น>.a<0|1>.m<0|1>.t<unix>.id<สุ่ม> อายุ 12 เดือนเท่ากันทั้งยอมรับและปฏิเสธ
	   - ถอนความยินยอม: หยุดแท็ก ลบคุกกี้ของหมวดนั้นทั้งบนโดเมนหลักและ .โดเมน แล้วรีโหลดหน้า */
	var consentCfg = window.ea2000Consent || {};
	var consentBox = document.getElementById('cookie-settings');
	var consentVersion = /^\d+$/.test(String(consentCfg.version)) ? String(consentCfg.version) : '1';
	var consentTools = { analytics: !!consentCfg.ga, marketing: !!consentCfg.pixel };
	var consentHasOptional = consentTools.analytics || consentTools.marketing;
	var consentLoaded = { analytics: false, marketing: false };
	var consentTrigger = null;

	function consentRead() {
		var m = document.cookie.match(/(?:^|;\s*)ea2000_consent=([^;]+)/);
		var p = m ? /^v(\d+)\.a([01])\.m([01])\.t\d+\.id[a-z0-9]+$/.exec(m[1]) : null;
		if (!p || p[1] !== consentVersion) {
			return null;
		}
		return { analytics: p[2] === '1', marketing: p[3] === '1' };
	}

	function consentWrite(choice) {
		var id = '';
		try {
			var bytes = new Uint8Array(6);
			window.crypto.getRandomValues(bytes);
			for (var i = 0; i < bytes.length; i++) {
				id += ('0' + bytes[i].toString(16)).slice(-2);
			}
		} catch (err) {
			id = Math.random().toString(36).slice(2, 14);
		}
		var value = 'v' + consentVersion + '.a' + (choice.analytics ? 1 : 0) + '.m' + (choice.marketing ? 1 : 0) + '.t' + Math.floor(Date.now() / 1000) + '.id' + id;
		document.cookie = 'ea2000_consent=' + value + '; max-age=' + (365 * 24 * 60 * 60) + '; path=/; SameSite=Lax' + (window.location.protocol === 'https:' ? '; Secure' : '');
	}

	function consentClearCookies(pattern) {
		var host = window.location.hostname;
		var bare = host.replace(/^www\./, '');
		var domains = ['', host, '.' + bare];
		document.cookie.split(';').forEach(function (part) {
			var name = part.split('=')[0].trim();
			if (!pattern.test(name)) {
				return;
			}
			domains.forEach(function (domain) {
				document.cookie = name + '=; max-age=0; path=/' + (domain ? '; domain=' + domain : '');
			});
		});
	}

	function consentAdSignals(choice) {
		var ads = choice.marketing ? 'granted' : 'denied';
		return { analytics_storage: choice.analytics ? 'granted' : 'denied', ad_storage: ads, ad_user_data: ads, ad_personalization: ads };
	}

	function consentLoadAnalytics(choice) {
		if (consentLoaded.analytics || !consentCfg.ga) {
			return;
		}
		consentLoaded.analytics = true;
		window['ga-disable-' + consentCfg.ga] = false;
		window.dataLayer = window.dataLayer || [];
		window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
		window.gtag('consent', 'default', consentAdSignals(choice));
		window.gtag('js', new Date());
		window.gtag('config', consentCfg.ga);
		var tag = document.createElement('script');
		tag.async = true;
		tag.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(consentCfg.ga);
		document.head.appendChild(tag);
	}

	function consentLoadMarketing() {
		if (consentLoaded.marketing || !consentCfg.pixel) {
			return;
		}
		consentLoaded.marketing = true;
		!function (f, b, e, v, n, t, s) {
			if (f.fbq) return;
			n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
			if (!f._fbq) f._fbq = n;
			n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = [];
			t = b.createElement(e); t.async = !0; t.src = v;
			s = b.getElementsByTagName(e)[0];
			if (s && s.parentNode) { s.parentNode.insertBefore(t, s); } else { (b.head || b.documentElement).appendChild(t); }
		}(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
		window.fbq('consent', 'grant');
		window.fbq('init', consentCfg.pixel);
		window.fbq('track', 'PageView');
	}

	function consentApply(choice) {
		if (choice.analytics) {
			consentLoadAnalytics(choice);
		}
		if (choice.marketing) {
			consentLoadMarketing();
		}
		if (consentLoaded.analytics && typeof window.gtag === 'function') {
			window.gtag('consent', 'update', consentAdSignals(choice));
		}
	}

	function consentSave(choice) {
		choice = { analytics: consentTools.analytics && !!choice.analytics, marketing: consentTools.marketing && !!choice.marketing };
		var revoke = (consentLoaded.analytics && !choice.analytics) || (consentLoaded.marketing && !choice.marketing);
		consentWrite(choice);
		if (!choice.analytics) {
			if (consentCfg.ga) {
				window['ga-disable-' + consentCfg.ga] = true;
			}
			consentClearCookies(/^(_ga(_.*)?|_gid|_gat(_.*)?)$/);
		}
		if (!choice.marketing) {
			if (consentLoaded.marketing && typeof window.fbq === 'function') {
				window.fbq('consent', 'revoke');
			}
			consentClearCookies(/^(_fbp|_fbc)$/);
		}
		if (revoke) {
			if (typeof window.gtag === 'function') {
				window.gtag('consent', 'update', consentAdSignals(choice));
			}
			window.location.reload();
			return;
		}
		consentApply(choice);
		consentHide();
	}

	function consentView(view) {
		if (!consentBox) {
			return;
		}
		consentBox.classList.toggle('is-prefs', view === 'prefs');
		Array.prototype.forEach.call(consentBox.querySelectorAll('[data-show]'), function (el) {
			el.hidden = el.getAttribute('data-show') !== view;
		});
		Array.prototype.forEach.call(consentBox.querySelectorAll('[data-when]'), function (el) {
			el.hidden = el.getAttribute('data-when') === 'optional' ? !consentHasOptional : consentHasOptional;
		});
		Array.prototype.forEach.call(consentBox.querySelectorAll('[data-cat]'), function (el) {
			var cat = el.getAttribute('data-cat');
			el.hidden = !consentTools[cat];
			var input = el.querySelector('input');
			if (input) {
				var stored = consentRead();
				input.checked = !!(stored && stored[cat]);
			}
		});
	}

	function consentShow(view, focus) {
		if (!consentBox) {
			return;
		}
		consentView(view);
		consentBox.hidden = false;
		if (focus) {
			var title = consentBox.querySelector('.consent-title');
			if (title) {
				title.focus();
			}
		}
	}

	function consentHide() {
		if (!consentBox) {
			return;
		}
		consentBox.hidden = true;
		if (consentTrigger && document.contains(consentTrigger)) {
			consentTrigger.focus();
		}
		consentTrigger = null;
	}

	function consentDismissedThisVisit() {
		try { return window.sessionStorage.getItem('ea2000ConsentDismissed') === '1'; } catch (err) { return false; }
	}

	var consentStored = consentRead();
	if (consentStored) {
		consentApply(consentStored);
	}

	if (consentBox) {
		if (consentHasOptional && !consentStored && !consentDismissedThisVisit()) {
			consentShow('intro', false);
		}

		consentBox.addEventListener('click', function (e) {
			var btn = e.target && e.target.closest ? e.target.closest('[data-consent]') : null;
			if (!btn) {
				return;
			}
			var action = btn.getAttribute('data-consent');
			if (action === 'accept') {
				consentSave({ analytics: true, marketing: true });
			} else if (action === 'reject') {
				consentSave({ analytics: false, marketing: false });
			} else if (action === 'prefs') {
				consentView('prefs');
				var title = consentBox.querySelector('.consent-title');
				if (title) {
					title.focus();
				}
			} else if (action === 'save') {
				var picked = {};
				Array.prototype.forEach.call(consentBox.querySelectorAll('[data-cat] input'), function (input) {
					picked[input.name] = input.checked;
				});
				consentSave(picked);
			} else if (action === 'close') {
				if (!consentRead()) {
					try { window.sessionStorage.setItem('ea2000ConsentDismissed', '1'); } catch (err) {}
				}
				consentHide();
			}
		});

		consentBox.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				if (!consentRead()) {
					try { window.sessionStorage.setItem('ea2000ConsentDismissed', '1'); } catch (err) {}
				}
				consentHide();
			}
		});

		/* ลิงก์ "ตั้งค่าคุกกี้" ท้ายเว็บ ท้ายหน้า /go/ และในนโยบายความเป็นส่วนตัว */
		document.addEventListener('click', function (e) {
			var trigger = e.target && e.target.closest ? e.target.closest('a[href$="#cookie-settings"]') : null;
			if (!trigger) {
				return;
			}
			e.preventDefault();
			consentTrigger = trigger;
			consentShow('prefs', true);
		});

		if (window.location.hash === '#cookie-settings') {
			consentShow('prefs', true);
		}
	}
})();
