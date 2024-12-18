<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culinary Experience</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <link href="../src/output.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-neutral-50 to-neutral-100 text-neutral-800 min-h-screen">
    <div id="app" class="container mx-auto">
        <!-- navigation -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md shadow-lg z-50">
            <div class="flex justify-around py-3 max-w-xl mx-auto">
                <button onclick="navigateTo('home')" class="nav-item" data-section="home">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-xs">Home</span>
                </button>
                <button onclick="navigateTo('menu')" class="nav-item" data-section="menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-xs">Menus</span>
                </button>
                <button onclick="navigateTo('booking')" class="nav-item" data-section="booking">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs">Book</span>
                </button>
                <button onclick="navigateTo('login')" class="nav-item" data-section="login">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span class="text-xs">Login</span>
                </button>
            </div>
        </nav>

        <!-- content section -->
        <div id="content" class="pt-16 px-4">
            <!-- home section -->
            <section id="home-section" class="text-center space-y-8">
                <div class="max-w-2xl mx-auto">
                    <h1 class="text-4xl md:text-6xl font-light text-neutral-900 mb-4">
                        Culinary Artistry Unveiled
                    </h1>
                    <p class="text-xl text-neutral-600 mb-8">
                        Experience extraordinary cuisine crafted by a world-renowned chef,
                        bringing gourmet dining directly to your home.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <button onclick="navigateTo('booking')" class="bg-emerald-600 text-white px-6 py-3 rounded-full hover:bg-emerald-700 transition">
                            Book an Experience
                        </button>
                        <button onclick="navigateTo('menu')" class="border border-emerald-600 text-emerald-600 px-6 py-3 rounded-full hover:bg-emerald-50 transition">
                            View Menus
                        </button>
                    </div>
                </div>
                <div class="grid md:grid-cols-3 gap-6 mt-12">
                    <div class="bg-white rounded-xl shadow-md p-6 transform transition hover:scale-105">
                        <h3 class="text-2xl font-semibold text-emerald-600 mb-2">Exclusive Menus</h3>
                        <p class="text-neutral-600">Curated culinary journeys</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 transform transition hover:scale-105">
                        <h3 class="text-2xl font-semibold text-emerald-600 mb-2">Private Dining</h3>
                        <p class="text-neutral-600">Personalized chef experiences</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 transform transition hover:scale-105">
                        <h3 class="text-2xl font-semibold text-emerald-600 mb-2">Gourmet Ingredients</h3>
                        <p class="text-neutral-600">Finest seasonal selections</p>
                    </div>
                </div>
            </section>

            <!-- menu section -->
            <section id="menu-section" class="hidden space-y-8">
                <h2 class="text-4xl text-center font-light text-neutral-900 mb-8">
                    Our Exquisite Menus
                </h2>
                <div class="grid md:grid-cols-2 gap-6" id="menu-container">
                    <!-- Menus will be dynamically populated -->
                </div>
            </section>

            <!-- booking Section -->
            <section id="booking-section" class="hidden space-y-8 max-w-xl mx-auto">
                <h2 class="text-4xl text-center font-light text-neutral-900 mb-8">
                    Book Your Culinary Experience
                </h2>
                <div class="bg-white rounded-xl shadow-md p-8 space-y-6">
                    <div>
                        <label class="block text-neutral-600 mb-2">Select Date</label>
                        <input
                            type="date"
                            id="booking-date"
                            class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-neutral-600 mb-2">Number of Guests</label>
                        <div class="flex items-center space-x-4">
                            <button
                                onclick="adjustGuests(-1)"
                                class="bg-neutral-100 px-4 py-2 rounded-lg">
                                -
                            </button>
                            <span id="guests-count" class="text-xl">2</span>
                            <button
                                onclick="adjustGuests(1)"
                                class="bg-neutral-100 px-4 py-2 rounded-lg">
                                +
                            </button>
                        </div>
                    </div>
                    <button
                        onclick="checkAvailability()"
                        class="w-full bg-emerald-600 text-white py-3 rounded-full hover:bg-emerald-700 transition">
                        Check Availability
                    </button>
                </div>
            </section>

            <!-- login section -->
            <section id="login-section" class="hidden space-y-8 max-w-md mx-auto">
                <h2 id="login-title" class="text-4xl text-center font-light text-neutral-900 mb-8">
                    Welcome Back
                </h2>
                <form id="login-form" method="POST" action="login.php" class="bg-white rounded-xl shadow-md p-8 space-y-6">
        <input type="email" name="email" placeholder="Email Address" class="w-full px-4 py-3 border border-neutral-300 rounded-lg" required>
        <input type="password" name="password" placeholder="Password" class="w-full px-4 py-3 border border-neutral-300 rounded-lg" required>
        <input type="submit" id="login-button" value="Login" class="w-full bg-emerald-600 text-white py-3 rounded-full hover:bg-emerald-700 transition">
        <p id="auth-toggle" onclick="toggleAuthMode()" class="text-center text-neutral-600 cursor-pointer hover:text-emerald-600">Need an account? Sign Up</p>
    </form>

    <!-- signup form -->
    <form id="signup-form" method="POST" action="signup.php" class="bg-white rounded-xl shadow-md p-8 space-y-6 hidden">
        <input type="text" name="full_name" placeholder="Full Name" class="w-full px-4 py-3 border border-neutral-300 rounded-lg" required>
        <input type="email" name="email" placeholder="Email Address" class="w-full px-4 py-3 border border-neutral-300 rounded-lg" required>
        <input type="password" name="password" placeholder="Password" class="w-full px-4 py-3 border border-neutral-300 rounded-lg" required>
        <input type="submit" id="signup-button" value="Sign Up" class="w-full bg-emerald-600 text-white py-3 rounded-full hover:bg-emerald-700 transition">
        <p id="auth-toggle" onclick="toggleAuthMode()" class="text-center text-neutral-600 cursor-pointer hover:text-emerald-600">Already have an account? Login</p>
    </form>

            </section>
        </div>

        <!-- footer -->
        <footer class="bg-neutral-900 text-white py-12 mt-16">
            <div class="container mx-auto px-4 text-center">
                <h3 class="text-3xl font-light mb-4">Culinary Experience</h3>
                <p class="text-neutral-300 mb-6">
                    Bringing world-class gastronomy to your home
                </p>
                <div class="flex justify-center space-x-6">
                    <a href="#" class="text-neutral-400 hover:text-white transition">About</a>
                    <a href="#" class="text-neutral-400 hover:text-white transition">Menus</a>
                    <a href="#" class="text-neutral-400 hover:text-white transition">Booking</a>
                    <a href="#" class="text-neutral-400 hover:text-white transition">Contact</a>
                </div>
                <div class="mt-8 text-neutral-500">
                    © 2024 Culinary Experience. All Rights Reserved.
                </div>
            </div>
        </footer>
    </div>

    <script>
        // navigation 
        function navigateTo(section) {
            // hide all sections
            ['home', 'menu', 'booking', 'login'].forEach(s => {
                document.getElementById(`${s}-section`).classList.add('hidden');
                document.querySelector(`[data-section="${s}"]`).classList.remove('text-emerald-600', 'bg-emerald-50');
            });

            // show selected section
            document.getElementById(`${section}-section`).classList.remove('hidden');
            document.querySelector(`[data-section="${section}"]`).classList.add('text-emerald-600', 'bg-emerald-50');

            // menu secetion
            if (section === 'menu') {
                populateMenus();
            }
        }

        // menu Population
        function populateMenus() {
            const menuContainer = document.getElementById('menu-container');
            const menus = [{
                    name: 'Seasonal Tasting Menu',
                    description: 'A journey through local, seasonal ingredients',
                    dishes: ['Amuse-bouche', 'Seasonal Appetizer', 'Main Course', 'Dessert']
                },
                {
                    name: 'Vegetarian Exploration',
                    description: 'Innovative plant-based culinary art',
                    dishes: ['Garden Starter', 'Forest Mushroom Dish', 'Root Vegetable Main', 'Fruit Dessert']
                }
            ];

            menuContainer.innerHTML = menus.map(menu => `
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <h3 class="text-2xl font-semibold text-emerald-600 mb-2">${menu.name}</h3>
                    <p class="text-neutral-600 mb-4">${menu.description}</p>
                    <ul class="space-y-2 text-neutral-700">
                        ${menu.dishes.map(dish => `
                            <li class="flex items-center">
                                <span class="mr-2 text-emerald-600">•</span>
                                ${dish}
                            </li>
                        `).join('')}
                    </ul>
                </div>
            `).join('');
        }


        function adjustGuests(change) {
            const guestsCount = document.getElementById('guests-count');
            let currentGuests = parseInt(guestsCount.textContent);
            currentGuests = Math.max(1, currentGuests + change);
            guestsCount.textContent = currentGuests;
        }


        function checkAvailability() {
            const date = document.getElementById('booking-date').value;
            const guests = document.getElementById('guests-count').textContent;

            if (!date) {
                alert('Please select a date');
                return;
            }

            alert(`Checking availability for ${guests} guests on ${date}`);
        }
        let isLoginMode = true;

        function toggleAuthMode() {
            isLoginMode = !isLoginMode;
            const loginTitle = document.getElementById('login-title');
            const loginButton = document.getElementById('login-button');
            const authToggle = document.getElementById('auth-toggle');
            // const signupNameContainer = document.getElementById('signup-name');
            const signupForm = document.querySelector('#signup-form');
            const loginForm = document.querySelector('#login-form');

            if (isLoginMode) {
                loginTitle.textContent = 'Welcome Back';
                loginButton.textContent = 'Login';
                authToggle.textContent = 'Need an account? Sign Up';
                signupForm.classList.add('hidden');
                loginForm.classList.remove('hidden');

            } else {
                loginTitle.textContent = 'Create Account';
                loginButton.textContent = 'Sign Up';
                authToggle.textContent = 'Already have an account? Login';
                signupForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
            }
        }


        function handleAuth() {
            const emailInput = document.querySelector('input[type="email"]');
            const passwordInput = document.querySelector('input[type="password"]');
            const nameInput = document.querySelector('input[type="text"]');


            if (!emailInput.value || !passwordInput.value) {
                alert('Please fill in all required fields');
                return;
            }

            if (isLoginMode) {

                console.log('Logging in with:', emailInput.value);
                alert('Login functionality to be implemented');
            } else {

                if (!nameInput || !nameInput.value) {
                    alert('Please provide your full name');
                    return;
                }
                console.log('Signing up:', nameInput.value, emailInput.value);
                alert('Signup functionality to be implemented');
            }
        }


        document.addEventListener('DOMContentLoaded', () => {
            navigateTo('home');
        });
    </script>
</body>

</html>