<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo') | Vênus</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            background-color: #fdf4f6;
            font-family: 'Poppins', Arial, sans-serif;
            color: #4a3037;
        }

        .email-container { width: 100%; padding: 40px 20px; }

        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(158, 24, 43, 0.10);
        }

        .header {
            background: linear-gradient(135deg, #f9cbd6, #f2a9bc);
            padding: 35px 30px;
            text-align: center;
        }

        .header h1 { margin: 0; color: #9e182b; font-size: 25px; font-weight: 700; }

        .content { padding: 40px 35px; text-align: center; }

        .content h2 { margin: 0 0 15px; color: #4a3037; font-size: 21px; font-weight: 600; }

        .content p { margin: 12px 0; color: #6f5a60; font-size: 14px; line-height: 1.7; }

        .button-container { margin: 30px 0; }

        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #9e182b;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
        }

        .box {
            margin: 22px 0;
            padding: 16px 18px;
            background-color: #fdf0f3;
            border-radius: 10px;
            text-align: left;
        }

        .box strong { display: block; margin-bottom: 6px; color: #9e182b; font-size: 13px; }

        .box p { margin: 0; color: #7d4b55; font-size: 14px; }

        .box ul { margin: 0; padding-left: 20px; color: #7d4b55; font-size: 14px; line-height: 1.8; }

        .warning {
            margin-top: 25px !important;
            padding: 15px;
            background-color: #fdf0f3;
            border-radius: 10px;
            color: #7d4b55 !important;
            font-size: 13px !important;
        }

        .footer {
            padding: 22px 30px;
            background-color: #faf7f8;
            text-align: center;
            border-top: 1px solid #f1e3e6;
        }

        .footer p { margin: 0; color: #927d83; font-size: 12px; line-height: 1.6; }

        @media (max-width: 600px) {
            .email-container { padding: 20px 10px; }
            .content { padding: 30px 22px; }
            .header { padding: 30px 20px; }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-card">

            <div class="header">
                <h1>@yield('titulo')</h1>
            </div>

            <div class="content">
                @yield('conteudo')
            </div>

            <div class="footer">
                <p>Este é um e-mail automático. Por favor, não responda.</p>
                <p>© {{ date('Y') }} Vênus — Saúde da Mulher</p>
            </div>

        </div>
    </div>
</body>

</html>
