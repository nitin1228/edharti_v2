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
  });
  // const g = [
  //   {
  //     id: "#IndSaleDeed",
  //     errorId: "#IndSaleDeedError",
  //     checkbox: "#saleDeedAtorney"
  //   },
  //   {
  //     id: "#IndBuildAgree",
  //     errorId: "#IndBuildAgreeError",
  //     checkbox: "#bbAgreement"
  //   },
  //   {
  //     id: "#IndSubMut",
  //     errorId: "#IndSubMutError",
  //     checkbox: "#subsMutationLetter"
  //   },
  //   {
  //     id: "#IndOther",
  //     errorId: "#IndOtherError",
  //     checkbox: "#diffDoc"
  //   }
  // ];

  // g.forEach(function (item) {
  //   const fileInput = $(item.id);
  //   const checkbox = $(item.checkbox);
  //   const errorDiv = $(item.errorId);

  //   // Show/hide on page load
  //   if (checkbox.is(":checked")) {
  //     fileInput.css("display", "block");
  //   } else {
  //     fileInput.css("display", "none").val("");
  //     errorDiv.hide();
  //     fileInput.removeClass("required");
  //   }

  //   // Show/hide immediately on checkbox change
  //   checkbox.off("change.init").on("change.init", function () {
  //     if ($(this).is(":checked")) {
  //       fileInput.css("display", "block");
  //     } else {
  //       fileInput.css("display", "none").val("");
  //       errorDiv.hide();
  //       fileInput.removeClass("required");
  //     }

  //     // Hide global section error if any box is checked
  //     const anyChecked = g.some(x => $(x.checkbox).is(":checked"));
  //     if (anyChecked) {
  //       $("#IndChooseOneError").hide();
  //     }
  //   });
  // });
  $("#IndsubmitButton").click(async function (d) {
    d.preventDefault(), r();

    let formIsValid = true;

    // Run image validation
    const imageValid = validateImageUpload(
      "#file-input",
      "#file-inputError",
      "#img-preview",
      true // submit flag
    );

    // added by anil new fucntion on 10-11-2025
    if (!imageValid) {
      formIsValid = false;
      const imageInput = $("#file-input");
      const imageError = $("#file-inputError");

      // Make sure the error is visible and focus on input
      imageError.show();
      imageInput.focus();

      console.log("Image validation failed - focusing on #file-input");
    }

    const captchaIsValid = await captchaValidation("invRegisterCaptcha");
    if (!captchaIsValid) formIsValid = false;

    // if (!imageValid) formIsValid = false;
    // end added by anil new fucntion on 05-06-2025

    var l = e.attr("id"),
      i = $("#" + l);
    (function () {
      // let e = null,
      //   r = !0;
      let e = null,
        r = true;
      // #Start Modified by Lalit to manage aadhaar, pan, passport, oci card validation on NRI / OCI check box on 10-01-2025
      // const isIndian = $("#isIndian").is(":checked");
      // const baseFields = [
      //   { id: "#indfullname", errorId: "#IndFullNameError" },
      //   { id: "#Indgender", errorId: "#IndGenderError" },
      //   { id: "#dateOfBirth", errorId: "#dateOfBirthError" },
      //   { id: "#mobileInv", errorId: "#IndMobileError" },
      //   { id: "#emailInv", errorId: "#IndEmailError" },
      //   { id: "#IndSecondName", errorId: "#IndSecondNameError" },
      //   { id: "#commAddress", errorId: "#IndCommAddressError" },
      // ];

      // const conditionalFields = isIndian
      //   ? [
      //     { id: "#IndPanNumber", errorId: "#IndPanNumberError" },
      //     { id: "#IndAadhar", errorId: "#IndAadharError" },
      //   ]
      //   : [
      //     // anil nri validation
      //     // { id: "#IndPassportNumber", errorId: "#IndPassportNumberError" },
      //     // { id: "#IndOci", errorId: "#IndOciError" },
      //   ];

      // const validationArray = [...baseFields, ...conditionalFields];
      // validationArray.forEach(function (d) {
      //   const l = $(d.id),
      //     i = $(d.errorId);
      //   l.on("input", function () {
      //     "" !== l.val().trim() && (l.removeClass("required"), i.hide());
      //   }),
      //     "" === l.val().trim()
      //       ? (l.addClass("required"),
      //         i.text("This field is required").show(),
      //         (r = !1),
      //         null === e && (e = l))
      //       : (l.removeClass("required"), i.hide());
      // });

      // ===== Base Fields =====
      const baseFields = [
        { id: "#indfullname", errorId: "#IndFullNameError" },
        { id: "#Indgender", errorId: "#IndGenderError" },
        { id: "#dateOfBirth", errorId: "#dateOfBirthError" },
        { id: "#mobileInv", errorId: "#IndMobileError" },
        { id: "#emailInv", errorId: "#IndEmailError" },
        { id: "#IndSecondName", errorId: "#IndSecondNameError" },
        { id: "#commAddress", errorId: "#IndCommAddressError" }
      ];

      // Validate base required fields (name, gender, dob, mobile, email, second name, address)
      baseFields.forEach(function (d) {
        const l = $(d.id),
          i = $(d.errorId),
          val = l.val().trim();

        if (val === "") {
          l.addClass("required");
          i.text("This field is required").show();
          r = false;
          // if (eField === null) eField = l;
          null === e && (e = l);
        } else {
          // l.removeClass("required");
          // i.hide();
          // Special validation for indfullname field - check for dots
          if (d.id === "#indfullname" && val.includes(".")) {
            l.addClass("required");
            i.text("Full name should not contain dots (.)").show();
            r = false;
            null === e && (e = l);
          } else {
            l.removeClass("required");
            i.hide();
          }
        }
      });

      // ===== Indian / Non-Indian Validation =====
      // Determine if applicant is resident of India (radio value "1" => Yes)
      const isIndian = $("input[name='isIndian']:checked").val() === "1";

      if (isIndian) {
        // -------------------------
        // If Indian: clear Non-Indian fields/errors (because user may have switched)
        // -------------------------
        // Clear document type/number (values + error state)
        $("#documentType").val("");
        $("#documentTypeError").text("").hide();
        $("#documentType").removeClass("required");

        $("#documentTypeNumber").val("");
        $("#documentTypeNumberError").text("").hide();
        $("#documentTypeNumber").removeClass("required");
        $("#documentTypeNumber").removeAttr("maxlength");

        // ----- PAN -----
        const pan = $("#IndPanNumber"),
          panErr = $("#IndPanNumberError"),
          panVal = pan.val().trim().toUpperCase();
        pan.val(panVal);

        if (panVal === "") {
          pan.addClass("required");
          panErr.text("This field is required").show();
          r = false;
          // if (eField === null) eField = pan;
          null === e && (e = pan);
        } else if (!/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(panVal)) {
          pan.addClass("required");
          panErr.text("Please enter a valid PAN (ABCDE1234F)").show();
          r = false;
          // if (eField === null) eField = pan;
          null === e && (e = pan);
        } else {
          pan.removeClass("required");
          panErr.hide();
        }

        // ----- Aadhaar -----
        const aadhaar = $("#IndAadhar"),
          aadErr = $("#IndAadharError"),
          aadVal = aadhaar.val().trim();
        if (aadVal === "") {
          aadhaar.addClass("required");
          aadErr.text("This field is required").show();
          r = false;
          // if (eField === null) eField = aadhaar;
          null === e && (e = aadhaar);
        } else if (!/^\d{12}$/.test(aadVal)) {
          aadhaar.addClass("required");
          aadErr.text("Please enter a valid 12-digit Aadhaar number").show();
          r = false;
          // if (eField === null) eField = aadhaar;
          null === e && (e = aadhaar);
        } else {
          aadhaar.removeClass("required");
          aadErr.hide();
        }

      } else {
        // -------------------------
        // If Non-Indian: clear Indian fields/errors (user requested clearing)
        // -------------------------
        // Clear PAN / Aadhaar values and errors as user requested "YES CLEAR"
        $("#IndPanNumber").val("");
        $("#IndPanNumberError").text("").hide();
        $("#IndPanNumber").removeClass("required");

        $("#IndAadhar").val("");
        $("#IndAadharError").text("").hide();
        $("#IndAadhar").removeClass("required");

        // ----- Non-Indian: Document Type + Document Number -----
        const docType = $("#documentType"),
          docTypeErr = $("#documentTypeError"),
          // normalize selected value to uppercase to handle 'pion','pion' etc.
          docTypeValRaw = (docType.val() || "").trim(),
          docTypeVal = docTypeValRaw.toUpperCase();

        const docNum = $("#documentTypeNumber"),
          docNumErr = $("#documentTypeNumberError"),
          docNumValRaw = (docNum.val() || "").trim(),
          docNumVal = docNumValRaw.toUpperCase();

        // put uppercase value back into the input (helps regex)
        docNum.val(docNumVal);

        // ---- Dynamic maxlength based on document type ----
        // Accept common synonyms: PIO / PION, OCI / OCIN, PASSPORT
        if (["PIO", "PION"].includes(docTypeVal)) {
          docNum.attr("maxlength", 8);   // PIO / PION => 1 letter + 7 digits (8)
        } else if (["OCI", "OCIN"].includes(docTypeVal)) {
          docNum.attr("maxlength", 10);  // OCI / OCIN => 1 letter + 9 digits (10)
        } else if (docTypeVal === "PASSPORT") {
          docNum.attr("maxlength", 12);  // Passport => up to 12
        } else {
          docNum.removeAttr("maxlength");
        }

        // ---- Validate Document Type ----
        if (docTypeVal === "") {
          docType.addClass("required");
          docTypeErr.text("Please select a document type").show();
          r = false;
          // if (eField === null) eField = docType;
          null === e && (e = docType);
        } else {
          docType.removeClass("required");
          docTypeErr.hide();
        }

        // ---- Validate Document Number ----
        if (docNumVal === "") {
          docNum.addClass("required");
          docNumErr.text("This field is required").show();
          r = false;
          // if (eField === null) eField = docNum;
          null === e && (e = docNum);
        } else {
          // Reset previous error state before applying regex
          docNum.removeClass("required");
          docNumErr.hide();

          // Determine regex and error message for all supported doc types
          let regex = null,
            errMsg = "";

          if (["PIO", "PION"].includes(docTypeVal)) {
            // Person of Indian Origin Number (PION/PIO) => 1 letter + 7 digits
            regex = /^[A-Z]\d{7}$/;
            errMsg = "Invalid PIO/PION Number (1 letter + 7 digits)";
          } else if (["OCI", "OCIN"].includes(docTypeVal)) {
            // Overseas Citizen of India Number (OCI / OCIN) => 1 letter + 9 digits
            regex = /^[A-Z]\d{9}$/;
            errMsg = "Invalid OCI/OCIN Number (1 letter + 9 digits)";
          } else if (docTypeVal === "PASSPORT") {
            // Passport: alpha-numeric, length between 6 and 12
            regex = /^[A-Z0-9]{6,12}$/;
            errMsg = "Invalid Passport Number (6-12 alphanumeric characters)";
          }

          // Only run regex if we identified one for the selected doc type
          if (regex && !regex.test(docNumVal)) {
            docNum.addClass("required");
            docNumErr.text(errMsg).show();
            r = false;
            // if (eField === null) eField = docNum;
            null === e && (e = docNum);
          } else if (regex) {
            // passes regex
            docNum.removeClass("required");
            docNumErr.hide();
          }
          // if no regex (unknown doc type) we already flagged docType above
        }
      }

      // ===== Focus first invalid field =====
      // if (!r && eField !== null) eField.focus();

      // // Keep console logs as requested
      // if (r) {
      //   console.log("Form is valid");
      // } else {
      //   console.log("Form has errors");
      // }

      // #End Modified by Lalit on 10-01-2025
      /* [
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
      }); */

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
      // commented by anil for adding new validation code on 01-10-2025
      // if ($("#isNRI").is(":checked")) {
      //   const c = $("#IndPassportNumber"),
      //     f = $("#IndPassportNumberError"),
      //     I = c.val().trim();
      //   "" === I
      //     ? (c.addClass("required"),
      //       f.text("This field is required").show(),
      //       (r = !1),
      //       null === e && (e = c))
      //     : 12 !== I.length
      //     ? (c.addClass("required"),
      //       f.text("Passport must be 12 characters maximum").show(),
      //       (r = !1),
      //       null === e && (e = c))
      //     : (c.removeClass("required"), f.hide());

      //   const m = $("#IndOci"),
      //     v = $("#IndOciError"),
      //     E = m.val().trim();

      //   // Removed required validation, only keep length validation
      //   if (E !== "" && 7 !== E.length) {
      //     m.addClass("required"),
      //       v.text("OCI card number must be 7 characters maximum").show(),
      //       (r = !1),
      //       null === e && (e = m);
      //   } else {
      //     m.removeClass("required"), v.hide();
      //   }
      /* if ($("#isNRI").is(":checked")) {
       
        const c = $("#IndPassportNumber"),
          f = $("#IndPassportNumberError"),
          I = c.val().trim();

        if (I === "") {
          c.addClass("required");
          f.text("This field is required").show();
          r = !1;
          if (e === null) e = c;
        } else if (!/^[A-Za-z0-9]+$/.test(I)) {
          c.addClass("required");
          f.text("Passport number must be alphanumeric").show();
          r = !1;
          if (e === null) e = c;
        } else if (I.length > 9) {
          c.addClass("required");
          f.text("Passport number must be maximum 9 characters").show();
          r = !1;
          if (e === null) e = c;
        } else {
          c.removeClass("required");
          f.hide();
        }

       
        const m = $("#IndOci"),
          v = $("#IndOciError"),
          E = m.val().trim();

        if (E !== "") {
          if (E.length !== 8) {
            m.addClass("required");
            v.text("OCI card number must be exactly 8 characters").show();
            r = !1;
            if (e === null) e = m;
          } else if (!/^[A-Za-z]/.test(E.charAt(0))) {
            m.addClass("required");
            v.text("First character must be alphabetic").show();
            r = !1;
            if (e === null) e = m;
          } else if (!/^[0-9]{7}$/.test(E.substring(1))) {
            m.addClass("required");
            v.text("Last 7 characters must be digits").show();
            r = !1;
            if (e === null) e = m;
          } else {
            m.removeClass("required");
            v.hide();
          }
        } else {
          m.removeClass("required");
          v.hide();
        }

        
      } else {
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
      } */
      if ($("#isIndian").is(":checked")) {
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
      } else {
        // anil nri validation
      }

      const C = $("#commAddress"),
        q = $("#IndCommAddressError"),
        b = C.val().trim(),
        y = /^[a-zA-Z0-9\s,#.\-()/]*$/;
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
                  "Only letters, digits, hyphen (-), comma (,), dot (.), hash (#), parenthesis ( ), forward slash (/), and spaces are allowed"
                )
                .show(),
              (r = !1),
              null === e && (e = C));

      if ($("#Yes").is(":checked")) {
        ($("#isPropertyFlat").is(":checked")
          ? [
            { id: "#localityFill", errorId: "#localityFillError" },
            // { id: "#blocknoInvFill", errorId: "#blocknoInvFillError" },
            { id: "#plotnoInvFill", errorId: "#plotnoInvFillError" },
            { id: "#landUseInvFill", errorId: "#landUseInvFillError" },
            {
              id: "#landUseSubtypeInvFill",
              errorId: "#landUseSubtypeInvFillError",
            },
            /* {
              id: "#flat_no_after_Checked_Address_notfound",
              errorId: "#flat_no_after_Checked_Address_notfoundError",
            }, */
          ]
          : [
            { id: "#localityFill", errorId: "#localityFillError" },
            // { id: "#blocknoInvFill", errorId: "#blocknoInvFillError" },
            { id: "#plotnoInvFill", errorId: "#plotnoInvFillError" },
            { id: "#landUseInvFill", errorId: "#landUseInvFillError" },
            {
              id: "#landUseSubtypeInvFill",
              errorId: "#landUseSubtypeInvFillError",
            },
            {
              id: "#landTypeInvFill",
              errorId: "#landTypeInvFillError",
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
          { id: "#landTypeInv", errorId: "#landTypeInvError" },
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
              o("#flat_no_rec_not_found", "#flat_no_rec_not_foundError", !0),
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
      // const g = [
      //   { id: "#IndSaleDeed", errorId: "#IndSaleDeedError" },
      //   { id: "#IndBuildAgree", errorId: "#IndBuildAgreeError" },
      //   { id: "#IndSubMut", errorId: "#IndSubMutError" },
      //   { id: "#IndOther", errorId: "#IndOtherError" },
      // ];
      // let x = !0,
      //   A = null,
      //   k = !1;
      // g.forEach(function (e) {
      //   const r = $(e.id),
      //     d = $(e.errorId);
      //   r.removeClass("required"),
      //     d.hide(),
      //     r.on("change", function () {
      //       const e = r[0].files;
      //       if (e.length > 0) {
      //         let l = !0;
      //         for (let r = 0; r < e.length; r++) {
      //           if (!e[r].name.endsWith(".pdf")) {
      //             l = !1;
      //             break;
      //           }
      //         }
      //         l
      //           ? (r.removeClass("required"), d.hide())
      //           : (r.addClass("required"),
      //             d.text("Only PDF files are allowed").show(),
      //             (x = !1),
      //             null === A && (A = r));
      //       }
      //     });
      // }),
      //   g.forEach(function (e) {
      //     const r = $(e.id),
      //       d = $(e.errorId),
      //       l = r[0].files;
      //     if (l.length > 0) {
      //       k = !0;
      //       let e = !0;
      //       for (let r = 0; r < l.length; r++) {
      //         if (!l[r].name.endsWith(".pdf")) {
      //           e = !1;
      //           break;
      //         }
      //       }
      //       e ||
      //         (r.addClass("required"),
      //         d.text("Only PDF files are allowed").show(),
      //         (x = !1),
      //         null === A && (A = r));
      //     }
      //   }),
      //   k ||
      //     g.forEach(function (e) {
      //       const r = $(e.id),
      //         d = $(e.errorId);
      //       r.addClass("required"),
      //         d.text("At least one file is required").show(),
      //         (x = !1),
      //         null === A && (A = r);
      //     });
      // A && A.focus();  

      // const g = [
      //   {
      //     id: "#IndSaleDeed",
      //     errorId: "#IndSaleDeedError",
      //     checkbox: "#saleDeedAtorney"
      //   },
      //   {
      //     id: "#IndBuildAgree",
      //     errorId: "#IndBuildAgreeError",
      //     checkbox: "#bbAgreement"
      //   },
      //   {
      //     id: "#IndSubMut",
      //     errorId: "#IndSubMutError",
      //     checkbox: "#subsMutationLetter"
      //   },
      //   {
      //     id: "#IndOther",
      //     errorId: "#IndOtherError",
      //     checkbox: "#diffDoc"
      //   }
      // ];

      // ✅ Step 1: Check if any checkbox is checked
      // let fileSectionValid = true;
      // let fileSectionFirstError = null;
      // const anyChecked = g.some(item => $(item.checkbox).is(":checked"));

      // if (!anyChecked) {
      //   $("#IndChooseOneError").text("At least one file is required").show();
      //   fileSectionValid = false;
      // } else {
      //   $("#IndChooseOneError").hide();
      // }

      // // ✅ Step 2 & 3: Loop over each item
      // g.forEach(function (item) {
      //   const fileInput = $(item.id);
      //   const errorDiv = $(item.errorId);
      //   const checkbox = $(item.checkbox);
      //   const files = fileInput[0].files;

      //   // Initial state cleanup
      //   fileInput.removeClass("required");
      //   errorDiv.hide();

      // ✅ Bind change event to enable/disable input on checkbox toggle
      // checkbox.off("change").on("change", function () {
      //   if ($(this).is(":checked")) {
      //     fileInput.prop("disabled", false);
      //   } else {
      //     fileInput.prop("disabled", true).val("");
      //     errorDiv.hide();
      //     fileInput.removeClass("required");
      //   }

      //   // Also hide #IndChooseOneError if any one is checked
      //   const anyStillChecked = g.some(item => $(item.checkbox).is(":checked"));
      //   if (anyStillChecked) {
      //     $("#IndChooseOneError").hide();
      //   }
      // });

      // ✅ Validate if checkbox is checked
      //   if (checkbox.is(":checked")) {
      //     fileInput.css("display", "block");

      //     if (files.length === 0) {
      //       fileInput.addClass("required");
      //       errorDiv.text("This field is mandatory.").show();
      //       fileSectionValid = false;
      //       if (!fileSectionFirstError) fileSectionFirstError = fileInput;
      //     } else {
      //       let allPdf = true;
      //       for (let i = 0; i < files.length; i++) {
      //         if (!files[i].name.toLowerCase().endsWith(".pdf")) {
      //           allPdf = false;
      //           break;
      //         }
      //       }
      //       if (!allPdf) {
      //         fileInput.addClass("required");
      //         errorDiv.text("Only PDF files are allowed").show();
      //         fileSectionValid = false;
      //         if (!fileSectionFirstError) fileSectionFirstError = fileInput;
      //       }
      //     }
      //   } else {
      //     fileInput.css("display", "none").val("");
      //     errorDiv.hide();
      //   }
      // });
      // if (!fileSectionValid && fileSectionFirstError) {
      //   fileSectionFirstError.focus();
      // }
      // const _ = $("#IndOwnerLess"),
      //   U = $("#IndOwnerLessError");
      // _.removeClass("required"), U.hide();
      // let N = !0,
      //   P = null;
      // function S() {
      //   const e = _[0].files;

      //   //   debugger;

      //   // Case 1: File too large previously
      //   // if (fileTooLargeMap["IndOwnerLess"]) {
      //   //   _.addClass("required");
      //   //   U.text("Maximum allowed size is upto 5 MB.").show();
      //   //   return false;
      //   // }

      //   // Case 2: No file selected
      //   if (!e || e.length === 0) {
      //     _.addClass("required");
      //     U.text("This field is mandatory. Upload a PDF file").show();
      //     return false;
      //   }

      //   // Case 3: Extension check
      //   const file = e[0];
      //   if (!file.name.toLowerCase().endsWith(".pdf")) {
      //     _.addClass("required");
      //     U.text("Only PDF files are allowed").show();
      //     return false;
      //   }

      //   // ✅ All good
      //   _.removeClass("required");
      //   U.hide();
      //   return true;
      // }

      // Change + focus handler
      // _.on("focus", function () {
      //   fileTooLargeMap["IndOwnerLess"] = false;
      // }),

      // _.on("change", function () {
      //   validateFileSize("IndOwnerLess", "IndOwnerLessError", 5); // size
      //   S(); // type and empty check
      // }),
      // S() || ((N = !1), (P = _));
      // P && P.focus();
      // const O = $("#IndLeaseDeed"),
      //   D = $("#IndLeaseDeedError");
      // O.removeClass("required"), D.hide();
      // let T = !0,
      //   L = null;
      // function B() {
      //   const files = O[0].files;

      //   // Case 1: File was too large previously
      //   // if (fileTooLargeMap["IndLeaseDeed"]) {
      //   //   O.addClass("required");
      //   //   D.text("Maximum allowed size is upto 5 MB.").show();
      //   //   return false;
      //   // }

      //   // Case 2: No file selected
      //   if (!files || files.length === 0) {
      //     O.addClass("required");
      //     D.text("This field is mandatory. Upload a PDF file").show();
      //     return false;
      //   }

      //   // Case 3: Check extension
      //   const file = files[0];
      //   if (!file.name.toLowerCase().endsWith(".pdf")) {
      //     O.addClass("required");
      //     D.text("Only PDF files are allowed").show();
      //     return false;
      //   }

      //   // ✅ All good — clear error
      //   O.removeClass("required");
      //   D.hide();
      //   return true;
      // }
      const O = $("#IndLeaseDeed"),
        D = $("#IndLeaseDeedError"),
        MAX_SIZE_MB = 20;

      O.removeClass("required");
      D.hide();

      let isValid = true;   // keep this

      function checkFile(input, errorDiv, fileKey) {
        const files = input[0].files;
        errorDiv.text("").hide();
        input.removeClass("required");

        // EMPTY FILE → show required error
        if (!files || files.length === 0) {
          errorDiv.text("This document is required").show();
          input.addClass("required");
          if (e === null) e = input;   // use existing variable
          return false;
        }

        const file = files[0];

        // Too large from map
        if (fileTooLargeMap[fileKey]) {
          errorDiv.text(`Maximum allowed size is upto ${MAX_SIZE_MB} MB.`).show();
          input.addClass("required");
          if (e === null) e = input;
          return false;
        }

        // Not PDF
        if (!file.name.toLowerCase().endsWith(".pdf")) {
          errorDiv.text("Only PDF files are allowed").show();
          input.addClass("required");
          if (e === null) e = input;
          return false;
        }

        // Size limit
        if (file.size > MAX_SIZE_MB * 1024 * 1024) {
          errorDiv.text(`Maximum allowed size is ${MAX_SIZE_MB} MB`).show();
          input.addClass("required");
          if (e === null) e = input;
          return false;
        }

        return true;
      }

      // Validate only Lease Deed now
      const leaseValid = checkFile(O, D, "IndLeaseDeed");

      // Final validation
      if (!leaseValid) {
        isValid = false;
      }

      if (!isValid) {
        if (e !== null) {
          if (e.is(":hidden")) e.closest(":hidden").show();
          e.focus();
          console.log("Focused on:", e.attr("id"));
        }
        return false;
      }

      // if (!isValid) {
      //   if (ownerValid !== true) {
      //     _.focus();
      //   } else {
      //     O.focus();
      //   }
      //   return false;
      // }
      // O.on("focus", function () {
      //   fileTooLargeMap["IndLeaseDeed"] = false;
      // });


      // if (!beforeSubmitFunction()) {
      //   formIsValid = false;
      // }

      // O.on("change", function () {
      //   B();
      // }),
      // B() || ((T = !1), (L = O));
      // L && L.focus();
      // null !== e && e.focus();
      const M = $("#IndConsent"),
        W = $("#IndConsentError");
      M.is(":checked")
        ? (M.removeClass("required"), W.hide())
        : (M.addClass("required"),
          W.text("You must agree to the terms").show(),
          r = false,
          null === e && (e = M));
      // null !== e && e.focus();
      // ✅ Final focus logic (only once)
      if (!r || !isValid || !formIsValid) {
        if (e !== null) {
          if (e.is(":hidden")) e.closest(":hidden").show();
          e.focus();

          // Special handling for checkbox (like #IndConsent)
          if (e.is(":checkbox")) e[0].scrollIntoView({ behavior: "smooth", block: "center" });

          console.log("Focused on first invalid field:", e.attr("id"));
        }
        return false; // stop form submission
      }
      // null !== e && e.focus();
      // if (eField !== null) eField.focus();
      // return r && x && N && T; commented and add below line to fixe issue in "Is Your Property Flat?" validation by anil on 12-06-2025
      // return formIsValid && r && fileSectionValid && N && T; commented by anil on 07-11-2025
      // return formIsValid && r;
      return formIsValid && r && isValid;
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
  });
  // Final full version of the #OrgsubmitButton validation logic with file input validation added

  // Final full version of the #OrgsubmitButton validation logic with file input validation added

  // Final full version of the #OrgsubmitButton validation logic with file input validation added

  // Final full version of the #OrgsubmitButton validation logic with file input validation added

  // const docGroup = [
  //   {
  //     id: "#OrgSaleDeedDoc",
  //     errorId: "#OrgSaleDeedDocError",
  //     checkbox: "#saleDeedAtorneyOrg"
  //   },
  //   {
  //     id: "#OrgBuildAgreeDoc",
  //     errorId: "#OrgBuildAgreeDocError",
  //     checkbox: "#bbAgreementOrg"
  //   },
  //   {
  //     id: "#OrgSubMutDoc",
  //     errorId: "#OrgSubMutDocError",
  //     checkbox: "#subsMutationLetterOrg"
  //   },
  //   {
  //     id: "#OrgOther",
  //     errorId: "#OrgOtherError",
  //     checkbox: "#diffDocOrg"
  //   }
  // ];

  // // ✅ Show/hide on page load and checkbox change for Org section
  // docGroup.forEach(function (item) {
  //   const fileInput = $(item.id);
  //   const checkbox = $(item.checkbox);
  //   const errorDiv = $(item.errorId);

  //   if (checkbox.is(":checked")) {
  //     fileInput.css("display", "block");
  //   } else {
  //     fileInput.css("display", "none").val("");
  //     errorDiv.hide();
  //     fileInput.removeClass("required");
  //   }

  //   checkbox.off("change.orgInit").on("change.orgInit", function () {
  //     if ($(this).is(":checked")) {
  //       fileInput.css("display", "block");
  //     } else {
  //       fileInput.css("display", "none").val("");
  //       errorDiv.hide();
  //       fileInput.removeClass("required");
  //     }

  //     const anyChecked = docGroup.some(x => $(x.checkbox).is(":checked"));
  //     if (anyChecked) {
  //       $("#OrgChooseOneError").hide();
  //     }
  //   });
  // });

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
      let allValid = true;

      // Basic required fields
      /* const requiredFields = [
        { id: "#OrgName", errorId: "#OrgNameError" },
        { id: "#OrgPAN", errorId: "#OrgPANError" },
        { id: "#orgAddressOrg", errorId: "#orgAddressOrgError" },
        { id: "#OrgNameAuthSign", errorId: "#OrgNameAuthSignError" },
        { id: "#authsignatory_mobile", errorId: "#authsignatory_mobileError" },
        { id: "#emailauthsignatory", errorId: "#emailauthsignatoryError" },
        { id: "#orgAadharAuth", errorId: "#orgAadharAuthError" }
      ]; */

      // const isIndianOrg = $("#isIndianOrg").is(":checked");
      // const baseFieldsOrg = [
      //   { id: "#OrgName", errorId: "#OrgNameError" },
      //   { id: "#OrgPAN", errorId: "#OrgPANError" },
      //   { id: "#orgAddressOrg", errorId: "#orgAddressOrgError" },
      //   { id: "#OrgNameAuthSign", errorId: "#OrgNameAuthSignError" },
      //   { id: "#authsignatory_mobile", errorId: "#authsignatory_mobileError" },
      //   { id: "#emailauthsignatory", errorId: "#emailauthsignatoryError" },
      // ];

      // const conditionalFieldsOrg = isIndianOrg
      //   ? [{ id: "#orgAadharAuth", errorId: "#orgAadharAuthError" }]
      //   : [
      //     // { id: "#IndPassportNumber", errorId: "#IndPassportNumberError" },
      //     // { id: "#IndOci", errorId: "#IndOciError" },
      //   ];

      // const validationArrayOrg = [...baseFieldsOrg, ...conditionalFieldsOrg];

      // requiredFields.forEach(function (field) {
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
      //     if (firstInvalid === null) firstInvalid = $input;
      //   } else {
      //     $input.removeClass("required");
      //     $error.hide();
      //   }
      // });

      // validationArrayOrg.forEach(function (field) {
      //   const $input = $(field.id);
      //   const $error = $(field.errorId);

      //   $input.off("input.org").on("input.org", function () { // ✅ UPDATED: prevent multiple listeners
      //     if ($input.val().trim() !== "") {
      //       $input.removeClass("required");
      //       $error.hide();
      //     }
      //   });

      //   if ($input.val().trim() === "") {
      //     $input.addClass("required");
      //     $error.text("This field is required").show();
      //     allRequiredValid = false;
      //     if (firstInvalid === null) firstInvalid = $input;
      //   } else {
      //     $input.removeClass("required");
      //     $error.hide();
      //   }
      // });
      const isIndianOrg = $("input[name='isIndianOrg']:checked").val() === "1" || $("#isIndianOrgYes").is(":checked");
      const baseFieldsOrg = [
        { id: "#OrgName", errorId: "#OrgNameError" },
        { id: "#OrgPAN", errorId: "#OrgPANError" },
        { id: "#orgAddressOrg", errorId: "#orgAddressOrgError" },
        { id: "#OrgNameAuthSign", errorId: "#OrgNameAuthSignError" },
        { id: "#authsignatory_mobile", errorId: "#authsignatory_mobileError" },
        { id: "#emailauthsignatory", errorId: "#emailauthsignatoryError" },
      ];

      const conditionalFieldsOrg = isIndianOrg
        ? [{ id: "#orgAadharAuth", errorId: "#orgAadharAuthError" }]
        : [
          // { id: "#IndPassportNumber", errorId: "#IndPassportNumberError" },
          // { id: "#IndOci", errorId: "#IndOciError" },
        ];

      const validationArrayOrg = [...baseFieldsOrg, ...conditionalFieldsOrg];

      baseFieldsOrg.forEach(function (field) {
        const $input = $(field.id);
        const $err = $(field.errorId);
        const val = $input.val().trim();

        if (val === "") {
          $input.addClass("required");
          $err.text("This field is required").css("display", "block");
          allValid = false;
          if (!firstInvalid) firstInvalid = $input;
        } else {
          $input.removeClass("required");
          $err.text("").css("display", "none");
        }
      });


      if (isIndianOrg) {
        // --- Aadhaar only for Indian ---
        const $aad = $("#orgAadharAuth"),
          $aadErr = $("#orgAadharAuthError"),
          aadValRaw = $aad.val().trim(),
          aadVal = aadValRaw.replace(/\D/g, ""); // digits only

        if (aadVal === "") {
          $aad.addClass("required");
          $aadErr.text("This field is required").css("display", "block");
          allValid = false;
          if (!firstInvalid) firstInvalid = $aad;
        } else if (!/^\d{12}$/.test(aadVal)) {
          $aad.addClass("required");
          $aadErr.text("Please enter a valid 12-digit Aadhaar number").css("display", "block");
          allValid = false;
          if (!firstInvalid) firstInvalid = $aad;
        } else {
          $aad.removeClass("required");
          $aadErr.text("").css("display", "none");
        }

        // Hide and reset non-Indian fields
        $("#documentTypeOrg").removeClass("required");
        $("#documentTypeOrgError").text("").css("display", "none");
        $("#documentTypeNumberOrg").removeClass("required");
        $("#documentTypeNumberOrgError").text("").css("display", "none");
      } else {
        // --- Non-Indian: Document Type + Number ---
        const $docTypeOrg = $("#documentTypeOrg"),
          $docTypeOrgErr = $("#documentTypeOrgError"),
          docTypeValRaw = $docTypeOrg.val().trim(),
          docTypeVal = (docTypeValRaw || "").toUpperCase();

        const $docNumOrg = $("#documentTypeNumberOrg"),
          $docNumOrgErr = $("#documentTypeNumberOrgError"),
          docNumRaw = $docNumOrg.val().trim(),
          docNumVal = docNumRaw.toUpperCase();

        $docNumOrg.val(docNumVal);

        // Dynamic maxlength setup
        if (["PIO", "PION"].includes(docTypeVal)) {
          $docNumOrg.attr("maxlength", 8);
        } else if (["OCI", "OCIN"].includes(docTypeVal)) {
          $docNumOrg.attr("maxlength", 10);
        } else if (docTypeVal === "PASSPORT") {
          $docNumOrg.attr("maxlength", 12);
        } else {
          $docNumOrg.removeAttr("maxlength");
        }

        // Validate document type selection
        if (!docTypeValRaw) {
          $docTypeOrg.addClass("required");
          $docTypeOrgErr.text("Please select a document type").css("display", "block");
          allValid = false;
          if (!firstInvalid) firstInvalid = $docTypeOrg;
        } else {
          $docTypeOrg.removeClass("required");
          $docTypeOrgErr.text("").css("display", "none");
        }

        // Validate document number
        if (docNumVal === "") {
          $docNumOrg.addClass("required");
          $docNumOrgErr.text("This field is required").css("display", "block");
          allValid = false;
          if (!firstInvalid) firstInvalid = $docNumOrg;
        } else {
          let regex = null,
            errMsg = "";
          if (["PIO", "PION"].includes(docTypeVal)) {
            regex = /^[A-Z]\d{7}$/;
            errMsg = "Invalid PIO/PION Number (1 letter + 7 digits)";
          } else if (["OCI", "OCIN"].includes(docTypeVal)) {
            regex = /^[A-Z]\d{9}$/;
            errMsg = "Invalid OCI/OCIN Number (1 letter + 9 digits)";
          } else if (docTypeVal === "PASSPORT") {
            regex = /^[A-Z0-9]{6,12}$/;
            errMsg = "Invalid Passport Number (6-12 alphanumeric characters)";
          }

          if (regex && !regex.test(docNumVal)) {
            $docNumOrg.addClass("required");
            $docNumOrgErr.text(errMsg).css("display", "block");
            allValid = false;
            if (!firstInvalid) firstInvalid = $docNumOrg;
          } else {
            $docNumOrg.removeClass("required");
            $docNumOrgErr.text("").css("display", "none");
          }
        }

        // Reset Aadhaar field when non-Indian
        $("#orgAadharAuth").removeClass("required");
        $("#orgAadharAuthError").text("").css("display", "none");
      }


      validationArrayOrg.forEach(function (field) {
        const $input = $(field.id);
        const $error = $(field.errorId);

        $input.off("input.org").on("input.org", function () {
          if ($input.val().trim() !== "") {
            $input.removeClass("required");
            $error.text("").css("display", "none");
          }
        });
      });

      // Mobile Validation
      const $mobile = $("#authsignatory_mobile");
      const $mobileError = $("#OrgMobileAuthError");
      const mobileVal = $mobile.val().trim();
      const mobileVerified = $mobile.attr("data-id");

      // if (mobileVal !== "") {
      //   if (mobileVal.length !== 10) {
      //     $mobileError.text("Mobile Number must be exactly 10 digit").show();
      //     allRequiredValid = false;
      //     if (firstInvalid === null) firstInvalid = $mobile;
      //   } else if (mobileVerified === "0") {
      //     $mobileError.text("Please verify your mobile number").show();
      //     allRequiredValid = false;
      //     if (firstInvalid === null) firstInvalid = $mobile;
      //   } else {
      //     $mobileError.hide();
      //   }
      // } 

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
        } else if (!panRegex.test(panVal)) {
          // ➕ OPTIONAL
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
      // const $aadhaar = $("#orgAadharAuth");
      // const $aadhaarError = $("#orgAadharAuthError");
      // const aadhaarVal = $aadhaar.val().trim();
      // const aadhaarRegex = /^\d{12}$/; // ➕ OPTIONAL

      // if (aadhaarVal !== "") {
      //   if (aadhaarVal.length !== 12 || !aadhaarRegex.test(aadhaarVal)) {
      //     $aadhaar.addClass("required");
      //     $aadhaarError.text("Aadhar Number must be exactly 12 digits").show();
      //     allRequiredValid = false;
      //     if (firstInvalid === null) firstInvalid = $aadhaar;
      //   } else {
      //     $aadhaar.removeClass("required");
      //     $aadhaarError.hide();
      //   }
      // }
      if ($("#isIndianOrg").is(":checked")) {
        // Aadhaar Validation
        const $aadhaar = $("#orgAadharAuth");
        const $aadhaarError = $("#orgAadharAuthError");
        const aadhaarVal = $aadhaar.val().trim();
        const aadhaarRegex = /^\d{12}$/; // ➕ OPTIONAL

        if (aadhaarVal !== "") {
          if (aadhaarVal.length !== 12 || !aadhaarRegex.test(aadhaarVal)) {
            $aadhaar.addClass("required");
            $aadhaarError
              .text("Aadhar Number must be exactly 12 digits")
              .show();
            allRequiredValid = false;
            if (firstInvalid === null) firstInvalid = $aadhaar;
          } else {
            $aadhaar.removeClass("required");
            $aadhaarError.hide();
          }
        }
      } else {
        //Validation for Document Type for Foreign National
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
            {
              id: "#landUseSubtypeOrgFill",
              errorId: "#landUseSubtypeOrgFillError",
            },
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
            {
              id: "#landUseSubtypeOrgFill",
              errorId: "#landUseSubtypeOrgFillError",
            },
            {
              id: "#landTypeOrgFill",
              errorId: "#landTypeOrgFillError",
            },
          ]
        : [
          { id: "#locality_org", errorId: "#locality_orgError" },
          { id: "#block_org", errorId: "#block_orgError" },
          { id: "#plot_org", errorId: "#plot_orgError" },
          { id: "#landUse_org", errorId: "#landUse_orgError" },
          { id: "#landUseSubtype_org", errorId: "#landUseSubtype_orgError" },
          { id: "#landTypeOrg", errorId: "#landTypeOrgError" },
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

      //     const docGroup = [
      //   {
      //     id: "#OrgSaleDeedDoc",
      //     errorId: "#OrgSaleDeedDocError",
      //     checkbox: "#saleDeedAtorneyOrg"
      //   },
      //   {
      //     id: "#OrgBuildAgreeDoc",
      //     errorId: "#OrgBuildAgreeDocError",
      //     checkbox: "#bbAgreementOrg"
      //   },
      //   {
      //     id: "#OrgSubMutDoc",
      //     errorId: "#OrgSubMutDocError",
      //     checkbox: "#subsMutationLetterOrg"
      //   },
      //   {
      //     id: "#OrgOther",
      //     errorId: "#OrgOtherError",
      //     checkbox: "#diffDocOrg"
      //   }
      // ];

      // ✅ Step 1: Check if any checkbox is checked
      // let orgFileSectionValid = true;
      // let orgFileSectionFirstError = null;
      // const anyOrgChecked = docGroup.some(item => $(item.checkbox).is(":checked"));

      // if (!anyOrgChecked) {
      //   $("#OrgChooseOneError").text("At least one file is required").show();
      //   orgFileSectionValid = false;
      // } else {
      //   $("#OrgChooseOneError").hide();
      // }

      // ✅ Step 2 & 3: Loop over each item
      // docGroup.forEach(function (item) {
      //   const fileInput = $(item.id);
      //   const errorDiv = $(item.errorId);
      //   const checkbox = $(item.checkbox);
      //   const files = fileInput[0].files;

      //   // Initial cleanup
      //   fileInput.removeClass("required");
      //   errorDiv.hide();

      // ✅ Bind change event to enable/disable input
      // checkbox.off("change").on("change", function () {
      //   if ($(this).is(":checked")) {
      //     fileInput.prop("disabled", false);
      //   } else {
      //     fileInput.prop("disabled", true).val("");
      //     errorDiv.hide();
      //     fileInput.removeClass("required");
      //   }

      //   // Hide general error if any one is now checked
      //   const anyStillChecked = docGroup.some(item => $(item.checkbox).is(":checked"));
      //   if (anyStillChecked) {
      //     $("#OrgChooseOneError").hide();
      //   }
      // });

      // ✅ Validate if checkbox is checked
      // if (checkbox.is(":checked")) {
      //   fileInput.css("display", "block");

      //   if (files.length === 0) {
      //     fileInput.addClass("required");
      //     errorDiv.text("This field is mandatory.").show();
      //     orgFileSectionValid = false;
      //     if (!orgFileSectionFirstError) orgFileSectionFirstError = fileInput;
      //   } else {
      //     let allPdf = true;
      //     for (let i = 0; i < files.length; i++) {
      //       if (!files[i].name.toLowerCase().endsWith(".pdf")) {
      //         allPdf = false;
      //         break;
      //       }
      //     }
      //     if (!allPdf) {
      //       fileInput.addClass("required");
      //       errorDiv.text("Only PDF files are allowed").show();
      //       orgFileSectionValid = false;
      //       if (!orgFileSectionFirstError) orgFileSectionFirstError = fileInput;
      //     }
      //   }
      // } else {
      //   fileInput.css("display", "none").val("");
      //   errorDiv.hide();
      // }
      // });

      // // ✅ Focus on first invalid input if needed
      // if (!orgFileSectionValid && orgFileSectionFirstError) {
      //   orgFileSectionFirstError.focus();
      // }
      // Other required file inputs with PDF check
      const otherFiles = [
        { id: "#OrgSignAuthDoc", errorId: "#OrgSignAuthDocError" },
        // { id: "#scannedIDOrg", errorId: "#scannedIDOrgError" },
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
      return (
        allRequiredValid &&
        fileValidationPassed &&
        formIsValid
        // orgFileSectionValid
      );
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
