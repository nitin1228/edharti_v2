// Minified by Diwakar Sinha at 30-12-2024
document.addEventListener("DOMContentLoaded", (function() {
    var e = document.getElementById("aadharnumber");
    e && e.addEventListener("input", (function(e) {
        var t = e.target.value.replace(/\D/g, "");
        e.target.value = t
    }))
}));

// Minified by Diwakar Sinha at 30-12-2024
const transferredCheckboxYes = document.getElementById("transferredFormYes"),
    transferredContainer = document.getElementById("transferredContainer");
transferredCheckboxYes.addEventListener("change", (function() {
    this.checked ? transferredContainer.style.display = "block" : transferredContainer.style.display = "none"
}));

// Free Hold
// Minified by Diwakar Sinha at 30-12-2024
var freeHoldCheckboxYes = document.getElementById("freeHoldFormYes"),
    freeHoldCheckboxNo = document.getElementById("freeHoldFormNo");
$(document).on("change", 'input[id^="freeHoldFormYes"]', (function() {
    var e = $(this).attr("id"),
        o = parseInt(e.replace("freeHoldFormYes", ""), 10);
    isNaN(o) && (o = 0);
    var r = 0 == o ? $("#freeHoldContainer") : $("#freeHoldContainer" + o);
    $(this).is(":checked") ? r.show() : r.hide()
}));

// Minified by Diwakar Sinha at 30-12-2024
$(document).on("change", 'input[id^="freeHoldFormNo"]', (function() {
    var e = $(this).attr("id"),
        o = parseInt(e.replace("freeHoldFormNo", ""), 10);
    isNaN(o) && (o = 0);
    var r = 0 == o ? $("#freeHoldContainer") : $("#freeHoldContainer" + o);
    $(this).is(":checked") ? r.hide() : r.show()
}));

// Land Type: Vacant
// Minified by Diwakar Sinha at 30-12-2024
var landTypeCheckboxYes = document.getElementById("landTypeFormYes"),
    landTypeCheckboxNo = document.getElementById("landTypeFormNo");
$(document).on("change", 'input[id^="landTypeFormYes"]', (function() {
    var e = $(this).attr("id"),
        n = parseInt(e.replace("landTypeFormYes", ""), 10);
    isNaN(n) && (n = 0);
    var a = 0 == n ? $("#landTypeContainer") : $("#landTypeContainer" + n);
    $(this).is(":checked") ? a.show() : a.hide()
}));

// Minified by Diwakar Sinha at 30-12-2024
$(document).on("change", 'input[id^="landTypeFormNo"]', (function() {
    var n = $(this).attr("id"),
        e = parseInt(n.replace("landTypeFormNo", ""), 10);
    isNaN(e) && (e = 0);
    var a = 0 == e ? $("#landTypeContainer") : $("#landTypeContainer" + e);
    $(this).is(":checked") ? a.hide() : a.show()
}));

// Land Type : Others
// Minified by Diwakar Sinha at 30-12-2024
var landTypeOthersCheckboxYes = document.getElementById("landTypeFormOthersYes"),
    landTypeOthersCheckboxNo = document.getElementById("landTypeFormOthersNo");
$(document).on("change", 'input[id^="landTypeFormOthersYes"]', (function() {
    var e = $(this).attr("id"),
        t = parseInt(e.replace("landTypeFormOthersYes", ""), 10);
    isNaN(t) && (t = 0);
    var n = 0 == t ? $("#landTypeOthersContainer") : $("#landTypeOthersContainer" + t);
    $(this).is(":checked") ? n.show() : n.hide()
}));

// Minified by Diwakar Sinha at 30-12-2024
$(document).on("change", 'input[id^="landTypeFormOthersNo"]', (function() {
    var e = $(this).attr("id"),
        n = parseInt(e.replace("landTypeFormOthersNo", ""), 10);
    isNaN(n) && (n = 0);
    var t = 0 == n ? $("#landTypeOthersContainer") : $("#landTypeOthersContainer" + n);
    $(this).is(":checked") ? t.hide() : t.show()
}));

// GR
// Minified by Diwakar Sinha at 30-12-2024
const GRCheckboxYes = document.getElementById("GRFormYes"),
    GRCheckboxNo = document.getElementById("GRFormNo"),
    GRContainer = document.getElementById("GRContainer");
