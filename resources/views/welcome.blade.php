<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JEM</title>
    <link rel="icon" type="image/x-icon" href="images/G!.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Gradient Background -->
    <!-- <div class="blob w-full h-full rounded-[999px] absolute top-0 right-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-r from-indigo-200 via-purple-200 to-pink-200"></div>
    <div class="blob w-[1000px] h-[1000px] rounded-[999px] absolute bottom-0 left-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-r from-red-200 via-gray-100 to-blue-100"></div>
    <div class="blob w-[600px] h-[600px] rounded-[999px] absolute bottom-0 left-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-r from-slate-100 via-teal-100 to-blue-100"></div>
    <div class="blob w-[300px] h-[300px] rounded-[999px] absolute bottom-[10px] left-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-r from-green-200 via-cyan-200 to-Fuchsia-300"></div> -->
    <div class="blob w-full h-full absolute top-0 right-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-b from-pink-300 via-slate-50 to-pink-300"></div>
    <div class="blob w-full h-full absolute top-0 right-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-b from-pink-300 via-slate-50 to-pink-300"></div>
    <div class="blob w-full h-full absolute top-0 right-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-b from-pink-300 via-slate-50 to-pink-300"></div>
    <div class="blob w-full h-full absolute top-0 right-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-b from-pink-300 via-slate-50 to-pink-300"></div>

    <!-- Header -->
    <header class="fixed top-0 w-full bg-white/80 backdrop-blur-md shadow-md z-50 py-4 px-6 flex justify-between items-center">
    <!-- <div class="blob w-full h-full rounded-[999px] absolute top-0 right-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-r from-indigo-200 via-purple-200 to-pink-200"></div>
    <div class="blob w-[1000px] h-[1000px] rounded-[999px] absolute bottom-0 left-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-r from-red-200 via-gray-100 to-blue-100"></div>
    <div class="blob w-[600px] h-[600px] rounded-[999px] absolute bottom-0 left-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-r from-slate-100 via-teal-100 to-blue-100"></div>
    <div class="blob w-[300px] h-[300px] rounded-[999px] absolute bottom-[10px] left-0 -z-10 blur-3xl bg-opacity-60 bg-gradient-to-r from-green-200 via-cyan-200 to-Fuchsia-300"></div> -->

        <div class="flex items-center space-x-4">
        <!-- <svg width="45px" height="45px" viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--fxemoji" preserveAspectRatio="xMidYMid meet" fill="#FF1493"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path fill="#d100ca" d="M26.1 49.8l9.6 21.8c0 .1-.1.1-.1.1L16.9 49.8h9.2zm-8.8-20.1H.1c0 .7 1.1 1.6 1.1 1.6l15.7 18.5l6.3-6.7l-5.9-13.4z"></path><path fill="#ef75ff" d="M71.5 30.5c-.5.5-1.3.5-1.8 0c-.2-.2-.4-.6-.4-.9H54.6l-9.2-10L55 9.5l16.3 19.1s0 .1.1.1h.1c.5.6.5 1.4 0 1.8zm-35.6-.8l9.4-10l-9.4-10.2l-9.4 10.2l9.4 10z"></path><path fill="#f05bfb" d="M36.3 71.7c-.1.1-.2 0-.1-.1l9.6-21.8H55L36.3 71.7zm12.4-28.6l6.3 6.7l16.4-19.2s.4-.6.4-.9H54.6l-5.9 13.4z"></path><path fill="#e629ff" d="M26.5 19.7l-9.2 10h18.6z"></path><path fill="#fab3ff" d="M45.4 19.7l-9.5 10h18.7z"></path><path fill="#ff00dd" d="M26.5 19.7l-9.2 10H.1s.1-.6.5-1l15.8-18.5c.1-.1.2-.2.3-.2c.2-.3.5-.4.9-.4c.7 0 1.2.6 1.2 1.2c0 .2-.1.4-.2.6l7.9 8.3zm9.4 10l12.8 13.5l5.9-13.5H35.9zm-18.6 0l5.9 13.5L36 29.7H17.3zm8.8 20.1l9.7 22c.1.1.2.1.3 0l9.7-22H26.1zm22.6-6.7l-2.9 6.7H55l-6.3-6.7z"></path><path fill="#f05bfb" d="M45.4 19.7L35.9 9.5H55l-9.6 10.2zM35.9 9.5H18c-.4 0-1.2 0-.9.3l9.4 9.9l9.4-10.2z"></path><path fill="#a8009a" d="M23.2 43.1l-6.3 6.7h9.2z"></path><path fill="#d100ca" d="M23.2 43.1l2.9 6.7h19.7l2.9-6.7l-12.8-13.4z"></path><path fill="#FFF" d="M52.7 29.9c2.7.7 4.8 2.8 5.5 5.5c.1.3.4.3.5 0c.7-2.7 2.8-4.8 5.5-5.5c.3-.1.3-.4 0-.5c-2.7-.7-4.8-2.8-5.5-5.5c-.1-.3-.4-.3-.5 0c-.7 2.7-2.8 4.8-5.5 5.5c-.3.1-.3.4 0 .5z"></path><path fill="#fab3ff" d="M42.6 3.6c1.4.4 2.5 1.5 2.9 2.9c0 .1.2.1.2 0c.4-1.4 1.5-2.5 2.9-2.9c.1 0 .1-.2 0-.2c-1.4-.4-2.5-1.5-2.9-2.9c0-.1-.2-.1-.2 0c-.4 1.4-1.5 2.5-2.9 2.9c-.1 0-.1.2 0 .2z"></path></g></svg>
        -->  <a href="index.php" class="flex items-center space-x-4">
                <img src="storage/images/jemlogo.png" class="w-48 h-auto" alt="" />
                </a>
        <!-- <h1 class="text-lg font-semibold text-gray-700">JEM</h1>  -->
        </div>
        
        <nav class="flex space-x-4">
            @auth
                <a href="{{ url('/app') }}" class="text-gray-800 hover:text-pink-500 transition">Dashboard</a>
                <a href="{{ url('/admin') }}" class="text-gray-800 hover:text-pink-500 transition">Admin Dashboard</a>
            @else
                <a href="{{ url('/app/edit-profile') }}" class="text-gray-800 hover:text-pink-500 transition">Log in</a>
                <a href="{{ url('/admin') }}" class="text-gray-800 hover:text-pink-500 transition">Log in as Admin</a>
            @endauth
        </nav>
    </header>

    <!-- Main Content -->
    <main class="pt-36 pb-6">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-20">
                <!-- Carousel Section -->
                <div class="lg:w-3/4">
                    <div class="carousel relative">
                        <div class="list overflow-hidden rounded-2xl shadow-xl">
                            <?php
                            $events = [
                                ["title" => "Event 1", "author" => "John Doe", "description" => "Description for event 1", "image" => "event1.jpg"],
                                ["title" => "Event 2", "author" => "Jane Smith", "description" => "Description for event 2", "image" => "event2.jpg"],
                                ["title" => "Event 3", "author" => "Alice Brown", "description" => "Description for event 3", "image" => "event3.jpg"]
                            ];
                            
                            foreach ($events as $index => $event) {
                                $imagePath = 'uploads/' . ($event['image'] ? $event['image'] : 'default-event.jpg');
                                ?>
                                <div class="item relative <?= $index === 0 ? '' : 'hidden'; ?>" data-index="<?= $index ?>">
                                    <img src="<?= htmlspecialchars($imagePath); ?>" 
                                         alt="<?= htmlspecialchars($event['title']); ?>" 
                                         class="w-full h-[400px] lg:h-[550px] object-cover rounded-2xl" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end p-6 text-white">
                                        <div class="max-w-2xl">
                                            <div class="text-blue-400 font-medium mb-2"> <?= htmlspecialchars($event['author']); ?> </div>
                                            <h2 class="text-2xl lg:text-4xl font-bold mb-2 line-clamp-2"> <?= htmlspecialchars($event['title']); ?> </h2>
                                            <p class="text-sm lg:text-lg mb-4 line-clamp-2"> <?= htmlspecialchars($event['description']); ?> </p>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                
                <!-- Thumbnails Section -->
                <div class="lg:w-1/4">
                    <div class="sticky top-24">
                        <h3 class="text-lg font-semibold mb-4">Event Preview</h3>
                        <div class="relative overflow-hidden h-[480px] scrollbar-hide">
                            <div class="thumbnails-container flex flex-col space-y-4">
                                <?php 
                                foreach ($events as $index => $event) {
                                    $thumbnailPath = 'uploads/' . ($event['image'] ? $event['image'] : 'default-event.jpg');
                                    ?>
                                    <div class="thumbnail-item cursor-pointer hover:scale-105 transition-all duration-300" data-index="<?= $index ?>">
                                        <img src="<?= htmlspecialchars($thumbnailPath); ?>" 
                                            alt="<?= htmlspecialchars($event['title']); ?>" 
                                            class="w-full h-32 object-cover rounded-lg border-2 border-gray-300" />
                                        <p class="mt-1 text-sm font-medium text-gray-700 truncate px-2"> <?= htmlspecialchars($event['title']); ?> </p>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
