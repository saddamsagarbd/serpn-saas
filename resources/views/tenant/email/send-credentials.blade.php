<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>User Credentials</title>
        <style>
        /* -------------------------------------
            GLOBAL RESETS
        ------------------------------------- */
        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%; 
        }

        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%; 
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%; 
        }

        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top; 
        }

        /* -------------------------------------
            BODY & CONTAINER
        ------------------------------------- */
        .body {
            background-color: #f6f6f6;
            width: 100%; 
        }

        .container {
            display: block;
            margin: 0 auto !important;
            max-width: 580px;
            padding: 10px;
            width: 580px; 
        }

        .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 580px;
            padding: 10px; 
        }

        /* -------------------------------------
            HEADER, FOOTER, MAIN
        ------------------------------------- */
        .main {
            background: #ffffff;
            border-radius: 3px;
            width: 100%; 
        }

        .wrapper {
            box-sizing: border-box;
            padding: 20px; 
        }

        .footer {
            clear: both;
            margin-top: 10px;
            text-align: center;
            width: 100%; 
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #999999;
            font-size: 12px;
            text-align: center; 
        }

        /* -------------------------------------
            TYPOGRAPHY & CREDENTIAL CARD
        ------------------------------------- */
        h1, h2, h3, h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 20px; 
        }

        p, ul, ol {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px; 
        }

        a {
            color: #3498db;
            text-decoration: underline; 
        }

        .credentials-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px 20px;
            margin: 20px 0;
        }

        .credentials-card p {
            margin-bottom: 8px;
        }

        .credentials-card p:last-child {
            margin-bottom: 0;
        }

        /* -------------------------------------
            BUTTONS
        ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%; 
        }

        .btn > tbody > tr > td {
            padding-bottom: 15px; 
        }

        .btn table {
            width: auto; 
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center; 
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 10px 24px;
            text-decoration: none;
            text-transform: capitalize; 
        }

        .btn-primary table td {
            background-color: #3498db; 
        }

        .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff; 
        }

        .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0; 
        }

        /* -------------------------------------
            RESPONSIVE AND MOBILE FRIENDLY
        ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table.body h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important; 
            }
            table.body p, table.body td, table.body a {
                font-size: 16px !important; 
            }
            table.body .wrapper {
                padding: 15px !important; 
            }
            table.body .container {
                padding: 0 !important;
                width: 100% !important; 
            }
            table.body .btn table {
                width: 100% !important; 
            }
            table.body .btn a {
                width: 100% !important; 
            }
        }

        @media all {
            .btn-primary table td:hover {
                background-color: #2980b9 !important; 
            }
            .btn-primary a:hover {
                background-color: #2980b9 !important;
                border-color: #2980b9 !important; 
            } 
        }
        </style>
    </head>
    <body>
        <span class="preheader">Your access credentials for {{ $tenant->name ?? 'our platform' }}</span>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
            <tr>
                <td>&nbsp;</td>
                <td class="container">
                    <div class="content">

                        <!-- START CENTERED WHITE CONTAINER -->
                        <table role="presentation" class="main">

                            <!-- START MAIN CONTENT AREA -->
                            <tr>
                                <td class="wrapper">
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td>
                                                <p>Dear {{ ucfirst($user->name ?? 'User') }},</p>
                                                <p>You have been successfully registered as a user on our system. Your access credentials are provided below. Please change your password after your first login.</p>
                                                
                                                <div class="credentials-card">
                                                    <p><strong>Username / Email:</strong> {{ $user->email }}</p>
                                                    <p><strong>Password:</strong> <code>{{ $password }}</code></p>
                                                </div>

                                                @if(!empty($loginUrl))
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                    <tbody>
                                                        <tr>
                                                            <td align="left">
                                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <a href="{{ $loginUrl }}" target="_blank">Login to Your Account</a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @endif

                                                <p><strong>Note:</strong> This is a system-generated email. Please do not reply directly to this message.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- END MAIN CONTENT AREA -->

                        </table>
                        <!-- END CENTERED WHITE CONTAINER -->

                        <!-- START FOOTER -->
                        <div class="footer">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="content-block">
                                        <span class="apple-link">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- END FOOTER -->

                    </div>
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </body>
</html>