GRCheckboxYes.addEventListener("change", (function() {
    this.checked ? GRContainer.style.display = "block" : GRContainer.style.display = "none"
})), GRCheckboxNo.addEventListener("change", (function() {
    this.checked ? GRContainer.style.display = "none" : GRContainer.style.display = "block"
}));

// Supplementary
// Minified by Diwakar Sinha at 30-12-2024
const SupplementaryCheckboxYes = document.getElementById("SupplementaryFormYes"),
    SupplementaryCheckboxNo = document.getElementById("SupplementaryFormNo"),
    SupplementaryContainer = document.getElementById("SupplementaryContainer");
SupplementaryCheckboxYes.addEventListener("change", (function() {
    this.checked ? SupplementaryContainer.style.display = "block" : SupplementaryContainer.style.display = "none"
})), SupplementaryCheckboxNo.addEventListener("change", (function() {
    this.checked ? SupplementaryContainer.style.display = "none" : SupplementaryContainer.style.display = "block"
}));

// Re-Entered
// Minified by Diwakar Sinha at 30-12-2024
const ReenteredCheckboxYes = document.getElementById("ReenteredFormYes"),
    ReenteredCheckboxNo = document.getElementById("ReenteredFormNo"),
    ReenteredContainer = document.getElementById("ReenteredContainer");
ReenteredCheckboxYes.addEventListener("change", (function() {
    this.checked ? ReenteredContainer.style.display = "block" : ReenteredContainer.style.display = "none"
})), ReenteredCheckboxNo.addEventListener("change", (function() {
    this.checked ? ReenteredContainer.style.display = "none" : ReenteredContainer.style.display = "block"
}));

