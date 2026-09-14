const enKey = "aa11ss22dd33ff44gg55hh66jj77kk88";
const enIv = "a1s2d3f4g5h6j7k8";

function encryptString(text) {
  const key = CryptoJS.enc.Utf8.parse(enKey); // 32-byte key
  const iv = CryptoJS.enc.Utf8.parse(enIv); // 16-byte IV

  const encrypted = CryptoJS.AES.encrypt(text, key, {
    iv: iv,
    mode: CryptoJS.mode.CBC,
    padding: CryptoJS.pad.Pkcs7,
  });

  return CryptoJS.enc.Base64.stringify(encrypted.ciphertext);
}

function decryptString(str) {
  if (!str || str.length === 0) {
    return str;
  }

  let decodedBase64;
  try {
    // Decode base64 to a WordArray (CryptoJS object)
    decodedBase64 = CryptoJS.enc.Base64.parse(str);
  } catch (e) {
    return str; // invalid base64
  }

  // Assuming enKey and enIv are strings
  const key = CryptoJS.enc.Utf8.parse(enKey.padEnd(32, " ")); // pad to 32 chars
  const iv = CryptoJS.enc.Utf8.parse(enIv.padEnd(16, " ")); // pad to 16 chars

  try {
    const decrypted = CryptoJS.AES.decrypt({ ciphertext: decodedBase64 }, key, {
      iv: iv,
      mode: CryptoJS.mode.CBC,
      padding: CryptoJS.pad.Pkcs7,
    });

    return decrypted.toString(CryptoJS.enc.Utf8);
  } catch (err) {
    return str; // decryption failed
  }
}