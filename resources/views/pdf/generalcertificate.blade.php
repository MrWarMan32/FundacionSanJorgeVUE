<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificación de Terapias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            width: 90%;
            margin: 0 auto;
            padding: 40px;
            border: 2px solid #000;
            border-radius: 10px;
            text-align: center;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }
        .header img {
            width: 150px;
            margin-right: 20px;
            margin-top: -20px;
        }
        .header-text {
            text-align: center;
            flex-grow: 1;
        }
        .header-text .title {
            margin-top: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .header-text .subtitle {
            font-size: 14px;
            font-style: italic;
            margin-top: 5px;
            text-align: center;
        }
        .content {
            margin-top: 40px;
            font-size: 14px;
            text-align: justify;
            line-height: 1.6;
        }
        .signature {
            margin-top: 90px;
            text-align: left;
            font-size: 14px;
        }
        .signature p {
            margin: 5px 0;
        }
        .signature .name {
            font-weight: bold;
            text-decoration: underline;
        }
        .footer {
            margin-top: 40px;
            font-size: 14px;
            color: #555;
        }
        .small-font {
            font-size: 14px;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    @php
        use Illuminate\Support\Collection;

        $therapyGroups = $shifts->groupBy('id_therapy');
        $smallFont = $therapyGroups->count() > 2;
                
    @endphp

    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <img src="{{ public_path('images/Logo_Fundacion.jpg') }}" alt="Logo Fundación">
            <div class="header-text">
                <div class="title">FUNDACIÓN DE AYUDA MUTUA SAN JORGE</div>
                <div class="subtitle">"Soy un ser especial, Dios no comete errores, Él me ama así"</div>
            </div>
        </div>

        <!-- Contenido del Certificado -->
        <div class="content {{ $smallFont ? 'small-font' : '' }} ">
            <p>
                La <strong>FUNDACIÓN DE AYUDA MUTUA SAN JORGE</strong>, comprometida con el bienestar 
                y desarrollo integral de sus pacientes, extiende el presente certificado a:
            </p>

            <p style="text-align: center; font-size: 18px; font-weight: bold;">
                {{ mb_convert_case($patient->name, MB_CASE_TITLE, 'UTF-8') }} 
                {{ mb_convert_case($patient->last_name, MB_CASE_TITLE, 'UTF-8') }}
            </p>

            <p>
                Portador/a de la cédula de identidad No. <strong>{{ $patient->id_card }}</strong>,
                en el cual certifica que el/la paciente asiste y recibe las siguientes terapias en nuestra fundación:
            </p>

            @foreach ($therapyGroups as $therapyId => $group)
                @php
                    $therapy = $group->first()->therapy;
                    $doctor = $group->first()->doctor;

                    // Contar cuántas veces se repite cada combinación de día + hora
                    $combinations = $group->groupBy(function($shift) {
                        return $shift->appointment->day . '-' . $shift->appointment->start_time . '-' . $shift->appointment->end_time;
                    });

                    // Obtener la combinación más frecuente
                    $mostFrequent = $combinations->sortByDesc(function($items) {
                        return $items->count();
                    })->first()->first();

                    $day = $mostFrequent->appointment->day;
                    $start = \Carbon\Carbon::parse($mostFrequent->appointment->start_time)->format('h:i A');
                    $end = \Carbon\Carbon::parse($mostFrequent->appointment->end_time)->format('h:i A');
                @endphp

                <p>
                    <strong>{{ ucwords(strtolower($therapy->name)) }}</strong>, bajo la supervisión de el/la terapeuta
                    <strong>{{ mb_convert_case(strtolower($doctor->name), MB_CASE_TITLE, 'UTF-8') }}
                    {{ mb_convert_case(strtolower($doctor->last_name), MB_CASE_TITLE, 'UTF-8') }}</strong>,
                    los días {{ $day }} de {{ $start }} a {{ $end }}.
                </p>
            @endforeach
    

            <p>
                Es todo cuanto puedo certificar en honor a la verdad para los fines consiguientes.
            </p>

            <p>
                Este certificado se expide a petición del/de la interesado/a, para los fines que estime convenientes.
            </p>

            <p>
                Portoviejo, {{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
            </p>

            <p>Atentamente,</p>
        </div>

        

        <div class="signature">
            <p>Ing. Gabriela Briones Giler</p>
            <p>COORDINADORA GENERAL</p>
            <p>FUNDACIÓN DE AYUDA MUTUA SAN JORGE</p>
        </div>

        <!-- Pie de Página -->
        <div class="footer">
            <p>Contacto: famsanjorge@gmail.com</p>
        </div>
    </div> 
</body>
</html>