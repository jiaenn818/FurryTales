<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FurryTales - Order Delivered</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f9fafb; padding:20px;">
    <div style="
        max-width:500px;
        margin:auto;
        background:#ffffff;
        padding:24px;
        border-radius:8px;
    ">

        <h2 style="color:#5a2c2c;">✅ Order Delivered</h2>

        <p>Hello {{ $purchase->CustomerID }},</p>

        <p>
            Your order
            <strong style="color:#a95c68;">
                #{{ $purchase->PurchaseID }}
            </strong>
            has been successfully delivered.
        </p>

        <div style="
            margin:20px 0;
            padding:16px;
            background:#fdf6f5;
            border-radius:6px;
            text-align:center;
        ">
            <p style="margin:0; font-size:14px; color:#555;">
                Delivered at
            </p>
            <h3 style="margin:8px 0 0; color:#a95c68;">
                {{ \Carbon\Carbon::parse($purchase->DeliveredDate)->format('d M Y, h:i A') }}
            </h3>
        </div>

        <p>
            Thank you for shopping with <strong>FurryTales</strong> 🐾  
            We hope you enjoy your purchase!
        </p>

        <p style="margin-top:24px;">
            Regards,<br>
            <strong>FurryTales Team</strong>
        </p>

    </div>
</body>
</html>