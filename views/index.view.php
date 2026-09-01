<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Basic MVC</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-gray-100">

    <nav class="bg-gray-900 text-white">
        <div class="max-w-6xl mx-auto px-6 py-4">
            <h1 class="text-xl font-bold">
                Basic MVC
            </h1>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-20">

        <div class="text-center">

            <h2 class="text-4xl font-bold text-gray-900">
                Welcome to Basic MVC
            </h2>

            <p class="mt-4 text-lg text-gray-600">
                A simple PHP MVC structure.
            </p>

            <a href="#"
                class="inline-block mt-8 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Get Started
            </a>

        </div>

        <div class="grid md:grid-cols-3 gap-6 mt-16">

            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-xl font-semibold">
                    Simple
                </h3>

                <p class="mt-2 text-gray-600">
                    Keep your PHP application clean and easy to understand.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-xl font-semibold">
                    MVC
                </h3>

                <p class="mt-2 text-gray-600">
                    Separate controllers, models, and views.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-xl font-semibold">
                    Lightweight
                </h3>

                <p class="mt-2 text-gray-600">
                    No framework or unnecessary complexity.
                </p>
            </div>

        </div>

    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6">
        Basic PHP MVC
    </footer>

</body>

</html>
```