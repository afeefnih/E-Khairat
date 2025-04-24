<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $language === 'ms' ? 'Terma dan Syarat BKKMTS' : 'BKKMTS Terms and Conditions' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.6;
        }
        h1 {
            color: #1e40af;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            color: #1e3a8a;
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        p {
            margin-bottom: 10px;
        }
        ul {
            margin-bottom: 15px;
        }
        li {
            margin-bottom: 8px;
        }
        .section {
            margin-bottom: 25px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .exception-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h1>{{ $language === 'ms' ? 'Terma dan Syarat BKKMTS' : 'BKKMTS Terms and Conditions' }}</h1>

    <!-- Section 1: Tanggungan Ahli -->
    <div class="section">
        <h2>1. {{ $language === 'ms' ? 'Tanggungan Ahli' : 'Member Dependents' }}</h2>
        @if ($language === 'ms')
            <p>Tanggungan merujuk kepada individu-individu berikut yang tinggal serumah dengan ahli:</p>
            <ul>
                <li>Isteri atau suami ahli.</li>
                <li>Anak-anak yang belum berkahwin.</li>
                <li>Ibubapa ahli (ibu dan bapa kandung).</li>
                <li>Ibu bapa mertua ahli (ibu dan bapa pasangan).</li>
            </ul>
            <div class="exception-box">
                <h3>Pengecualian:</h3>
                <ul>
                    <li>Anak yang sudah berumah tangga dan tinggal serumah <strong>tidak dianggap sebagai tanggungan</strong> dan perlu mendaftar sebagai ahli secara berasingan.</li>
                    <li>Ibubapa atau ibu bapa mertua yang <strong>tidak tinggal serumah</strong> dengan ahli <strong>tidak dikira sebagai tanggungan</strong>.</li>
                </ul>
            </div>
        @else
            <p>Dependents refer to the following individuals living in the same house as the member:</p>
            <ul>
                <li>Member's spouse.</li>
                <li>Unmarried children.</li>
                <li>Member's parents (biological parents).</li>
                <li>Member's parents-in-law (spouse's parents).</li>
            </ul>
            <div class="exception-box">
                <h3>Exceptions:</h3>
                <ul>
                    <li>Married children living in the same house are <strong>not considered as dependents</strong> and must register as separate members.</li>
                    <li>Parents or parents-in-law who <strong>do not live in the same house</strong> with the member <strong>are not counted as dependents</strong>.</li>
                </ul>
            </div>
        @endif
    </div>

    <!-- Section 2: Sumbangan Keahlian -->
    <div class="section">
        <h2>2. {{ $language === 'ms' ? 'Sumbangan Keahlian' : 'Membership Contribution' }}</h2>
        <ul>
            @if ($language === 'ms')
                <li>Setiap ahli dikehendaki membayar <strong>sumbangan sebanyak RM{{ number_format($amount, 2) }} SEKALI SAHAJA</strong> semasa mendaftar sebagai ahli BKKMTS.</li>
                <li>Sumbangan ini adalah <strong>tidak tetap</strong> dan mungkin akan <strong>dikutip semula</strong> sekiranya dana atau wang yang dikumpulkan oleh BKKMTS mengalami pengurangan atau susut nilai.</li>
                <li>Ahli yang ingin memberikan sumbangan tambahan adalah <strong>amat digalakkan dan dihargai</strong>. Sumbangan tambahan ini akan membantu memperkukuhkan dana BKKMTS untuk manfaat bersama.</li>
            @else
                <li>Each member is required to pay a <strong>contribution of RM{{ number_format($amount, 2) }} ONCE ONLY</strong> when registering as a BKKMTS member.</li>
                <li>This contribution is <strong>not fixed</strong> and may be <strong>collected again</strong> if the funds collected by BKKMTS experience a reduction or depreciation.</li>
                <li>Members who wish to make additional contributions are <strong>highly encouraged and appreciated</strong>. These additional contributions will help strengthen BKKMTS funds for mutual benefit.</li>
            @endif
        </ul>
    </div>

    <!-- Continue with sections 3, 4, and 5 similarly -->
    <!-- Section 3: Nota Penting -->
    <div class="section">
        <h2>3. {{ $language === 'ms' ? 'Nota Penting' : 'Important Notes' }}</h2>
        <ul>
            @if ($language === 'ms')
                <li>Semua maklumat yang diberikan semasa pendaftaran mestilah <strong>tepat dan benar</strong>.</li>
                <li>Ahli bertanggungjawab untuk memaklumkan sebarang perubahan dalam maklumat tanggungan (contoh: perkahwinan, perpindahan, atau kematian) kepada pihak BKKMTS.</li>
                <li>Pihak BKKMTS berhak untuk membuat semakan dan pengesahan terhadap maklumat yang diberikan oleh ahli.</li>
            @else
                <li>All information provided during registration must be <strong>accurate and true</strong>.</li>
                <li>Members are responsible for notifying any changes in dependent information (e.g., marriage, relocation, or death) to BKKMTS.</li>
                <li>BKKMTS reserves the right to review and verify information provided by members.</li>
            @endif
        </ul>
    </div>

    <!-- Section 4: Hak dan Tanggungjawab Ahli -->
    <div class="section">
        <h2>4. {{ $language === 'ms' ? 'Hak dan Tanggungjawab Ahli' : 'Rights and Responsibilities of Members' }}</h2>
        <ul>
            @if ($language === 'ms')
                <li>Ahli berhak menerima manfaat dan perkhidmatan yang disediakan oleh BKKMTS mengikut syarat-syarat yang ditetapkan.</li>
                <li>Ahli bertanggungjawab untuk mematuhi semua peraturan dan syarat yang digariskan oleh BKKMTS.</li>
            @else
                <li>Members are entitled to receive benefits and services provided by BKKMTS in accordance with the established conditions.</li>
                <li>Members are responsible for complying with all rules and conditions outlined by BKKMTS.</li>
            @endif
        </ul>
    </div>

    <!-- Section 5: Perubahan Syarat -->
    <div class="section">
        <h2>5. {{ $language === 'ms' ? 'Perubahan Syarat' : 'Changes in Terms' }}</h2>
        <ul>
            @if ($language === 'ms')
                <li>Pihak BKKMTS berhak untuk mengubah atau meminda syarat-syarat ini pada bila-bila masa tanpa notis terlebih dahulu.</li>
                <li>Ahli akan dimaklumkan mengenai sebarang perubahan melalui saluran komunikasi yang disediakan.</li>
            @else
                <li>BKKMTS reserves the right to change or amend these terms at any time without prior notice.</li>
                <li>Members will be notified of any changes through the provided communication channels.</li>
            @endif
        </ul>
    </div>

    <div class="footer">
        @if ($language === 'ms')
            Dengan menggunakan perkhidmatan ini, anda bersetuju untuk mematuhi syarat-syarat dan peraturan yang telah ditetapkan oleh BKKMTS.
            <br><br>
            Versi 1.0 - Dikemas kini pada 27 Mar 2025
        @else
            By using this service, you agree to comply with the terms and conditions set by BKKMTS.
            <br><br>
            Version 1.0 - Updated on 27 Mar 2025
        @endif
    </div>
</body>
</html>
