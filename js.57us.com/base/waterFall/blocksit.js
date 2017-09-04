(function(n) {
	var e = {
		numOfCol: 5,
		offsetX: 5,
		offsetY: 5,
		blockElement: "div"
	},
		h, p, f = [];
	Array.prototype.indexOf || (Array.prototype.indexOf = function(c, d) {
		var b = this.length >>> 0,
			a = Number(d) || 0,
			a = 0 > a ? Math.ceil(a) : Math.floor(a);
		for (0 > a && (a += b); a < b; a++) if (a in this && this[a] === c) return a;
		return -1
	});
	var u = function() {
			f = [];
			for (var c = 0; c < e.numOfCol; c++) r("empty-" + c, c, 0, 1, -e.offsetY)
		},
		r = function(c, d, b, a, v) {
			for (c = 0; c < a; c++) {
				var g = {};
				g.x = d + c;
				g.size = a;
				g.endY = b + v + 2 * e.offsetY;
				f.push(g)
			}
		},
		t = function(c, d) {
			for (var b = 0; b < f.length; b++) {
				var a = f[b];
				if ("x" == d && a.x == c || "endY" == d && a.endY == c) return b
			}
		},
		q = function(c, d) {
			for (var b = [], a = 0; a < d; a++) b.push(f[t(c + a, "x")].endY);
			var a = Math.min.apply(Math, b),
				e = Math.max.apply(Math, b);
			return [a, e, b.indexOf(a)]
		};
	n.fn.BlocksIt = function(c) {
		c && "object" === typeof c && n.extend(e, c);
		h = n(this);
		p = h.width() / e.numOfCol;
		u();
		h.children(e.blockElement).each(function(d) {
			d = n(this);
			!d.data("size") || 0 > d.data("size") ? d.data("size", 1) : d.data("size") > e.numOfCol && d.data("size", e.numOfCol);
			var b, a = d.data("size");
			if (1 < a) {
				for (var c = f.length - a, g = !1, k, l = 0; l < f.length; l++) {
					var m = f[l].x;
					if (0 <= m && m <= c) {
						var h = q(m, a);
						g ? h[1] < b[1] && (b = h, k = m) : (g = !0, b = h, k = m)
					}
				}
				b = [k, b[1]]
			} else b = q(0, f.length), b = [b[2], b[0]];
			k = p * d.data("size") - (d.outerWidth() - d.width());
			d.css({
				width: k - 2 * e.offsetX,
				left: b[0] * p,
				top: b[1],
				position: "absolute"
			});
			k = d.outerHeight();
			a = b[0];
			c = d.data("size");
			for (g = 0; g < c; g++) l = t(a + g, "x"), f.splice(l, 1);
			r(d.attr("id"), b[0], b[1], d.data("size"), k)
		});
		c = q(0, f.length);
		h.height(c[1] + e.offsetY);
		return this
	}
})(jQuery);