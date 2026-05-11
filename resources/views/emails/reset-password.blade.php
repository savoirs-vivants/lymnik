<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe — Lymnik</title>
    <style>
        body { margin: 0; padding: 0; background: #0f172a; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 520px; margin: 0 auto; padding: 40px 20px; }
        .card { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 40px; }
        .logo { font-size: 18px; font-weight: 700; color: #ffffff; letter-spacing: -0.5px; margin-bottom: 32px; }
        .icon { width: 52px; height: 52px; background: rgba(255,255,255,0.08); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        h1 { font-size: 22px; font-weight: 700; color: #ffffff; margin: 0 0 8px 0; }
        p { font-size: 14px; color: #94a3b8; line-height: 1.6; margin: 0 0 24px 0; }
        .btn { display: block; background: linear-gradient(135deg, #3b82f6, #6366f1); color: #ffffff !important; text-decoration: none; font-size: 14px; font-weight: 600; text-align: center; padding: 14px 24px; border-radius: 12px; margin-bottom: 24px; }
        .divider { border: none; border-top: 1px solid rgba(255,255,255,0.06); margin: 24px 0; }
        .link-fallback { word-break: break-all; font-size: 11px; color: #475569; font-family: 'Courier New', monospace; }
        .footer { text-align: center; font-size: 11px; color: #334155; margin-top: 24px; }
        .expiry { font-size: 12px; color: #f97316; background: rgba(249,115,22,0.08); border: 1px solid rgba(249,115,22,0.2); border-radius: 8px; padding: 10px 14px; margin-bottom: 24px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="logo">Lymnik</div>

            <h1>Réinitialiser votre mot de passe</h1>
            <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.</p>

            <a href="{{ $url }}" class="btn">
                Réinitialiser mon mot de passe →
            </a>

            <div class="expiry">
                ⏱ Ce lien expire dans {{ config('auth.passwords.users.expire', 60) }} minutes.
            </div>

            <p style="margin-bottom:8px">Si vous n'avez pas demandé de réinitialisation, ignorez cet e-mail — votre mot de passe restera inchangé.</p>

            <hr class="divider">

            <p style="margin-bottom:4px;font-size:12px">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :</p>
            <p class="link-fallback">{{ $url }}</p>
        </div>

        <div class="footer">
            © {{ date('Y') }} Lymnik — Suivi de la qualité de l'eau
        </div>
    </div>
</body>
</html>
