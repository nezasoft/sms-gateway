



! function(a) {
    var b = function(b, d) {
        if (this.element = a(b), this.format = c.parseFormat(d.format || this.element.data("date-format") || "mm/dd/yyyy"), this.picker = a(c.template).appendTo("body").on({
                click: a.proxy(this.click, this)
            }), this.isInput = this.element.is("input"), this.component = this.element.is(".date") ? this.element.find(".add-on") : !1, this.isInput ? this.element.on({
                focus: a.proxy(this.show, this),
                keyup: a.proxy(this.update, this)
            }) : this.component ? this.component.on("click", a.proxy(this.show, this)) : this.element.on("click", a.proxy(this.show, this)), this.minViewMode = d.minViewMode || this.element.data("date-minviewmode") || 0, "string" == typeof this.minViewMode) switch (this.minViewMode) {
            case "months":
                this.minViewMode = 1;
                break;
            case "years":
                this.minViewMode = 2;
                break;
            default:
                this.minViewMode = 0
        }
        if (this.viewMode = d.viewMode || this.element.data("date-viewmode") || 0, "string" == typeof this.viewMode) switch (this.viewMode) {
            case "months":
                this.viewMode = 1;
                break;
            case "years":
                this.viewMode = 2;
                break;
            default:
                this.viewMode = 0
        }
        this.startViewMode = this.viewMode, this.weekStart = d.weekStart || this.element.data("date-weekstart") || 0, this.weekEnd = 0 === this.weekStart ? 6 : this.weekStart - 1, this.onRender = d.onRender, this.fillDow(), this.fillMonths(), this.update(), this.showMode()
    };
    b.prototype = {
        constructor: b,
        show: function(b) {
            this.picker.show(), this.height = this.component ? this.component.outerHeight() : this.element.outerHeight(), this.place(), a(window).on("resize", a.proxy(this.place, this)), b && (b.stopPropagation(), b.preventDefault()), !this.isInput;
            var c = this;
            a(document).on("mousedown", function(b) {
                0 == a(b.target).closest(".bsdatepicker").length && c.hide()
            }), this.element.trigger({
                type: "show",
                date: this.date
            })
        },
        hide: function() {
            this.picker.hide(), a(window).off("resize", this.place), this.viewMode = this.startViewMode, this.showMode(), this.isInput || a(document).off("mousedown", this.hide), this.element.trigger({
                type: "hide",
                date: this.date
            })
        },
        set: function() {
            var a = c.formatDate(this.date, this.format);
            this.isInput ? this.element.prop("value", a) : (this.component && this.element.find("input").prop("value", a), this.element.data("date", a))
        },
        setValue: function(a) {
            "string" == typeof a ? this.date = c.parseDate(a, this.format) : this.date = new Date(a), this.set(), this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0), this.fill()
        },
        place: function() {
            var a = this.component ? this.component.offset() : this.element.offset();
            this.picker.css({
                top: a.top + this.height,
                left: a.left
            })
        },
        update: function(a) {
            this.date = c.parseDate("string" == typeof a ? a : this.isInput ? this.element.prop("value") : this.element.data("date"), this.format), this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0), this.fill()
        },
        fillDow: function() {
            for (var a = this.weekStart, b = "<tr>"; a < this.weekStart + 7;) b += '<th class="dow">' + c.dates.daysMin[a++ % 7] + "</th>";
            b += "</tr>", this.picker.find(".bsdatepicker-days thead").append(b)
        },
        fillMonths: function() {
            for (var a = "", b = 0; 12 > b;) a += '<span class="month">' + c.dates.monthsShort[b++] + "</span>";
            this.picker.find(".bsdatepicker-months td").append(a)
        },
        fill: function() {
            var a = new Date(this.viewDate),
                b = a.getFullYear(),
                d = a.getMonth(),
                e = this.date.valueOf();
            this.picker.find(".bsdatepicker-days th:eq(1)").text(c.dates.months[d] + " " + b);
            var f = new Date(b, d - 1, 28, 0, 0, 0, 0),
                g = c.getDaysInMonth(f.getFullYear(), f.getMonth());
            f.setDate(g), f.setDate(g - (f.getDay() - this.weekStart + 7) % 7);
            var h = new Date(f);
            h.setDate(h.getDate() + 42), h = h.valueOf();
            for (var i, j, k, l = []; f.valueOf() < h;) f.getDay() === this.weekStart && l.push("<tr>"), i = this.onRender(f), j = f.getFullYear(), k = f.getMonth(), d > k && j === b || b > j ? i += " old" : (k > d && j === b || j > b) && (i += " new"), f.valueOf() === e && (i += " active"), l.push('<td class="day ' + i + '">' + f.getDate() + "</td>"), f.getDay() === this.weekEnd && l.push("</tr>"), f.setDate(f.getDate() + 1);
            this.picker.find(".bsdatepicker-days tbody").empty().append(l.join(""));
            var m = this.date.getFullYear(),
                n = this.picker.find(".bsdatepicker-months").find("th:eq(1)").text(b).end().find("span").removeClass("active");
            m === b && n.eq(this.date.getMonth()).addClass("active"), l = "", b = 10 * parseInt(b / 10, 10);
            var o = this.picker.find(".bsdatepicker-years").find("th:eq(1)").text(b + "-" + (b + 9)).end().find("td");
            b -= 1;
            for (var p = -1; 11 > p; p++) l += '<span class="year' + (-1 === p || 10 === p ? " old" : "") + (m === b ? " active" : "") + '">' + b + "</span>", b += 1;
            o.html(l)
        },
        click: function(b) {
            b.stopPropagation(), b.preventDefault();
            var d = a(b.target).closest("span, td, th");
            if (1 === d.length) switch (d[0].nodeName.toLowerCase()) {
                case "th":
                    switch (d[0].className) {
                        case "switch":
                            this.showMode(1);
                            break;
                        case "prev":
                        case "next":
                            this.viewDate["set" + c.modes[this.viewMode].navFnc].call(this.viewDate, this.viewDate["get" + c.modes[this.viewMode].navFnc].call(this.viewDate) + c.modes[this.viewMode].navStep * ("prev" === d[0].className ? -1 : 1)), this.fill(), this.set()
                    }
                    break;
                case "span":
                    if (d.is(".month")) {
                        var e = d.parent().find("span").index(d);
                        this.viewDate.setMonth(e)
                    } else {
                        var f = parseInt(d.text(), 10) || 0;
                        this.viewDate.setFullYear(f)
                    }
                    0 !== this.viewMode && (this.date = new Date(this.viewDate), this.element.trigger({
                        type: "changeDate",
                        date: this.date,
                        viewMode: c.modes[this.viewMode].clsName
                    })), this.showMode(-1), this.fill(), this.set();
                    break;
                case "td":
                    if (d.is(".day") && !d.is(".disabled")) {
                        var g = parseInt(d.text(), 10) || 1,
                            e = this.viewDate.getMonth();
                        d.is(".old") ? e -= 1 : d.is(".new") && (e += 1);
                        var f = this.viewDate.getFullYear();
                        this.date = new Date(f, e, g, 0, 0, 0, 0), this.viewDate = new Date(f, e, Math.min(28, g), 0, 0, 0, 0), this.fill(), this.set(), this.element.trigger({
                            type: "changeDate",
                            date: this.date,
                            viewMode: c.modes[this.viewMode].clsName
                        })
                    }
            }
        },
        mousedown: function(a) {
            a.stopPropagation(), a.preventDefault()
        },
        showMode: function(a) {
            a && (this.viewMode = Math.max(this.minViewMode, Math.min(2, this.viewMode + a))), this.picker.find(">div").hide().filter(".bsdatepicker-" + c.modes[this.viewMode].clsName).show()
        }
    }, a.fn.bsdatepicker = function(c, d) {
        return this.each(function() {
            var e = a(this),
                f = e.data("bsdatepicker"),
                g = "object" == typeof c && c;
            f || e.data("bsdatepicker", f = new b(this, a.extend({}, a.fn.bsdatepicker.defaults, g))), "string" == typeof c && f[c](d)
        })
    }, a.fn.bsdatepicker.defaults = {
        onRender: function(a) {
            return ""
        }
    }, a.fn.bsdatepicker.Constructor = b;
    var c = {
        modes: [{
            clsName: "days",
            navFnc: "Month",
            navStep: 1
        }, {
            clsName: "months",
            navFnc: "FullYear",
            navStep: 1
        }, {
            clsName: "years",
            navFnc: "FullYear",
            navStep: 10
        }],
        dates: {
            days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
            months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
        },
        isLeapYear: function(a) {
            return a % 4 === 0 && a % 100 !== 0 || a % 400 === 0
        },
        getDaysInMonth: function(a, b) {
            return [31, c.isLeapYear(a) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][b]
        },
        parseFormat: function(a) {
            var b = a.match(/[.\/\-\s].*?/),
                c = a.split(/\W+/);
            if (!b || !c || 0 === c.length) throw new Error("Invalid date format.");
            return {
                separator: b,
                parts: c
            }
        },
        parseDate: function(a, b) {
            var c, d = a.split(b.separator),
                a = new Date;
            if (a.setHours(0), a.setMinutes(0), a.setSeconds(0), a.setMilliseconds(0), d.length === b.parts.length) {
                for (var e = a.getFullYear(), f = a.getDate(), g = a.getMonth(), h = 0, i = b.parts.length; i > h; h++) switch (c = parseInt(d[h], 10) || 1, b.parts[h]) {
                    case "dd":
                    case "d":
                        f = c, a.setDate(c);
                        break;
                    case "mm":
                    case "m":
                        g = c - 1, a.setMonth(c - 1);
                        break;
                    case "yy":
                        e = 2e3 + c, a.setFullYear(2e3 + c);
                        break;
                    case "yyyy":
                        e = c, a.setFullYear(c)
                }
                a = new Date(e, g, f, 0, 0, 0)
            }
            return a
        },
        formatDate: function(a, b) {
            var c = {
                d: a.getDate(),
                m: a.getMonth() + 1,
                yy: a.getFullYear().toString().substring(2),
                yyyy: a.getFullYear()
            };
            c.dd = (c.d < 10 ? "0" : "") + c.d, c.mm = (c.m < 10 ? "0" : "") + c.m;
            for (var a = [], d = 0, e = b.parts.length; e > d; d++) a.push(c[b.parts[d]]);
            return a.join(b.separator)
        },
        headTemplate: '<thead><tr><th class="prev">&lsaquo;</th><th colspan="5" class="switch"></th><th class="next">&rsaquo;</th></tr></thead>',
        contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>'
    };
    c.template = '<div class="bsdatepicker dropdown-menu"><div class="bsdatepicker-days"><table class=" table-condensed">' + c.headTemplate + '<tbody></tbody></table></div><div class="bsdatepicker-months"><table class="table-condensed">' + c.headTemplate + c.contTemplate + '</table></div><div class="bsdatepicker-years"><table class="table-condensed">' + c.headTemplate + c.contTemplate + "</table></div></div>"
}(window.jQuery);
