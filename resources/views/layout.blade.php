<!DOCTYPE html>  
<html lang="ar">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>@yield('title')</title>  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">  
    <link rel="stylesheet" href="/css/app.css"> <!-- إشارة إلى أي CSS مخصص لديك -->  
</head>  
<body>  
    <header>  
        <h1>@yield('title')</h1>  
    </header>  

    <main>  
        @yield('content')  
        @yield('styles')  
        @yield('scripts')  
    </main>  

    <footer>  
        <p> Good Job </p>  
    </footer>  
</body>  
</html>  