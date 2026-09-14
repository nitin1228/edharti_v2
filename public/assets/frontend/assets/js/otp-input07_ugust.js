// Minified by Diwakar Sinha at 01-01-2025
document.addEventListener("DOMContentLoaded", () => {
  const e = (e, t) => {
    const n = document.getElementById(e),
      a = [...n.querySelectorAll("input[type=text]")],
      o = n.querySelector(t),
      r = (e) => {
        const t = a.indexOf(e.target);
        /^[0-9]{1}$/.test(e.key) ||
          "Backspace" === e.key ||
          "Delete" === e.key ||
          "Tab" === e.key ||
          e.metaKey ||
          e.preventDefault(),
          ("Delete" === e.key || "Backspace" === e.key) &&
            t >= 0 &&
            ("" === a[t].value
              ? t > 0 && (a[t - 1].focus(), (a[t - 1].value = ""))
              : (a[t].value = ""),
            e.preventDefault());
      },
      l = (e) => {
        const { target: t } = e,
          n = a.indexOf(t);
        t.value && n < a.length - 1
          ? a[n + 1].focus()
          : n === a.length - 1 && o.focus();
      },
      c = (e) => {
        e.target.select();
      },
      s = (e) => {
        e.preventDefault();
        const t = e.clipboardData.getData("text");
        if (!/^[0-9]{1,}$/.test(t)) return;
        const n = t.split("").slice(0, a.length);
        a.forEach((e, t) => (e.value = n[t] || "")),
          n.length === a.length && o.focus();
      };
    a.forEach((e) => {
      e.addEventListener("input", l),
        e.addEventListener("keydown", r),
        e.addEventListener("focus", c),
        e.addEventListener("paste", s);
    });
  };
  e("otp-form", "#verifyMobileOtpBtn"),
    e("otp-form-email", "#verifyEmailOtpBtn"),
    e("org-otp-form", "#orgVerifyMobileOtpBtn"),
    e("org-otp-form-email", "#orgVerifyEmailOtpBtn");
});

