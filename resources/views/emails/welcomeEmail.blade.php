<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to Literary Oasis Library</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            padding: 20px;
        }

        hr {
            border: 1px solid #000000;
        }
        a {
            color: #96b0f7f5;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <h3>Dear {{ $user->name }}</h3>
    <p>Welcome to Literary Oasis Library!</p>
    <hr>
    <p>We're thrilled to have you join our community of readers and learners.
        As a member of our library, you now have access to thousands of resources,
        including books, e-books, and audiobooks, all at your fingertips.
    </p>
    <br>
    <h2>Here are some steps to get you started:</h2>
    <dl>
        <dt>Log In to Your Account:</dt>
        <dd>- To access your account, please visit our website [insert link to website].</dd>
        <dd>- Click on the “Log In” button located at the top right corner.</dd>
        <dd>- Enter your registered email address and the password you created during registration.</dd>

        <dt>Explore Our Resources:</dt>
        <dd>- Once logged in, take some time to explore our vast collection of materials.</dd>
        <dd>- Check out the "New Arrivals" section for the latest additions.</dd>
        <dd>- Join our community events and workshops by visiting the "Events" page.</dd>

        <dt>Need Help?:</dt>
        <dd>- If you have any questions or need assistance, feel free to reach out to our support team at [support
            email] or visit our Help Center [insert link to Help Center].</dd>
    </dl>
    <p>Thank you for choosing Literary Oasis Library. We look forward to supporting your learning and reading journey! <br>Happy
        Reading! <br> Best Regards,</p>
    <p>
        The Team <br>
        Dubai <br>
        Literary Oasis Library<br>
        <a href="tel:+9630936745054">+963 0936745054</a>
    </p>


</body>

</html>
