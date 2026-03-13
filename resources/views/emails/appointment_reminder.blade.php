<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FurryTales - Appointment Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f9fafb; padding:20px;">
    
    <div style="
        max-width:500px;
        margin:auto;
        background:#ffffff;
        padding:24px;
        border-radius:8px;
    ">

        <h2 style="color:#5a2c2c;">📅 Appointment Reminder</h2>

        <p>Hello {{ $appointment->customer->user->name ?? 'Customer' }},</p>

        <p>
            This is a friendly reminder that you have an upcoming appointment with 
            <strong>FurryTales</strong>.
        </p>

        <div style="
            margin:20px 0;
            padding:16px;
            background:#fdf6f5;
            border-radius:6px;
        ">
            <p style="margin:0; font-size:14px; color:#555;">
                Appointment Details
            </p>

            <h3 style="margin:10px 0 5px; color:#a95c68;">
                {{ $appointment->AppointmentDateTime->format('d M Y, h:i A') }}
            </h3>

            <p style="margin:0; font-size:14px; color:#555;">
                🐾 Pet: {{ $appointment->pet->PetName ?? 'N/A' }}
            </p>

            <p style="margin:4px 0 0; font-size:14px; color:#555;">
                📍 Method: {{ $appointment->Method }}
            </p>

            <p style="margin:4px 0 0; font-size:14px; color:#555;">
                🗺️ Location: {{ $appointment->pet->outlet->AddressLine1 ?? 'N/A' }}
            </p>

            <p style="margin:4px 0 0; font-size:14px; color:#555;">
                📞 Contact: {{ $appointment->CustomerPhone }}
            </p>
        </div>

        <p style="font-size:14px; color:#555;">
            Please ensure you arrive on time. If you need to cancel or reschedule,
            kindly contact us in advance.
        </p>

        <p style="margin-top:24px;">
            Regards,<br>
            <strong>FurryTales Team</strong>
        </p>

    </div>

</body>
</html>