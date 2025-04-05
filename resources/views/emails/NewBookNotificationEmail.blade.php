<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>New Book Notification</title>
</head>

<body>
    <h1>A new book has been added!</h1>
    {{-- <h3>Dear {{ $user->name }}</h3> --}}
    <p>New Book Added</p>
    <p>We're excited to let you know that a new book has been added to our collection! 🎉</p>

     {{-- <p>Title: {{ $book->title }}</p> --}}
    {{-- <p>Author: {{ $book->author ? $book->author->name : 'Unknown Author'}}</p> --}}
     {{-- <p>Book Overview: {{ $book->categories->isNotEmpty() ? $book->categories->first()->name : 'No Category' }}</p> --}}

    <p>You can now visit our website to discover the book and explore more great titles!</p>

    <p>We hope you find this book engaging and enjoyable! <br> Best regards,</p>

    <p>
        The Team <br>
        Dubai <br>
        Literary Oasis Library<br>
        <a href="tel:+9630936745054">+963 0936745054</a>
    </p>

    <p>Feel free to customize the content to suit your specific needs, such as adding more details about the book or
        adjusting the tone to reflect your library's identity. If you need further assistance, just let me know!
    </p>

</body>

</html>
