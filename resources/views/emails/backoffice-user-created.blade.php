<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos identifiants Lymnik</title>
    <style>
        body { margin: 0; padding: 0; background: #0f172a; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 520px; margin: 0 auto; padding: 40px 20px; }
        .card { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 40px; }
        .logo { font-size: 18px; font-weight: 700; color: #ffffff; letter-spacing: -0.5px; margin-bottom: 32px; }
        .icon { width: 52px; height: 52px; background: rgba(34,42,96,0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        h1 { font-size: 22px; font-weight: 700; color: #ffffff; margin: 0 0 8px 0; }
        p { font-size: 14px; color: #94a3b8; line-height: 1.6; margin: 0 0 24px 0; }
        .credentials { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 20px 24px; margin-bottom: 24px; }
        .cred-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .cred-row:last-child { border-bottom: none; padding-bottom: 0; }
        .cred-label { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; width: 90px; shrink: 0; }
        .cred-value { font-size: 14px; color: #e2e8f0; font-family: 'Courier New', monospace; font-weight: 600; word-break: break-all; }
        .btn { display: block; background: linear-gradient(135deg, #222a60, #1a7fc4); color: #ffffff !important; text-decoration: none; font-size: 14px; font-weight: 600; text-align: center; padding: 14px 24px; border-radius: 12px; margin-bottom: 24px; }
        .divider { border: none; border-top: 1px solid rgba(255,255,255,0.06); margin: 24px 0; }
        .notice { font-size: 12px; color: #f97316; background: rgba(249,115,22,0.08); border: 1px solid rgba(249,115,22,0.2); border-radius: 8px; padding: 10px 14px; margin-bottom: 24px; }
        .footer { text-align: center; font-size: 11px; color: #334155; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="logo">Lymnik</div>

            <h1>Bienvenue sur Lymnik, {{ $user->firstname }} !</h1>
            <p>Un compte a été créé pour vous sur la plateforme de suivi de la qualité de l'eau. Voici vos identifiants de connexion :</p>

            <div class="credentials">
                <div class="cred-row">
                    <span class="cred-label">Prénom</span>
                    <span class="cred-value">{{ $user->firstname }} {{ $user->name }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Email</span>
                    <span class="cred-value">{{ $user->email }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Mot de passe</span>
                    <span class="cred-value">{{ $plainPassword }}</span>
                </div>
            </div>

            <a href="{{ route('login') }}" class="btn">
                Se connecter à Lymnik →
            </a>

            <div class="notice">
                🔒 Pour votre sécurité, pensez à modifier votre mot de passe après votre première connexion.
            </div>

            <hr class="divider">

            <p style="margin:0;font-size:12px;">Si vous n'étiez pas attendu sur cette plateforme, ignorez cet e-mail ou contactez votre administrateur.</p>
        </div>

        <div class="footer">
            © {{ date('Y') }} Lymnik — Suivi de la qualité de l'eau
        </div>
    </div>
</body>
</html>