// MIS Form Vaidation
//************************************************************************************************************************************** */
// Minified by Diwakar Sinha at 30-12-2024
document.addEventListener("DOMContentLoaded", (function() {
    var e = document.getElementById("test-vl-1"),
        t = document.getElementById("test-vl-2"),
        n = (document.getElementById("test-vl-3"), document.getElementById("test-vl-4"), document.getElementById("test-vl-5")),
        r = (document.getElementById("test-vl-6"), document.getElementById("test-vl-7"), document.getElementById("FileNumber")),
        d = document.getElementById("colonyName"),
        l = document.getElementById("ColonyNameOld"),
        o = document.getElementById("PropertyStatus"),
        i = document.getElementById("LandType"),
        s = document.getElementById("FileNumberError"),
        u = document.getElementById("PresentColonyNameError"),
        m = document.getElementById("OldColonyNameError"),
        a = document.getElementById("PropertyStatusError"),
        y = document.getElementById("LandTypeError");
    var c = document.getElementById("TypeLease"),
        p = document.getElementById("dateexecution"),
        E = document.getElementById("lease_exp_duration"),
        g = document.getElementById("plotno"),
        B = document.getElementById("areaunitname"),
        v = document.getElementById("selectareaunit"),
        I = document.getElementById("premiumunit1"),
        b = document.getElementById("premiumunit2"),
        f = (document.getElementById("selectpremiumunit"), document.getElementById("groundRent1")),
        x = document.getElementById("groundRent2"),
        C = (document.getElementById("selectGroundRentUnit"), document.getElementById("startdateGR")),
        h = document.getElementById("RGRduration"),
        k = document.getElementById("frevisiondateGR"),
        q = document.getElementById("oldPropertyType"),
        L = document.getElementById("oldPropertySubType"),
        R = document.getElementById("propertyType"),
        T = document.getElementById("propertySubType"),
        D = document.getElementById("TypeLeaseError"),
        P = document.getElementById("dateexecutionError"),
        G = document.getElementById("leaseExpDurationError"),
        A = document.getElementById("dateallotmentError"),
        F = document.getElementById("plotnoError"),
        N = document.getElementById("selectareaunitError"),
        S = document.getElementById("premiumunit2Error"),
        w = (document.getElementById("selectGroundRentUnitError"), document.getElementById("startdateGRError")),
        _ = document.getElementById("RGRdurationError"),
        O = document.getElementById("frevisiondateGRError"),
        U = document.getElementById("oldPropertyTypeError"),
        M = document.getElementById("oldPropertySubTypeError"),
        j = document.getElementById("propertyTypeError"),
        z = document.getElementById("propertySubTypeError");
    document.getElementById("ProcessTransfer"), document.getElementById("transferredDate"), document.getElementById("name"), document.getElementById("age"), document.getElementById("share"), document.getElementById("pannumber"), document.getElementById("aadharnumber"), document.getElementById("ProcessTransferError"), document.getElementById("transferredDateError"), document.getElementById("nameError"), document.getElementById("ageError"), document.getElementById("shareError"), document.getElementById("pannumberError"), document.getElementById("aadharnumberError"), document.getElementById("LastDemandLetter"), document.getElementById("amountDemandLetter"), document.getElementById("LastAmount"), document.getElementById("lastamountdate"), document.getElementById("LastDemandLetterError"), document.getElementById("amountDemandLetterError"), document.getElementById("LastAmountError"), document.getElementById("lastamountdateError");
    var H = document.getElementById("address"),
        J = document.getElementById("asondate"),
        K = document.getElementById("addressError"),
        Q = document.getElementById("asondateError");

    function V() {
        var e = "" === r.value.trim() ? (s.textContent = "File Number is required", s.style.display = "block", !1) : (s.style.display = "none", !0),
            t = "" === d.value.trim() ? (u.textContent = "Present Colony Name is required", u.style.display = "block", !1) : (u.style.display = "none", !0),
            n = "" === l.value.trim() ? (m.textContent = "Old Colony Name is required", m.style.display = "block", !1) : (m.style.display = "none", !0),
            c = "" === o.value.trim() ? (a.textContent = "Property Status is required", a.style.display = "block", !1) : (a.style.display = "none", !0),
            p = "" === i.value.trim() ? (y.textContent = "Land Type is required", y.style.display = "block", !1) : (y.style.display = "none", !0);
        return e && t && n && c && p
    }

    function W() {
        var e = "" === c.value.trim() ? (D.textContent = "Type Lease is required", D.style.display = "block", !1) : (D.style.display = "none", !0),
            t = function() {
                if (p) return "" === p.value.trim() ? (P.textContent = "Date Execution is required", P.style.display = "block", !1) : (P.style.display = "none", !0)
            }(),
            n = !E || ("" === E.value.trim() ? (G.textContent = "Duration is required", G.style.display = "block", !1) : (G.style.display = "none", !0)),
            r = "" === dateallotment.value.trim() ? (A.textContent = "Date Allotment is required", A.style.display = "block", !1) : (A.style.display = "none", !0),
            d = "" === g.value.trim() ? (F.textContent = "Plot Number is required", F.style.display = "block", !1) : (F.style.display = "none", !0),
            l = "" === B.value.trim() ? (N.textContent = "Area & Unit is required", N.style.display = "block", !1) : (N.style.display = "none", !0),
            o = "" === v.value.trim() ? (N.textContent = "Area & Unit is required", N.style.display = "block", !1) : (N.style.display = "none", !0),
            i = "" === I.value.trim() ? (S.textContent = "Premium is required", S.style.display = "block", !1) : (S.style.display = "none", !0),
            s = "" === b.value.trim() ? (S.textContent = "Premium is required", S.style.display = "block", !1) : (S.style.display = "none", !0),
            u = "" === f.value.trim() ? (groundRent2Error.textContent = "Ground Rent is required", groundRent2Error.style.display = "block", !1) : (groundRent2Error.style.display = "none", !0),
            m = "" === x.value.trim() ? (groundRent2Error.textContent = "Ground Rent is required", groundRent2Error.style.display = "block", !1) : (groundRent2Error.style.display = "none", !0),
            a = "" === C.value.trim() ? (w.textContent = "Start Date GR is required", w.style.display = "block", !1) : (w.style.display = "none", !0),
            y = "" === h.value.trim() ? (_.textContent = "RGR Duration is required", _.style.display = "block", !1) : (_.style.display = "none", !0),
            R = "" === k.value.trim() ? (O.textContent = "First Revision Date is required", O.style.display = "block", !1) : (O.style.display = "none", !0),
            $ = "" === q.value.trim() ? (U.textContent = "Purpose for which leased/ allotted (As per lease) is required", U.style.display = "block", !1) : (U.style.display = "none", !0),
            T = "" === L.value.trim() ? (M.textContent = "Sub-Type (Purpose , at present) is required", M.style.display = "block", !1) : (M.style.display = "none", !0);
        return console.log(e, t, n, r, d, l, o, i, s, u, m, a, y, R, $, T), p ? e && t && n && r && d && l && o && i && s && u && m && a && y && R && $ && T : e && r && d && l && o && i && s && u && m && a && y && R && $ && T
    }

    function X() {
        var e = "" === R.value.trim() ? (j.textContent = "Purpose for which leased/ allotted (At present) is required", j.style.display = "block", !1) : (j.style.display = "none", !0),
            t = "" === T.value.trim() ? (z.textContent = "Sub-Type (Purpose , at present) is required", z.style.display = "block", !1) : (z.style.display = "none", !0);
        return e && t
    }

    function Y() {
        var e = "" === H.value.trim() ? (K.textContent = "Address is required", K.style.display = "block", !1) : (K.style.display = "none", !0),
            t = "" === J.value.trim() ? (Q.textContent = "As on Date is required", Q.style.display = "block", !1) : (Q.style.display = "none", !0);
        return e && t
    }

    function Z() {
        var e = !0;
        $('input[id^="original"]').removeClass("invalid"), $('input[id^="original"]').parent().find(".text-danger").html("");
        $('input[id^="in_favor_new"]').removeClass("invalid"), $('input[id^="in_favor_new"]').parent().find(".text-danger").html("");
        $('input[id^="original"]').each((function() {
            "" === $(this).val().trim() ? ($(this).addClass("invalid"), $(this).parent().find(".text-danger").html("This field is required"), e = !1) : 0
        })), $('input[id^="in_favor_new"]').each((function() {
            "" === $(this).val().trim() ? ($(this).addClass("invalid"), $(this).parent().find(".text-danger").html("This field is required"), e = !1) : 0
        })), e && stepper3.next()
    }
    e.addEventListener("button", (function(e) {
        e.preventDefault(), V() && alert("Form submitted successfully")
    })), t.addEventListener("button", (function(e) {
        e.preventDefault(), W() && alert("Form submitted successfully")
    })), n.addEventListener("button", (function(e) {
        e.preventDefault(), validateForm5() && alert("Form submitted successfully")
    })), document.getElementById("submitButton1").addEventListener("click", (function() {
        V() && stepper3.next()
    })), document.getElementById("submitButton2").addEventListener("click", (function() {
        document.getElementById("landusechange").checked ? X() && Z() && stepper3.next() : W() && Z() && stepper3.next()
    })),document.getElementById("submitButton4").addEventListener("click", function () {
    var e = true;

    // If YES → do validation
    if ($('#freeHoldFormYes').is(":checked")) {
        $('input[id^="conversion"]').removeClass("invalid")
            .parent().find(".text-danger").html("");
        $('input[id^="newInFavourConversion"]').removeClass("invalid")
            .parent().find(".text-danger").html("");
        $('#ConveyanceDate').removeClass("invalid")
            .parent().find(".text-danger").html("");
        $('input[id^="conversion"]').each(function () {
            if ($(this).val().trim() === "") {
                $(this).addClass("invalid");
                $(this).parent().find(".text-danger").html("This field is required");
                e = false;
            }
        });
        $('input[id^="newInFavourConversion"]').each(function () {
            if ($(this).val().trim() === "") {
                $(this).addClass("invalid");
                $(this).parent().find(".text-danger").html("This field is required");
                e = false;
            }
        });
        if ($('#ConveyanceDate').val().trim() === "") {
            $('#ConveyanceDate').addClass("invalid");
            $('#ConveyanceDate').parent().find(".text-danger").html("This field is required");
            e = false;
        }
    }     else {      
        e = true;
    }
    if (e) stepper3.next();
}), 
    document.getElementById("submitButton5").addEventListener("click", (function() {
        stepper3.next()
    })), 
document.getElementById("submitButton6").addEventListener("click", function () {
    let valid = true;
    function validateGroup(yesPrefix, datePrefix) {
        $('input[id^="' + yesPrefix + '"]').each(function (i) {
            let selector = (i === 0)
                ? '#' + datePrefix
                : '#' + datePrefix + i;
            if ($(this).is(":checked")) {
                $(selector).each(function () {

                    if ($(this).val().trim() === "") {
                        $(this).addClass("invalid");
                        $(this).parent().find(".text-danger").html("This field is required");
                        valid = false;
                    } else {
                        $(this).removeClass("invalid");
                        $(this).parent().find(".text-danger").html("");
                    }
                });
            }
        });
    }
    validateGroup("GRFormYes", "GRrevisedDate");
    validateGroup("SupplementaryFormYes", "SupplementaryDate");
    validateGroup("ReenteredFormYes", "reentryDate");
    if (valid) stepper3.next();
}),
    document.getElementById("btnfinalsubmit").addEventListener("click", (function() {
        Y() ? this.removeAttribute("type", "submit") : this.setAttribute("type", "button")
    }))
}));