// Field Data Validation in All Inputs - 31-07-2024 by Diwakar Sinha
// Minified by Diwakar Sinha at 01-01-2025
$(document).ready(function () {
  $(".alpha-only").keypress(function (a) {
    var t = a.which;
    (t < 65 || (t > 90 && t < 97) || t > 122) &&
      32 !== t &&
      46 !== t &&
      a.preventDefault();
  }),
    $(".numericDecimal").on("input", function () {
      var a = $(this).val();
      /^\d*\.?\d*$/.test(a) || $(this).val(a.slice(0, -1));
    }),
    $(".numericOnly").on("input", function (a) {
      $(this).val(
        $(this)
          .val()
          .replace(/[^0-9]/g, "")
      );
    }),
    $(".alphaNum-hiphenForwardSlash").on("input", function () {
      var a = $(this)
        .val()
        .replace(/[^a-zA-Z0-9\-\/]/g, "");
      $(this).val(a);
    }),
    $(".date_format").on("input", function (a) {
      var t = $(this).val().replace(/\D/g, "");
      t.length > 8 && (t = t.substring(0, 8));
      var n = "";
      t.length > 0 && (n = t.substring(0, 2)),
        t.length >= 3 && (n += "-" + t.substring(2, 4)),
        t.length >= 5 && (n += "-" + t.substring(4, 8)),
        $(this).val(n);
    }),
    $(".plotNoAlpaMix").on("input", function () {
      var a = $(this)
        .val()
        .replace(/[^a-zA-Z0-9+\-/]/g, "");
      $(this).val(a);
    }),
    $(".alphaNum-hiphenForwardSlash").on("input", function () {
      var a = $(this)
        .val()
        .replace(/[^a-zA-Z0-9 \/\(\)\-]/g, "");
      $(this).val(a);
    }),
    $(".alphaNumHypSlashParenthspace").on("input", function () {
      var a = $(this)
        .val()
        .replace(/[^a-zA-Z0-9 \/\(\)\-]/g, "");
      $(this).val(a);
    }),
    $(".pan_number_format").on("input", function (a) {
      for (
        var t = $(this).val().toUpperCase(), n = "", i = 0;
        i < t.length;
        i++
      ) {
        var e = t[i];
        if (i < 5) {
          if (!/[A-Z]/.test(e)) {
            !1;
            break;
          }
          n += e;
        } else if (i >= 5 && i < 9) {
          if (!/[0-9]/.test(e)) {
            !1;
            break;
          }
          n += e;
        } else if (9 === i) {
          if (!/[A-Z]/.test(e)) {
            !1;
            break;
          }
          n += e;
        }
      }
      t.length, $(this).val(n);
    });
});
// Required Validation - 27-09-2024 by Diwakar Sinha
// This is working final for Individual Owner
// Minified by Diwakar Sinha at 01-01-2025
$(document).ready(function () {
  const placeholderSrc = "/assets/images/image-placeholder.jpg";
  // added by anil new fucntion on 05-06-2025
  function validateImageUpload(
    inputSelector,
    errorSelector,
    previewSelector,
    isSubmit = false
  ) {
    const input = $(inputSelector)[0];
    const errorEl = $(errorSelector);
    const previewImg = $(previewSelector);
    const file = input?.files?.[0];

    const allowedTypes = ["image/jpeg", "image/png"];
    const maxSize = 100 * 1024; // 100KB

    // Empty input case
    if (!file) {
      const msg = isSubmit
        ? "This field is required"
        : "Please select an image.";
      errorEl.text(msg).show();
      previewImg.attr("src", placeholderSrc).show();
      return false;
    }

    // Invalid type
    if (!allowedTypes.includes(file.type)) {
      errorEl.text("Only JPG and PNG images are allowed.").show();
      previewImg.attr("src", placeholderSrc).show();
      return false;
    }

    // Too large
    if (file.size > maxSize) {
      errorEl.text("Image size must be less than 100KB.").show();

      // Still show preview
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImg.attr("src", e.target.result).show();
      };
      reader.readAsDataURL(file);

      return false;
    }

    // ✅ Valid image
    errorEl.text("").hide();

    const reader = new FileReader();
    reader.onload = function (e) {
      previewImg.attr("src", e.target.result).show();
    };
    reader.readAsDataURL(file);

    return true;
  }

  // ✅ Instant preview & validation on change
  $("#file-input").on("change", function () {
    validateImageUpload("#file-input", "#file-inputError", "#img-preview");
  }); // end added by anil new fucntion on 05-06-2025

  var e = $(".dynamicForm");
  function r() {
    $("#propertyowner").is(":checked")
      ? e.attr("id", "propertyOwnerForm")
      : $("#organization").is(":checked") && e.attr("id", "organizationForm");
  }
    function d() {
    if ($("#Yes").is(":checked")) {
      $("#ifyes").show();
      $("#ifYesNotChecked").hide();
      $(".isPropertyDetailsRecordNotFoundUnChecked").addClass("hide-sec");
      $(".isPropertyDetailsNotFoundUnChecked").addClass("hide-sec");
    } else {
      $("#ifyes").hide();
      $("#ifYesNotChecked").show();
      $(".isPropertyDetailsRecordNotFoundUnChecked").removeClass("hide-sec");
      $(".isPropertyDetailsNotFoundUnChecked").removeClass("hide-sec");
    }
  }
  $("#propertyowner, #organization").change(function () {
    r();
  }),
    $("#IndsubmitButton").click(async function (d) {
      d.preventDefault(), r();

      let formIsValid = true;

      // 🟡 Run image validation
      const imageValid = validateImageUpload(
        "#file-input",
        "#file-inputError",
        "#img-preview",
        true // submit flag
      );

      const captchaIsValid = await captchaValidation("invRegisterCaptcha");
      if (!captchaIsValid) formIsValid = false;

      if (!imageValid) formIsValid = false;
      // end added by anil new fucntion on 05-06-2025

      var l = e.attr("id"),
        i = $("#" + l);
      (function () {
        let e = null,
          r = !0;
        [
          { id: "#indfullname", errorId: "#IndFullNameError" },
          { id: "#Indgender", errorId: "#IndGenderError" },
          { id: "#dateOfBirth", errorId: "#dateOfBirthError" },
          { id: "#mobileInv", errorId: "#IndMobileError" },
          { id: "#emailInv", errorId: "#IndEmailError" },
          { id: "#IndSecondName", errorId: "#IndSecondNameError" },
          { id: "#IndPanNumber", errorId: "#IndPanNumberError" },
          { id: "#IndAadhar", errorId: "#IndAadharError" },
          { id: "#commAddress", errorId: "#IndCommAddressError" },
          // { id: "#file-input", errorId: "#file-inputError" },
        ].forEach(function (d) {
          const l = $(d.id),
            i = $(d.errorId);
          l.on("input", function () {
            "" !== l.val().trim() && (l.removeClass("required"), i.hide());
          }),
            "" === l.val().trim()
              ? (l.addClass("required"),
                i.text("This field is required").show(),
                (r = !1),
                null === e && (e = l))
              : (l.removeClass("required"), i.hide());
        });
        const d = $("#countryCode"),
          l = $("#countryCodeError");
        "" === d.val().trim()
          ? (l.text("Country Code is required"),
            l.show(),
            (r = !1),
            null === e && (e = d))
          : l.hide();
        const i = $("#mobileInv"),
          o = $("#IndMobileError"),
          n = i.val().trim(),
          t = i.attr("data-id");
        "" === n
          ? (o.text("This field is required"),
            o.show(),
            (r = !1),
            null === e && (e = i))
          : 10 !== n.length
          ? (o.text("Mobile Number must be exactly 10 digit"),
            o.show(),
            (r = !1),
            null === e && (e = i))
          : "0" === t
          ? (o.text("Please verify your mobile number"),
            o.show(),
            (r = !1),
            null === e && (e = i))
          : o.hide();
       const s = $("#emailInv"),
          a = $("#IndEmailError"),
          u = s.val().trim(),
          h = s.attr("data-id"),
          emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (u === "") {
          a.text("This field is required").show();
          r = false;
          if (e === null) e = s;
        } else if (!emailRegex.test(u)) {
          a.text("Please enter a valid email address").show();
          r = false;
          if (e === null) e = s;
        } else if (h === "0") {
          a.text("Please verify your email").show();
          r = false;
          if (e === null) e = s;
        } else {
          a.hide();
        }
        const c = $("#IndPanNumber"),
          f = $("#IndPanNumberError"),
          I = c.val().trim();
        "" === I
          ? (c.addClass("required"),
            f.text("This field is required").show(),
            (r = !1),
            null === e && (e = c))
          : 10 !== I.length
          ? (c.addClass("required"),
            f.text("PAN Number must be exactly 10 characters").show(),
            (r = !1),
            null === e && (e = c))
          : (c.removeClass("required"), f.hide());
        const m = $("#IndAadhar"),
          v = $("#IndAadharError"),
          E = m.val().trim();
        "" === E
          ? (m.addClass("required"),
            v.text("This field is required").show(),
            (r = !1),
            null === e && (e = m))
          : 12 !== E.length
          ? (m.addClass("required"),
            v.text("Aadhar Number must be exactly 12 digit").show(),
            (r = !1),
            null === e && (e = m))
          : (m.removeClass("required"), v.hide());
        const C = $("#commAddress"),
          q = $("#IndCommAddressError"),
          b = C.val().trim(),
          y = /^[a-zA-Z0-9\s,#\-()/]*$/;
        C.removeClass("required"),
          q.hide(),
          0 === b.length
            ? (C.addClass("required"),
              q.text("This field is required").show(),
              (r = !1),
              null === e && (e = C))
            : b.length > 200
            ? (C.addClass("required"),
              q.text("Address cannot exceed 200 characters").show(),
              (r = !1),
              null === e && (e = C))
            : y.test(b) ||
              (C.addClass("required"),
              q
                .text(
                  "Only letters, digits, hyphen (-), comma (,), hash (#), parenthesis ( ), forward slash (/), and spaces are allowed"
                )
                .show(),
              (r = !1),
              null === e && (e = C));
        // comented by anil and add new funciton validateImageUpload on 03-06-2025
        // const p = $("#file-input"),
        //   w = $("#file-inputError");
        // function F() {
        //   const e = p.val().toLowerCase();
        //   return 0 === p[0].files.length
        //     ? (w.text("Photo is required").show(), !1)
        //     : e.endsWith(".jpg") || e.endsWith(".png")
        //     ? (w.hide(), !0)
        //     : (w.text("Only .jpg or .png files are allowed").show(), !1);
        // }
        // p.on("change", function () {
        //   F();
        // }),

        //   F() || (r = !1);
        if ($("#Yes").is(":checked")) {
          ($("#isPropertyFlat").is(":checked")
            ? [
                { id: "#localityFill", errorId: "#localityFillError" },
                { id: "#blocknoInvFill", errorId: "#blocknoInvFillError" },
                { id: "#plotnoInvFill", errorId: "#plotnoInvFillError" },
                { id: "#landUseInvFill", errorId: "#landUseInvFillError" },
                {
                  id: "#landUseSubtypeInvFill",
                  errorId: "#landUseSubtypeInvFillError",
                },
                {
                  id: "#flat_no_after_Checked_Address_notfound",
                  errorId: "#flat_no_after_Checked_Address_notfoundError",
                },
              ]
            : [
                { id: "#localityFill", errorId: "#localityFillError" },
                { id: "#blocknoInvFill", errorId: "#blocknoInvFillError" },
                { id: "#plotnoInvFill", errorId: "#plotnoInvFillError" },
                { id: "#landUseInvFill", errorId: "#landUseInvFillError" },
                {
                  id: "#landUseSubtypeInvFill",
                  errorId: "#landUseSubtypeInvFillError",
                },
              ]
          ).forEach(function (d) {
            const l = $(d.id),
              i = $(d.errorId);
            l.on("input", function () {
              "" !== l.val().trim() && (l.removeClass("required"), i.hide());
            }),
              "" === l.val().trim()
                ? (l.addClass("required"),
                  i.text("This field is required").show(),
                  (r = !1),
                  null === e && (e = l))
                : (l.removeClass("required"), i.hide());
          });
        } else {
          [
            { id: "#locality", errorId: "#localityError" },
            { id: "#block", errorId: "#blockError" },
            { id: "#plot", errorId: "#plotError" },
            { id: "#landUse", errorId: "#landUseError" },
            { id: "#landUseSubtype", errorId: "#landUseSubtypeError" },
          ].forEach(function (d) {
            const l = $(d.id),
              i = $(d.errorId);
            function o(d, l, i) {
              const o = $(d),
                n = $(l);
              o.on("input", function () {
                "" !== o.val().trim() && (o.removeClass("required"), n.hide());
              }),
                i && "" === o.val().trim()
                  ? (o.addClass("required"),
                    n.text("This field is required").show(),
                    (r = !1),
                    null === e && (e = o))
                  : (o.removeClass("required"), n.hide());
            }
            l.on("input", function () {
              "" !== l.val().trim() && (l.removeClass("required"), i.hide());
            }),
              "" === l.val().trim()
                ? (l.addClass("required"),
                  i.text("This field is required").show(),
                  (r = !1),
                  null === e && (e = l))
                : (l.removeClass("required"), i.hide()),
              $("#isPropertyFlat").is(":checked") &&
                ("FlatNotAvailable" === $("#flatAvailableInv").val() &&
                  "" !== $("#landUse").val() &&
                  "" !== $("#landUseSubtype").val() &&
                  o(
                    "#flat_no_rec_not_found",
                    "#flat_no_rec_not_foundError",
                    !0
                  ),
                "FlatAvailable" === $("#flatAvailableInv").val() &&
                  "" !== $("#landUse").val() &&
                  "" !== $("#landUseSubtype").val() &&
                  (o(
                    "#flat",
                    "#flatError",
                    !$("#isFlatNotInList").is(":checked")
                  ),
                  $("#isFlatNotInList").is(":checked") &&
                    o("#flat_no", "#flat_noError", !0)));
              
          });
          $("#isFlatNotInList").on("change", function () {
            if (!$(this).is(":checked")) {
              $("#flat_no").removeClass("required");
              $("#flat_noError").hide();
            }
          });
        }
      /*  const g = [
          { id: "#IndSaleDeed", errorId: "#IndSaleDeedError" },
          { id: "#IndBuildAgree", errorId: "#IndBuildAgreeError" },
          { id: "#IndSubMut", errorId: "#IndSubMutError" },
          { id: "#IndOther", errorId: "#IndOtherError" },
        ];
        let x = !0,
          A = null,
          k = !1;
        g.forEach(function (e) {
          const r = $(e.id),
            d = $(e.errorId);
          r.removeClass("required"),
            d.hide(),
            r.on("change", function () {
              const e = r[0].files;
              if (e.length > 0) {
                let l = !0;
                for (let r = 0; r < e.length; r++) {
                  if (!e[r].name.endsWith(".pdf")) {
                    l = !1;
                    break;
                  }
                }
                l
                  ? (r.removeClass("required"), d.hide())
                  : (r.addClass("required"),
                    d.text("Only PDF files are allowed").show(),
                    (x = !1),
                    null === A && (A = r));
              }
            });
        }),
          g.forEach(function (e) {
            const r = $(e.id),
              d = $(e.errorId),
              l = r[0].files;
            if (l.length > 0) {
              k = !0;
              let e = !0;
              for (let r = 0; r < l.length; r++) {
                if (!l[r].name.endsWith(".pdf")) {
                  e = !1;
                  break;
                }
              }
              e ||
                (r.addClass("required"),
                d.text("Only PDF files are allowed").show(),
                (x = !1),
                null === A && (A = r));
            }
          }),
          k ||
            g.forEach(function (e) {
              const r = $(e.id),
                d = $(e.errorId);
              r.addClass("required"),
                d.text("At least one file is required").show(),
                (x = !1),
                null === A && (A = r);
            });
        A && A.focus();  */

 const g = [
          {
            id: "#IndSaleDeed",
            errorId: "#IndSaleDeedError",
            checkbox: "#saleDeedAtorney"
          },
          {
            id: "#IndBuildAgree",
            errorId: "#IndBuildAgreeError",
            checkbox: "#bbAgreement"
          },
          {
            id: "#IndSubMut",
            errorId: "#IndSubMutError",
            checkbox: "#subsMutationLetter"
          },
          {
            id: "#IndOther",
            errorId: "#IndOtherError",
            checkbox: "#diffDoc"
          }
        ];

        // ✅ Step 1: Check if any checkbox is checked
        let fileSectionValid = true;
        let fileSectionFirstError = null;
        const anyChecked = g.some(item => $(item.checkbox).is(":checked"));

        if (!anyChecked) {
          $("#IndChooseOneError").text("At least one file is required").show();
          fileSectionValid = false;
        } else {
          $("#IndChooseOneError").hide();
        }

        // ✅ Step 2 & 3: Loop over each item
        g.forEach(function (item) {
          const fileInput = $(item.id);
          const errorDiv = $(item.errorId);
          const checkbox = $(item.checkbox);
          const files = fileInput[0].files;

          // Initial state cleanup
          fileInput.removeClass("required");
          errorDiv.hide();

          // ✅ Bind change event to enable/disable input on checkbox toggle
          checkbox.off("change").on("change", function () {
            if ($(this).is(":checked")) {
              fileInput.prop("disabled", false);
            } else {
              fileInput.prop("disabled", true).val("");
              errorDiv.hide();
              fileInput.removeClass("required");
            }

            // Also hide #IndChooseOneError if any one is checked
            const anyStillChecked = g.some(item => $(item.checkbox).is(":checked"));
            if (anyStillChecked) {
              $("#IndChooseOneError").hide();
            }
          });

          // ✅ Validate if checkbox is checked
          if (checkbox.is(":checked")) {
            fileInput.prop("disabled", false);

            if (files.length === 0) {
              fileInput.addClass("required");
              errorDiv.text("This field is mandatory.").show();
              fileSectionValid = false;
              if (!fileSectionFirstError) fileSectionFirstError = fileInput;
            } else {
              let allPdf = true;
              for (let i = 0; i < files.length; i++) {
                if (!files[i].name.toLowerCase().endsWith(".pdf")) {
                  allPdf = false;
                  break;
                }
              }
              if (!allPdf) {
                fileInput.addClass("required");
                errorDiv.text("Only PDF files are allowed").show();
                fileSectionValid = false;
                if (!fileSectionFirstError) fileSectionFirstError = fileInput;
              }
            }
          } else {
            fileInput.prop("disabled", true).val("");
            errorDiv.hide();
          }
        });
        if (!fileSectionValid && fileSectionFirstError) {
          fileSectionFirstError.focus();
        }
        const _ = $("#IndOwnerLess"),
          U = $("#IndOwnerLessError");
        _.removeClass("required"), U.hide();
        let N = !0,
          P = null;
        function S() {
          const e = _[0].files;

        //   debugger;

          // Case 1: File too large previously
          // if (fileTooLargeMap["IndOwnerLess"]) {
          //   _.addClass("required");
          //   U.text("Maximum allowed size is upto 5 MB.").show();
          //   return false;
          // }

          // Case 2: No file selected
          if (!e || e.length === 0) {
            _.addClass("required");
            U.text("This field is mandatory. Upload a PDF file").show();
            return false;
          }

          // Case 3: Extension check
          const file = e[0];
          if (!file.name.toLowerCase().endsWith(".pdf")) {
            _.addClass("required");
            U.text("Only PDF files are allowed").show();
            return false;
          }

          // ✅ All good
          _.removeClass("required");
          U.hide();
          return true;
        }

        // Change + focus handler
        // _.on("focus", function () {
        //   fileTooLargeMap["IndOwnerLess"] = false;
        // }),

        // _.on("change", function () {
        //   validateFileSize("IndOwnerLess", "IndOwnerLessError", 5); // size
        //   S(); // type and empty check
        // }),
          S() || ((N = !1), (P = _));
        P && P.focus();
        const O = $("#IndLeaseDeed"),
          D = $("#IndLeaseDeedError");
        O.removeClass("required"), D.hide();
        let T = !0,
          L = null;
        function B() {
          const files = O[0].files;
          
          // Case 1: File was too large previously
          // if (fileTooLargeMap["IndLeaseDeed"]) {
          //   O.addClass("required");
          //   D.text("Maximum allowed size is upto 5 MB.").show();
          //   return false;
          // }

          // Case 2: No file selected
          if (!files || files.length === 0) {
            O.addClass("required");
            D.text("This field is mandatory. Upload a PDF file").show();
            return false;
          }

          // Case 3: Check extension
          const file = files[0];
          if (!file.name.toLowerCase().endsWith(".pdf")) {
            O.addClass("required");
            D.text("Only PDF files are allowed").show();
            return false;
          }

          // ✅ All good — clear error
          O.removeClass("required");
          D.hide();
          return true;
        }
        // O.on("focus", function () {
        //   fileTooLargeMap["IndLeaseDeed"] = false;
        // });


        // if (!beforeSubmitFunction()) {
        //   formIsValid = false;
        // }

        // O.on("change", function () {
        //   B();
        // }),
          B() || ((T = !1), (L = O));
        L && L.focus();
        null !== e && e.focus();
        const M = $("#IndConsent"),
          W = $("#IndConsentError");
        M.is(":checked")
          ? (M.removeClass("required"), W.hide())
          : (M.addClass("required"),
            W.text("You must agree to the terms").show(),
            (r = !1));
        null !== e && e.focus();
        // return r && x && N && T; commented and add below line to fixe issue in "Is Your Property Flat?" validation by anil on 12-06-2025
          return formIsValid && r && fileSectionValid && N && T;
      })() &&
        ($("#IndsubmitButton").attr("disabled", !0).text("Submitting..."),
        i[0].submit());
    }),
    d(),
    $("#Yes").change(function () {
      d();
    });
});

