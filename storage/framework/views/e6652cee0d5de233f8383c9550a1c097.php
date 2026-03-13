<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FurryTales - OTP Verification</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f9fafb; padding:20px;">
    <div style="
        max-width:500px;
        margin:auto;
        background:#ffffff;
        padding:24px;
        border-radius:8px;
    ">

        <h2 style="color:#5a2c2c;">🔐 Verify Your Email</h2>

        <p>Hello,</p>

        <p>
            You (or an admin) requested to reset the password on <strong>FurryTales</strong>.
            Please use the verification code below to continue:
        </p>

        <div style="
            margin:20px 0;
            padding:16px;
            background:#fdf6f5;
            border-radius:6px;
            text-align:center;
        ">
            <p style="margin:0; font-size:14px; color:#555;">
                Your 6-digit OTP
            </p>
            <h2 style="margin:10px 0 0; color:#a95c68; letter-spacing:4px;">
                <?php echo e($otp); ?>

            </h2>
        </div>

        <p style="font-size:14px; color:#555;">
            This code will expire in 5 minutes.  
            If you did not request this, please ignore this email.
        </p>

        <p style="margin-top:24px;">
            Regards,<br>
            <strong>FurryTales Team</strong>
        </p>

    </div>
</body>
</html>
<?php /**PATH C:\Users\User\finalyear\resources\views/emails/customer_otp.blade.php ENDPATH**/ ?>