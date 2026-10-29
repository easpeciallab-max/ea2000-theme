/*! EA2000 · เครื่องคำนวณขนาดออเดอร์
 * คำนวณในเบราว์เซอร์ทั้งหมด ไม่ส่งค่าที่ผู้ใช้กรอกออกไปที่ใด
 * ผลลัพธ์เป็นการคำนวณเชิงกลไกจากค่าที่กรอก ไม่ใช่คำแนะนำว่าควรใช้ขนาดเท่าไร
 */
(function () {
	var form = document.getElementById('lot-form');
	if (!form) {
		return;
	}

	var el = {
		balance: document.getElementById('lot-balance'),
		risk: document.getElementById('lot-risk'),
		stop: document.getElementById('lot-stop'),
		value: document.getElementById('lot-value'),
		result: document.getElementById('lot-result'),
		money: document.getElementById('lot-money'),
		out: document.getElementById('lot-out')
	};
	for (var k in el) {
		if (!el[k]) {
			return;
		}
	}

	var money = el.money.getAttribute('data-template') || '';

	function num(input) {
		var v = parseFloat(String(input.value).replace(/,/g, ''));
		return isFinite(v) && v >= 0 ? v : 0;
	}

	function fmt(n, digits) {
		return n.toLocaleString('en-US', { minimumFractionDigits: digits, maximumFractionDigits: digits });
	}

	function calc() {
		var balance = num(el.balance);
		var riskPct = num(el.risk);
		var stop = num(el.stop);
		var perPoint = num(el.value);

		var riskMoney = balance * riskPct / 100;
		var ok = stop > 0 && perPoint > 0 && riskMoney > 0;
		var lots = ok ? riskMoney / (stop * perPoint) : 0;

		// ปัดลงเป็นช่วง 0.01 เสมอ เพื่อไม่ให้ผลลัพธ์เสี่ยงเกินที่กรอกไว้
		lots = Math.floor(lots * 100) / 100;

		el.result.textContent = ok ? fmt(lots, 2) + ' lot' : '-';
		el.money.textContent = ok
			? money.replace('{amount}', fmt(riskMoney, 2))
			: '';
		el.out.classList.toggle('is-zero', ok && lots < 0.01);
	}

	form.addEventListener('input', calc);
	form.addEventListener('change', calc);
	form.addEventListener('submit', function (e) { e.preventDefault(); calc(); });
	calc();
})();