// function modified by Nitin to allow asynchronous captcha validation

async function captchaValidation(targetId) {
  const csrfToken = $('meta[name="csrf-token"]').attr("content");
  const $input = $("#" + targetId); //#invRegisterCaptcha
  const $errorSpan = $("#" + targetId + "Error");
   const invRegisterCaptcha = $input.val();

   if (invRegisterCaptcha.trim() == "") {
     $errorSpan.text("Captcha is required");
     $input.addClass("required");
     return false;
   }

  if ($input.prop("readonly")) {
    $input.prop("readonly", false);
    $errorSpan.addClass("text-danger");
  }

  try {
    const result = await $.ajax({
      url: validateCaptcha,
      type: "POST",
      data: {
        invRegisterCaptcha: invRegisterCaptcha,
        _token: csrfToken,
      },
      dataType: "json",
    });

    if (result.success) {
      $errorSpan
        .removeClass("text-danger")
        .css("color", "green")
        .html(result.message);
      $input.attr("readonly", true);
      $input.removeClass("required");
      return true;
    } else {
      $errorSpan.html(result.message);
      return false;
    }
  } catch (err) {
    $errorSpan.html("Captcha validation failed");
    return false;
  }
}

// Minified by Diwakar Sinha at 01-01-2025
$(document).ready(function () {
  var r = $(".dynamicForm");
  function e() {
    $("#propertyowner").is(":checked")
      ? r.attr("id", "propertyOwnerForm")
      : $("#organization").is(":checked") && r.attr("id", "organizationForm");
  }
 function o() {
    if ($("#YesOrg").is(":checked")) {
      $("#ifyesOrg").show();
      $("#ifYesNotCheckedOrg").hide();
      $(".isPropertyDetailsRecordNotFoundUnCheckedOrg").addClass("hide-sec");
      $(".isPropertyDetailsNotFoundUnCheckedOrg").addClass("hide-sec");
    } else {
      $("#ifyesOrg").hide();
      $("#ifYesNotCheckedOrg").show();
      $(".isPropertyDetailsRecordNotFoundUnCheckedOrg").removeClass("hide-sec");
      $(".isPropertyDetailsNotFoundUnCheckedOrg").removeClass("hide-sec");
    }
  }
  $("#propertyowner, #organization").change(function () {
    e();
  }),
    // Final full version of the #OrgsubmitButton validation logic with file input validation added

    // Final full version of the #OrgsubmitButton validation logic with file input validation added

    // Final full version of the #OrgsubmitButton validation logic with file input validation added

    // Final full version of the #OrgsubmitButton validation logic with file input validation added

$("#OrgsubmitButton").click(async function (o) {
  o.preventDefault();
  e(); // refresh or bind current form reference

  var formId = r.attr("id"),
    $form = $("#" + formId);

  const isValid = await (async function () {
    let firstInvalid = null,
      allRequiredValid = true,
      fileValidationPassed = true,
      formIsValid = true;

    // Basic required fields
    const requiredFields = [
      { id: "#OrgName", errorId: "#OrgNameError" },
      { id: "#OrgPAN", errorId: "#OrgPANError" },
      { id: "#orgAddressOrg", errorId: "#orgAddressOrgError" },
      { id: "#OrgNameAuthSign", errorId: "#OrgNameAuthSignError" },
      { id: "#authsignatory_mobile", errorId: "#authsignatory_mobileError" },
      { id: "#emailauthsignatory", errorId: "#emailauthsignatoryError" },
      { id: "#orgAadharAuth", errorId: "#orgAadharAuthError" }
    ];

/*    requiredFields.forEach(function (field) {
      const $input = $(field.id);
      const $error = $(field.errorId);
      $input.on("input", function () {
        if ($input.val().trim() !== "") {
          $input.removeClass("required");
          $error.hide();
        }
      });
      if ($input.val().trim() === "") {
        $input.addClass("required");
        $error.text("This field is required").show();
        allRequiredValid = false;
        if (firstInvalid === null) firstInvalid = $input;
      } else {
        $input.removeClass("required");
        $error.hide();
      }
    });
*/
 requiredFields.forEach(function (field) {
      const $input = $(field.id);
      const $error = $(field.errorId);

      $input.off("input.org").on("input.org", function () { // ✅ UPDATED: prevent multiple listeners
        if ($input.val().trim() !== "") {
          $input.removeClass("required");
          $error.hide();
        }
      });

      if ($input.val().trim() === "") {
        $input.addClass("required");
        $error.text("This field is required").show();
        allRequiredValid = false;
        if (firstInvalid === null) firstInvalid = $input;
      } else {
        $input.removeClass("required");
        $error.hide();
      }
    });

    // Mobile Validation
    const $mobile = $("#authsignatory_mobile");
    const $mobileError = $("#OrgMobileAuthError");
    const mobileVal = $mobile.val().trim();
    const mobileVerified = $mobile.attr("data-id");

/*    if (mobileVal !== "") {
      if (mobileVal.length !== 10) {
        $mobileError.text("Mobile Number must be exactly 10 digit").show();
        allRequiredValid = false;
        if (firstInvalid === null) firstInvalid = $mobile;
      } else if (mobileVerified === "0") {
        $mobileError.text("Please verify your mobile number").show();
        allRequiredValid = false;
        if (firstInvalid === null) firstInvalid = $mobile;
      } else {
        $mobileError.hide();
      }
    } */
   if (mobileVal === "") {
      $mobile.addClass("required");
      $mobileError.text("This field is required").show();
      allRequiredValid = false;
      if (firstInvalid === null) firstInvalid = $mobile;
    } else if (mobileVal.length !== 10) {
      $mobile.addClass("required");
      $mobileError.text("Mobile Number must be exactly 10 digit").show();
      allRequiredValid = false;
      if (firstInvalid === null) firstInvalid = $mobile;
    } else if (mobileVerified === "0") {
      $mobile.addClass("required");
      $mobileError.text("Please verify your mobile number").show();
      allRequiredValid = false;
      if (firstInvalid === null) firstInvalid = $mobile;
    } else {
      $mobile.removeClass("required");
      $mobileError.hide();
    }

    // Email Verification
      const $email = $("#emailauthsignatory");
    const $emailError = $("#OrgEmailAuthSignError");
    const emailVal = $email.val().trim();
    const emailVerified = $email.attr("data-id");
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
   /* const $email = $("#emailauthsignatory");
    const $emailError = $("#OrgEmailAuthSignError");
    const emailVerified = $email.attr("data-id");

    if ($email.val().trim() !== "" && emailVerified === "0") {
      $emailError.text("Please verify your email").show();
      allRequiredValid = false;
      if (firstInvalid === null) firstInvalid = $email;
    } else {
      $emailError.hide();
    }  */
   
    if (emailVal === "") {
      $email.addClass("required");
      $emailError.text("This field is required").show();
      allRequiredValid = false;
      if (firstInvalid === null) firstInvalid = $email;
    } else if (!emailRegex.test(emailVal)) {
      $email.addClass("required");
      $emailError.text("Please enter a valid email address").show();
      allRequiredValid = false;
      if (firstInvalid === null) firstInvalid = $email;
    } else if (emailVerified === "0") {
      $email.addClass("required");
      $emailError.text("Please verify your email").show();
      allRequiredValid = false;
      if (firstInvalid === null) firstInvalid = $email;
    } else {
      $email.removeClass("required");
      $emailError.hide();
    }

    // PAN Validation + ➕ OPTIONAL format check
    const $pan = $("#OrgPAN");
    const $panError = $("#OrgPANError");
    const panVal = $pan.val().trim();
    const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/; // ➕ OPTIONAL

    if (panVal !== "") {
      if (panVal.length !== 10) {
        $pan.addClass("required");
        $panError.text("PAN Number must be exactly 10 characters").show();
        allRequiredValid = false;
        if (firstInvalid === null) firstInvalid = $pan;
      } else if (!panRegex.test(panVal)) { // ➕ OPTIONAL
        $pan.addClass("required");
        $panError.text("Invalid PAN format (e.g. ABCDE1234F)").show();
        allRequiredValid = false;
        if (firstInvalid === null) firstInvalid = $pan;
      } else {
        $pan.removeClass("required");
        $panError.hide();
      }
    }

    // Aadhaar Validation + ➕ OPTIONAL format check
    const $aadhaar = $("#orgAadharAuth");
    const $aadhaarError = $("#orgAadharAuthError");
    const aadhaarVal = $aadhaar.val().trim();
    const aadhaarRegex = /^\d{12}$/; // ➕ OPTIONAL

    if (aadhaarVal !== "") {
      if (aadhaarVal.length !== 12 || !aadhaarRegex.test(aadhaarVal)) {
        $aadhaar.addClass("required");
        $aadhaarError.text("Aadhar Number must be exactly 12 digits").show();
        allRequiredValid = false;
        if (firstInvalid === null) firstInvalid = $aadhaar;
      } else {
        $aadhaar.removeClass("required");
        $aadhaarError.hide();
      }
    }
    // // Conditional fields commented by anil for updated code with validation for #flatOrg and Is Flat not Listed? on 04-07-2025
    // const yesOrgChecked = $("#YesOrg").is(":checked");
    // const isFlat = $("#isPropertyFlatOrg").is(":checked");

    // const conditionalFields = yesOrgChecked
    //   ? isFlat
    //     ? [
    //         { id: "#localityOrgFill", errorId: "#localityOrgFillError" },
    //         { id: "#blocknoOrgFill", errorId: "#blocknoOrgFillError" },
    //         { id: "#plotnoOrgFill", errorId: "#plotnoOrgFillError" },
    //         { id: "#landUseOrgFill", errorId: "#landUseOrgFillError" },
    //         { id: "#landUseSubtypeOrgFill", errorId: "#landUseSubtypeOrgFillError" },
    //         { id: "#flat_no_org_after_checked_Address_notfound", errorId: "#flat_no_org_after_checked_Address_notfoundError" }
    //       ]
    //     : [
    //         { id: "#localityOrgFill", errorId: "#localityOrgFillError" },
    //         { id: "#blocknoOrgFill", errorId: "#blocknoOrgFillError" },
    //         { id: "#plotnoOrgFill", errorId: "#plotnoOrgFillError" },
    //         { id: "#landUseOrgFill", errorId: "#landUseOrgFillError" },
    //         { id: "#landUseSubtypeOrgFill", errorId: "#landUseSubtypeOrgFillError" }
    //       ]
    //   : [
    //       { id: "#locality_org", errorId: "#locality_orgError" },
    //       { id: "#block_org", errorId: "#block_orgError" },
    //       { id: "#plot_org", errorId: "#plot_orgError" },
    //       { id: "#landUse_org", errorId: "#landUse_orgError" },
    //       { id: "#landUseSubtype_org", errorId: "#landUseSubtype_orgError" }
    //     ];

    // conditionalFields.forEach(function (field) {
    //   const $input = $(field.id);
    //   const $error = $(field.errorId);
    //   $input.on("input", function () {
    //     if ($input.val().trim() !== "") {
    //       $input.removeClass("required");
    //       $error.hide();
    //     }
    //   });
    //   if ($input.val().trim() === "") {
    //     $input.addClass("required");
    //     $error.text("This field is required").show();
    //     allRequiredValid = false;
    //     if (!firstInvalid) firstInvalid = $input;
    //   }
    // });
    // // Conditional: Only validate #flat_no_org_rec_not_found if #isPropertyFlatOrg is checked
    // if ($("#isPropertyFlatOrg").is(":checked")) {
    //   const $flatField = $("#flat_no_org_rec_not_found");
    //   const $flatError = $("#flat_no_org_rec_not_foundError");

    //   $flatField.on("input", function () {
    //     if ($flatField.val().trim() !== "") {
    //       $flatField.removeClass("required");
    //       $flatError.hide();
    //     }
    //   });

    //   if ($flatField.val().trim() === "") {
    //     $flatField.addClass("required");
    //     $flatError.text("This field is required").show();
    //     allRequiredValid = false;
    //     if (!firstInvalid) firstInvalid = $flatField;
    //   } else {
    //     $flatField.removeClass("required");
    //     $flatError.hide();
    //   }
    // }

    // Conditional fields
    const yesOrgChecked = $("#YesOrg").is(":checked");
    const isFlat = $("#isPropertyFlatOrg").is(":checked");

    const conditionalFields = yesOrgChecked
      ? isFlat
        ? [
            { id: "#localityOrgFill", errorId: "#localityOrgFillError" },
            { id: "#blocknoOrgFill", errorId: "#blocknoOrgFillError" },
            { id: "#plotnoOrgFill", errorId: "#plotnoOrgFillError" },
            { id: "#landUseOrgFill", errorId: "#landUseOrgFillError" },
            { id: "#landUseSubtypeOrgFill", errorId: "#landUseSubtypeOrgFillError" },
            {
              id: "#flat_no_org_after_checked_Address_notfound",
              errorId: "#flat_no_org_after_checked_Address_notfoundError",
            },
          ]
        : [
            { id: "#localityOrgFill", errorId: "#localityOrgFillError" },
            { id: "#blocknoOrgFill", errorId: "#blocknoOrgFillError" },
            { id: "#plotnoOrgFill", errorId: "#plotnoOrgFillError" },
            { id: "#landUseOrgFill", errorId: "#landUseOrgFillError" },
            { id: "#landUseSubtypeOrgFill", errorId: "#landUseSubtypeOrgFillError" },
          ]
      : [
          { id: "#locality_org", errorId: "#locality_orgError" },
          { id: "#block_org", errorId: "#block_orgError" },
          { id: "#plot_org", errorId: "#plot_orgError" },
          { id: "#landUse_org", errorId: "#landUse_orgError" },
          { id: "#landUseSubtype_org", errorId: "#landUseSubtype_orgError" },
        ];

    conditionalFields.forEach(function (field) {
      const $input = $(field.id);
      const $error = $(field.errorId);

      $input.on("input", function () {
        if ($input.val().trim() !== "") {
          $input.removeClass("required");
          $error.hide();
        }
      });

      if ($input.val().trim() === "") {
        $input.addClass("required");
        $error.text("This field is required").show();
        allRequiredValid = false;
        if (!firstInvalid) firstInvalid = $input;
      }
    });

    // Section: Flat Available / Not Available
    if ($("#isPropertyFlatOrg").is(":checked")) {

      // 🔹 Case: isPropertyDetailsNotFoundUnCheckedOrg
      if ($(".isPropertyDetailsNotFoundUnCheckedOrg").css("display") !== "none") {
        const isNotInList = $("#isFlatNotInListOrg").is(":checked");

        const $flatSelect = $("#flatOrg");
        const $flatSelectError = $("#flatOrgError");

        const $flatInput = $("#flat_no_org");
        const $flatInputError = $("#flat_no_orgError");

        $flatInput.on("input", function () {
          if ($flatInput.val().trim() !== "") {
            $flatInput.removeClass("required");
            $flatInputError.hide();
          }
        });

        $flatSelect.on("change", function () {
          if ($flatSelect.val() !== "") {
            $flatSelect.removeClass("required");
            $flatSelectError.hide();
          }
        });

        if (!isNotInList) {
          if ($flatSelect.val().trim() === "") {
            $flatSelect.addClass("required");
            $flatSelectError.text("This field is required").show();
            allRequiredValid = false;
            if (!firstInvalid) firstInvalid = $flatSelect;
          }
        } else {
          if ($flatInput.val().trim() === "") {
            $flatInput.addClass("required");
            $flatInputError.text("This field is required").show();
            allRequiredValid = false;
            if (!firstInvalid) firstInvalid = $flatInput;
          }
        }

        $("#isFlatNotInListOrg").on("change", function () {
          if ($(this).is(":checked")) {
            $flatSelect.removeClass("required");
            $flatSelectError.hide();
          } else {
            $flatInput.removeClass("required");
            $flatInputError.hide();
          }
        });
      }

      // 🔹 Case: isPropertyDetailsRecordNotFoundUnCheckedOrg
      if ($(".isPropertyDetailsRecordNotFoundUnCheckedOrg").css("display") !== "none") {
        const $flatRecField = $("#flat_no_org_rec_not_found");
        const $flatRecError = $("#flat_no_org_rec_not_foundError");

        $flatRecField.on("input", function () {
          if ($flatRecField.val().trim() !== "") {
            $flatRecField.removeClass("required");
            $flatRecError.hide();
          }
        });

        if ($flatRecField.val().trim() === "") {
          $flatRecField.addClass("required");
          $flatRecError.text("This field is required").show();
          allRequiredValid = false;
          if (!firstInvalid) firstInvalid = $flatRecField;
        }
      }
    }

/* const docGroup = [
      { id: "#OrgSaleDeedDoc", errorId: "#OrgSaleDeedDocError" },
      { id: "#OrgBuildAgreeDoc", errorId: "#OrgBuildAgreeDocError" },
      { id: "#OrgSubMutDoc", errorId: "#OrgSubMutDocError" },
      { id: "#OrgOther", errorId: "#OrgOtherError" }
    ];

    function validateDocGroup() {
      let oneUploaded = false;
      fileValidationPassed = true;
      let firstInvalid = null;

      docGroup.forEach(doc => {
        const $input = $(doc.id);
        const $error = $(doc.errorId);
        const files = $input[0].files;

        if (files.length > 0) {
          oneUploaded = true;

          // ✅ Check if the uploaded file is a PDF
          const file = files[0];
          const fileName = file.name.toLowerCase();
          if (!fileName.endsWith(".pdf")) {
            $error.text("Only PDF files are allowed").show();
            fileValidationPassed = false;
            if (!firstInvalid) firstInvalid = $input;
          } else {
            $error.hide();
          }
        } else {
          $error.hide();
        }
      });

      docGroup.forEach(doc => {
        const $input = $(doc.id);
        const $error = $(doc.errorId);
        const files = $input[0].files;

        if (oneUploaded) {
          $input.removeClass("required");
          if (files.length === 0) $error.hide(); // hide others' errors
        } else {
          $input.addClass("required");
          $error.text("At least one file is required").show();
          fileValidationPassed = false;
          if (!firstInvalid) firstInvalid = $input;
        }
      });
    } */

    // 🟡 At least one of 4 Org documents is required and must be .pdf
  
        const docGroup = [
      {
        id: "#OrgSaleDeedDoc",
        errorId: "#OrgSaleDeedDocError",
        checkbox: "#saleDeedAtorneyOrg"
      },
      {
        id: "#OrgBuildAgreeDoc",
        errorId: "#OrgBuildAgreeDocError",
        checkbox: "#bbAgreementOrg"
      },
      {
        id: "#OrgSubMutDoc",
        errorId: "#OrgSubMutDocError",
        checkbox: "#subsMutationLetterOrg"
      },
      {
        id: "#OrgOther",
        errorId: "#OrgOtherError",
        checkbox: "#diffDocOrg"
      }
    ];

    // ✅ Step 1: Check if any checkbox is checked
    let orgFileSectionValid = true;
    let orgFileSectionFirstError = null;
    const anyOrgChecked = docGroup.some(item => $(item.checkbox).is(":checked"));

    if (!anyOrgChecked) {
      $("#OrgChooseOneError").text("At least one file is required").show();
      orgFileSectionValid = false;
    } else {
      $("#OrgChooseOneError").hide();
    }

    // ✅ Step 2 & 3: Loop over each item
    docGroup.forEach(function (item) {
      const fileInput = $(item.id);
      const errorDiv = $(item.errorId);
      const checkbox = $(item.checkbox);
      const files = fileInput[0].files;

      // Initial cleanup
      fileInput.removeClass("required");
      errorDiv.hide();

      // ✅ Bind change event to enable/disable input
      checkbox.off("change").on("change", function () {
        if ($(this).is(":checked")) {
          fileInput.prop("disabled", false);
        } else {
          fileInput.prop("disabled", true).val("");
          errorDiv.hide();
          fileInput.removeClass("required");
        }

        // Hide general error if any one is now checked
        const anyStillChecked = docGroup.some(item => $(item.checkbox).is(":checked"));
        if (anyStillChecked) {
          $("#OrgChooseOneError").hide();
        }
      });

      // ✅ Validate if checkbox is checked
      if (checkbox.is(":checked")) {
        fileInput.prop("disabled", false);

        if (files.length === 0) {
          fileInput.addClass("required");
          errorDiv.text("This field is mandatory.").show();
          orgFileSectionValid = false;
          if (!orgFileSectionFirstError) orgFileSectionFirstError = fileInput;
        } else {
          let allPdf = true;
          for (let i = 0; i < files.length; i++) {
            if (!files[i].name.toLowerCase().endsWith(".pdf")) {
              allPdf = false;
              break;
            }
          }
          if (!allPdf) {
            fileInput.addClass("required");
            errorDiv.text("Only PDF files are allowed").show();
            orgFileSectionValid = false;
            if (!orgFileSectionFirstError) orgFileSectionFirstError = fileInput;
          }
        }
      } else {
        fileInput.prop("disabled", true).val("");
        errorDiv.hide();
      }
    });

    // ✅ Focus on first invalid input if needed
    if (!orgFileSectionValid && orgFileSectionFirstError) {
      orgFileSectionFirstError.focus();
    }
    // Other required file inputs with PDF check
    const otherFiles = [
      { id: "#OrgSignAuthDoc", errorId: "#OrgSignAuthDocError" },
      { id: "#scannedIDOrg", errorId: "#scannedIDOrgError" },
      { id: "#OrgLeaseDeedDoc", errorId: "#OrgLeaseDeedDocError" }
    ];

    otherFiles.forEach(group => {
      const $input = $(group.id);
      const $error = $(group.errorId);
      const files = $input[0].files;
      if (files.length === 0) {
        $input.addClass("required");
        $error.text("This field is required").show();
        fileValidationPassed = false;
        if (!firstInvalid) firstInvalid = $input;
      } else {
        let allPdf = true;
        for (let i = 0; i < files.length; i++) {
          if (!files[i].name.endsWith(".pdf")) {
            allPdf = false;
            break;
          }
        }
        if (!allPdf) {
          $input.addClass("required");
          $error.text("Only PDF files are allowed").show();
          fileValidationPassed = false;
          if (!firstInvalid) firstInvalid = $input;
        } else {
          $input.removeClass("required");
          $error.hide();
        }
      }
    });

    // Consent
    const $consent = $("#OrgConsent"),
      $consentError = $("#OrgConsentError");
    if (!$consent.is(":checked")) {
      $consent.addClass("required");
      $consentError.text("You must agree to the terms").show();
      allRequiredValid = false;
      if (!firstInvalid) firstInvalid = $consent;
    } else {
      $consent.removeClass("required");
      $consentError.hide();
    }

    // Captcha
     const $captchaInput = $("#orgRegisterCaptcha");
     const $captchaError = $("#orgRegisterCaptchaError");
     const captchaIsValid = await captchaValidation("orgRegisterCaptcha");
     if (!captchaIsValid) {
       formIsValid = false;
       $captchaInput.addClass("required");
       $captchaError.text("Captcha is required").show();
       if (!firstInvalid) firstInvalid = $captchaInput;
     } else {
       $captchaInput.removeClass("required");
       $captchaError.hide();
     }

    if (firstInvalid) firstInvalid.focus();

  //  return allRequiredValid && fileValidationPassed && formIsValid;
    return allRequiredValid && fileValidationPassed && formIsValid && orgFileSectionValid;
 
 })();

  if (isValid) {
    $("#OrgsubmitButton").attr("disabled", true).text("Submitting...");
    $form[0].submit();
  }
});

    o(),
    $("#YesOrg").change(function () {
      o();
    });
});
