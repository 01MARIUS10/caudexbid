<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>caudexbid API — Swagger</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.17.14/swagger-ui.css">
    <style>body { margin: 0; background: #fafafa; }</style>
</head>
<body>
<div id="swagger-ui"></div>

<script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.17.14/swagger-ui-bundle.js"></script>
<script>
    const ui = SwaggerUIBundle({
        url: @json(route('docs.spec')),
        dom_id: '#swagger-ui',
        deepLinking: true,
        persistAuthorization: true,
        tryItOutEnabled: true,
        displayRequestDuration: true,
        filter: true,
        docExpansion: 'none',
        requestInterceptor: (req) => {
            req.headers['Accept'] = 'application/json';
            return req;
        },
        // Après un login réussi, le jeton est appliqué automatiquement au bouton « Authorize ».
        responseInterceptor: (res) => {
            try {
                if (res.ok && /\/api\/auth\/login$/.test(new URL(res.url, location.href).pathname)) {
                    const body = typeof res.body === 'object' ? res.body : JSON.parse(res.text);
                    if (body && body.token) {
                        ui.preauthorizeApiKey('bearerAuth', body.token);
                    }
                }
            } catch (e) { /* réponse non JSON : on ignore */ }
            return res;
        },
    });
</script>
</body>
</html>
